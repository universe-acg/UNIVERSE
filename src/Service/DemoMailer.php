<?php
namespace App\Service;
use Symfony\Component\Mime\Email;
/** Demo delivery is captured privately and never sent to external recipients. */
final class DemoMailer
{
    public function __construct(private string $logDirectory) {}
    public function send(Email $message): int {
        if (!is_dir($this->logDirectory) && !mkdir($this->logDirectory, 0700, true) && !is_dir($this->logDirectory)) throw new \RuntimeException('Cannot create delivery log');
        $path = $this->logDirectory.'/'.bin2hex(random_bytes(16)).'.eml';
        if (file_put_contents($path, $message->toString(), LOCK_EX) === false) throw new \RuntimeException('Cannot capture message');
        chmod($path, 0600);
        return count($message->getTo());
    }
    public function captureSms(string $recipient,string $message): void {
        if(!is_dir($this->logDirectory) && !mkdir($this->logDirectory,0700,true) && !is_dir($this->logDirectory))throw new \RuntimeException('Cannot create delivery log');
        $file=$this->logDirectory.'/'.bin2hex(random_bytes(16)).'.sms.json';
        if(file_put_contents($file,json_encode(['delivery'=>'demo','recipient'=>$recipient,'message'=>$message,'created_at'=>(new \DateTimeImmutable())->format(DATE_ATOM)],JSON_THROW_ON_ERROR),LOCK_EX)===false)throw new \RuntimeException('Cannot capture message');
        chmod($file,0600);
    }
}
