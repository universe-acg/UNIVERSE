<?php
namespace App\Controller\Dashboard\Shared;
use App\Controller\BaseController;
use App\Entity\UniverseMessage;
use App\Service\ChatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Component\Routing\Attribute\Route;
final class MessageController extends BaseController
{
    public function __construct(private ChatService $chat,private EntityManagerInterface $em,private \App\Controller\Front\ChatController $frontend){}
    #[Route('/chat/{chatId}/message-list',name:'message_list',methods:['GET'])]
    public function listMessages(int $chatId): Response {$this->chat->room($chatId,$this->getUser());return $this->redirectToRoute('chat_show',['chatId'=>$chatId]);}
    #[Route('/chat/{chatId}/send-message',name:'message_send', methods: ["POST"])]
    public function sendMessage(int $chatId,Request $request): Response {return $this->frontend->messages($chatId,$request);}
    #[Route('/api/message/save',name:'save_message', methods: ["POST"])]
    public function saveMessage(Request $request): Response {
        try{$data=$request->toArray();}catch(\Throwable){return $this->json(['error'=>'Invalid message.'],400);}
        $request->request->set('message',(string)($data['content']??''));
        if(isset($data['client_id']))$request->request->set('client_id',$data['client_id']);
        return $this->frontend->messages((int)($data['chatId']??0),$request);
    }
    #[Route('/chat/{chatId}/message/{messageId}/edit',name:'message_edit',methods:['POST'])]
    public function editMessage(int $chatId,int $messageId,Request $request): Response {
        if(!$this->isCsrfTokenValid('action',$request->headers->get('X-CSRF-Token')??$request->request->get('_action_token')))throw $this->createAccessDeniedException();
        $message=$this->owned($chatId,$messageId);$text=trim((string)$request->getPayload()->get('message'));if($text===''||mb_strlen($text)>4000)return $this->json(['error'=>'Invalid message.'],422);
        $message->setContent($text);$this->em->flush();return $this->json($this->chat->serialize($message,$this->getUser()));
    }
    #[Route('/chat/{chatId}/message/{messageId}/delete',name:'message_delete', methods: ["POST"])]
    public function deleteMessage(int $chatId,int $messageId): Response {$message=$this->owned($chatId,$messageId);$this->em->remove($message);$this->em->flush();return $this->json(['deleted'=>true]);}
    private function owned(int $chatId,int $messageId): UniverseMessage {
        $room=$this->chat->room($chatId,$this->getUser());$message=$this->em->getRepository(UniverseMessage::class)->findOneBy(['id'=>$messageId,'universeChat'=>$room]);if(!$message)throw $this->createNotFoundException();if($message->getSender()->getId()!==$this->getUser()->getId())throw $this->createAccessDeniedException();return $message;
    }
}
