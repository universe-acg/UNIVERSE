<?php
namespace App\Command;
use App\Service\ChatService;
use Doctrine\ORM\EntityManagerInterface;
use Workerman\Connection\TcpConnection;
/** Signed, expiring subscriptions. Messages are persisted through the same authorized service as HTTP. */
final class ChatHandler
{
    private array $tokens=[];
    public function __construct(private ChatService $chat,private EntityManagerInterface $em) {}
    public function onMessage(TcpConnection $connection,string $message): void {
        try {
            if(strlen($message)>20000)throw new \InvalidArgumentException();
            $data=json_decode($message,true,8,JSON_THROW_ON_ERROR);
            $token=$data['token']??$this->tokens[$connection->id]??'';
            [$user,$room]=$this->chat->resolveToken($token);$this->tokens[$connection->id]=$token;
            if(isset($data['message']))$this->chat->send($room,$user,(string)$data['message']);
            $messages=$this->chat->messages($room,max(0,(int)($data['after']??0)));
            $connection->send(json_encode(['messages'=>array_map(fn($message)=>$this->chat->serialize($message,$user),$messages)],JSON_THROW_ON_ERROR));
        } catch(\Throwable) { $connection->close(json_encode(['error'=>'Invalid subscription or message.'])); }
        finally { $this->em->clear(); }
    }
    public function onClose(TcpConnection $connection): void {unset($this->tokens[$connection->id]);}
}
