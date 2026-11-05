<?php
namespace App\Controller\Front;
use App\Controller\BaseController;
use App\Entity\{UniverseChatEntity,UniverseChatUser,User};
use App\Service\ChatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request,Response,JsonResponse};
use Symfony\Component\Routing\Attribute\Route;
final class ChatController extends BaseController
{
    public function __construct(private ChatService $chat,private EntityManagerInterface $em) {}
    #[Route('/chat',name:'chat_index',methods:['GET'])]
    public function index(): Response { return $this->display(null); }
    #[Route('/chat/create',name:'chat_create',methods:['GET','POST'],priority:10)]
    public function createChat(Request $request): Response {
        if(!$request->isMethod('POST'))return $this->redirectToRoute('chat_index');$this->checkToken($request);
        $recipient=$this->em->find(User::class,$request->request->getInt('userId'));
        if(!$recipient||!$recipient->isEnabled()||$recipient===$this->getUser())throw $this->createNotFoundException();
        $room=new UniverseChatEntity();$room->setTitle(mb_substr((string)$request->request->get('title','Conversation'),0,255));$room->setParticipants(new \Doctrine\Common\Collections\ArrayCollection());$this->em->persist($room);$this->em->flush();
        $this->chat->add($room,$this->getUser());$this->chat->add($room,$recipient);
        return $this->redirectToRoute('chat_show',['chatId'=>$room->getId()]);
    }
    #[Route('/chat/{chatId}',name:'chat_show',requirements:['chatId'=>'\d+'],methods:['GET'])]
    public function showChat(int $chatId): Response { return $this->display($chatId); }
    private function display(?int $id): Response {
        $user=$this->getUser();$rooms=$this->chat->rooms($user);$room=$id?$this->chat->room($id,$user):($rooms[0]??null);$contacts=[];
        foreach($rooms as $item){$other=null;foreach($item->getParticipants() as $member)if($member!==$user){$other=$member;break;}$last=$this->em->getRepository(\App\Entity\UniverseMessage::class)->findOneBy(['universeChat'=>$item],['id'=>'DESC']);
            $contacts[]=['id'=>$item->getId(),'name'=>$other?->getCrossRoleName() ?: $item->getTitle(),'datecreated'=>$last?->getSentAt(),'unread'=>false,'profile_image'=>$other?->getLogoPlaceholder()??'','last_message'=>$last?->getContent()??'','isorganizer'=>$other?->hasRole('ROLE_ORGANIZER')??false];
        }
        $messages=$room?$this->chat->messages($room):[];
        return $this->render('Dashboard/Shared/Chat/index.html.twig',['chatRooms'=>$rooms,'contacts'=>$contacts,'msgs'=>array_map(fn($message)=>$this->chat->serialize($message,$user),$messages),'chatRoom'=>$room,'lastMessageId'=>$messages?end($messages)->getId():0,'chatToken'=>$room?$this->chat->token($user,$room->getId()):null]);
    }
    #[Route('/chat/{chatId}/messages',name:'chat_messages',methods:['GET','POST'])]
    public function messages(int $chatId,Request $request): JsonResponse {
        $user=$this->getUser();$room=$this->chat->room($chatId,$user);
        if($request->isMethod('POST')){$this->checkToken($request);try{$message=$this->chat->send($room,$user,(string)$request->request->get('message'));}catch(\InvalidArgumentException $error){return $this->json(['error'=>$error->getMessage()],422);}return $this->json($this->chat->serialize($message,$user),201);}
        return $this->json(array_map(fn($message)=>$this->chat->serialize($message,$user),$this->chat->messages($room,max(0,$request->query->getInt('after')))));
    }
    #[Route('/chat/{chatId}/add-user',name:'chat_add_user', methods: ["POST"])]
    public function addUserToChat(int $chatId,Request $request): Response {
        $room=$this->chat->room($chatId,$this->getUser());$this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');$user=$this->em->find(User::class,$request->request->getInt('userId'));if(!$user)throw $this->createNotFoundException();$this->chat->add($room,$user);return $this->redirectToRoute('chat_show',['chatId'=>$chatId]);
    }
    #[Route('/chat/{chatId}/remove-user/{userId}',name:'chat_remove_user', methods: ["POST"])]
    public function removeUserFromChat(int $chatId,int $userId): Response {
        $room=$this->chat->room($chatId,$this->getUser());if($userId!==$this->getUser()->getId())$this->denyAccessUnlessGranted('ROLE_ADMINISTRATOR');$user=$this->em->find(User::class,$userId);
        if($user){$room->getParticipants()->removeElement($user);$member=$this->em->find(UniverseChatUser::class,['universeChat'=>$room,'user'=>$user]);if($member)$this->em->remove($member);$this->em->flush();}return $this->redirectToRoute('chat_index');
    }
    #[Route('/chat/{chatId}/mute-chat',name:'chat_mute', methods: ["POST"])]
    public function muteChat(int $chatId,Request $request): Response {$this->chat->room($chatId,$this->getUser());$request->getSession()->set('chat_muted_'.$chatId,true);return $this->redirectToRoute('chat_show',['chatId'=>$chatId]);}
    private function checkToken(Request $request): void {if(!$this->isCsrfTokenValid('action',$request->request->get('_action_token')??$request->headers->get('X-CSRF-Token')))throw $this->createAccessDeniedException();}
}
