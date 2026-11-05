<?php
namespace App\Service;
/** Self-contained demo SMS capture; never contacts a delivery provider. */
final class TwilioService
{
    public function __construct(private DemoMailer $capture) {}
    public function sendSMS(string $to,string $message): array {
        $this->capture->captureSms($to,$message);
        return ['status'=>'captured','delivery'=>'demo'];
    }
}
