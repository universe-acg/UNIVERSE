<?php
namespace App\Controller\Front;
use App\Controller\BaseController;
use App\Entity\{UniverseChatUser,UniverseMessage,User};
use App\Service\ChatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\HttpFoundation\{Request,Response,JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
final class ChatController extends BaseController
{
    public function __construct(private ChatService $chat,private EntityManagerInterface $em, #[Autowire(service:'limiter.chat_messages')] private RateLimiterFactory $messageLimiter) {}
    #[Route('/chat',name:'chat_index',methods:['GET'])]
    public function index(): Response { return $this->display(null); }
    #[Route('/chat/create',name:'chat_create',methods:['GET','POST'],priority:10)]
    public function createChat(Request $request): Response
    {
        $params=$request->isMethod('POST')?$request->getPayload():$request->query;
        $username=trim((string)$params->get('username',''));
        $recipient=$username!=='' ? $this->em->getRepository(User::class)->findOneBy(['usernameCanonical'=>mb_strtolower($username)]) : $this->em->find(User::class,$params->getInt('userId'));
        if (!$recipient || !$recipient->isEnabled() || $recipient->getDeletedAt() || $recipient->getId()===$this->getUser()->getId()) {
            if (!$request->isMethod('POST')) return $this->display(null);
            $this->checkToken($request); $this->addFlash('error','Enter the username of another active user.'); return $this->redirectToRoute('chat_index');
        }
        if (!$request->isMethod('POST')) return $this->display(null,$recipient);
        $this->checkToken($request); $room=$this->chat->direct($this->getUser(),$recipient);
        return $this->redirectToRoute('chat_show',['chatId'=>$room->getId()]);
    }
    #[Route('/chat/{chatId}',name:'chat_show',requirements:['chatId'=>'\d+'],methods:['GET'])]
    public function showChat(int $chatId): Response { return $this->display($chatId); }
    private function display(?int $id,?User $recipient=null): Response
    {
        $user=$this->getUser(); $rooms=$this->chat->rooms($user); $room=$id?$this->chat->room($id,$user):($rooms[0]??null); $contacts=[];
        foreach ($rooms as $item) {
            $other=null; foreach ($item->getParticipants() as $member) if ($member->getId()!==$user->getId()) { $other=$member; break; }
            $last=$this->em->getRepository(UniverseMessage::class)->findOneBy(['universeChat'=>$item],['id'=>'DESC']);
            $contacts[]=['id'=>$item->getId(),'name'=>$item->getDirectKey()?$other?->getCrossRoleName():$item->getTitle(),'datecreated'=>$last?->getSentAt(),
                'unread'=>$this->chat->unread($item,$user),'muted'=>$this->chat->membership($item,$user)->isMuted(),'profile_image'=>$other?->getLogoPlaceholder()??'',
                'last_message'=>$last?->getContent()??'No messages yet.','isorganizer'=>$other?->hasRole('ROLE_ORGANIZER')??false];
        }
        $messages=$room?$this->chat->messages($room):[];
        return $this->render('Dashboard/Shared/Chat/index.html.twig',['chatRooms'=>$rooms,'contacts'=>$contacts,'msgs'=>array_map(fn($m)=>$this->chat->serialize($m,$user),$messages),
            'chatRoom'=>$room,'chatRecipient'=>$recipient,'chatMuted'=>$room?$this->chat->membership($room,$user)->isMuted():false,'lastMessageId'=>$messages?end($messages)->getId():0,
            'firstMessageId'=>$messages?$messages[0]->getId():0,'hasOlderMessages'=>count($messages)===200,'chatToken'=>$room?$this->chat->token($user,$room->getId()):null]);
    }
    #[Route('/chat/{chatId}/messages',name:'chat_messages',requirements:['chatId'=>'\d+'],methods:['GET','POST'])]
    public function messages(int $chatId,Request $request): JsonResponse
    {
        $user=$this->getUser(); $room=$this->chat->room($chatId,$user);
        if ($request->isMethod('POST')) {
            $this->checkToken($request); $payload=$request->getPayload();
            if (!$this->messageLimiter->create((string)$user->getId())->consume(1)->isAccepted()) return $this->json(['error'=>'You are sending messages too quickly. Please wait a moment.'],429,['Retry-After'=>'60']);
            try { $message=$this->chat->send($room,$user,(string)$payload->get('message',''),$payload->get('client_id')); }
            catch (\InvalidArgumentException $error) { return $this->json(['error'=>$error->getMessage()],422); }
            return $this->json($this->chat->serialize($message,$user),201);
        }
        $messages=$this->chat->messages($room,max(0,$request->query->getInt('after')),max(0,$request->query->getInt('before')));
        return $this->json(array_map(fn($m)=>$this->chat->serialize($m,$user),$messages));
    }
    #[Route('/chat/{chatId}/read',name:'chat_read',requirements:['chatId'=>'\d+'],methods:['POST'])]
    public function read(int $chatId,Request $request): JsonResponse
    {
        $this->checkToken($request); $room=$this->chat->room($chatId,$this->getUser()); $this->chat->markRead($room,$this->getUser(),$request->getPayload()->getInt('messageId'));
        return $this->json(['unread'=>$this->chat->unread($room,$this->getUser())]);
    }
    #[Route('/chat/{chatId}/add-user',name:'chat_add_user',methods:['POST'])]
    public function addUserToChat(int $chatId,Request $request): Response
    {
        $this->checkToken($request); $room=$this->chat->room($chatId,$this->getUser()); $this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');
        $user=$this->em->find(User::class,$request->getPayload()->getInt('userId'));
        if (!$user || !$user->isEnabled() || $user->getDeletedAt()) throw $this->createNotFoundException();
        $this->chat->add($room,$user); return $this->redirectToRoute('chat_show',['chatId'=>$chatId]);
    }
    #[Route('/chat/{chatId}/remove-user/{userId}',name:'chat_remove_user',methods:['POST'])]
    public function removeUserFromChat(int $chatId,int $userId,Request $request): Response
    {
        $this->checkToken($request); $room=$this->chat->room($chatId,$this->getUser());
        if ($userId!==$this->getUser()->getId()) $this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');
        $user=$this->em->find(User::class,$userId);
        if ($user) {
            $room->getParticipants()->removeElement($user); $room->setDirectKey(null);
            $member=$this->em->find(UniverseChatUser::class,['universeChat'=>$room,'user'=>$user]); if ($member) $this->em->remove($member); $this->em->flush();
        }
        return $this->redirectToRoute('chat_index');
    }
    #[Route('/chat/{chatId}/mute-chat',name:'chat_mute',methods:['POST'])]
    public function muteChat(int $chatId,Request $request): Response
    {
        $this->checkToken($request); $room=$this->chat->room($chatId,$this->getUser()); $member=$this->chat->membership($room,$this->getUser());
        $member->setMuted($request->getPayload()->getBoolean('muted',!$member->isMuted())); $this->em->flush(); return $this->redirectToRoute('chat_show',['chatId'=>$chatId]);
    }
    private function checkToken(Request $request): void
    {
        if (!$this->isCsrfTokenValid('action',$request->headers->get('X-CSRF-Token')??$request->getPayload()->get('_action_token'))) throw $this->createAccessDeniedException();
    }
}
