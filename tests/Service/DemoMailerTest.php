<?php
namespace App\Tests\Service;
use App\Service\DemoMailer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email;
final class DemoMailerTest extends TestCase
{
    public function testDeliveryIsCapturedWithPrivatePermissions(): void {
        $dir=sys_get_temp_dir().'/universe-mail-test-'.bin2hex(random_bytes(8));
        try {
            $mailer=new DemoMailer($dir);$message=(new Email())->from('demo@example.test')->to('student@example.test')->subject('Demo')->text('Fictional activity.');
            self::assertSame(1,$mailer->send($message));$files=glob($dir.'/*.eml');self::assertCount(1,$files);
            self::assertSame(0600,fileperms($files[0])&0777);self::assertStringContainsString('Fictional activity.',file_get_contents($files[0]));
        } finally { foreach(glob($dir.'/*')?:[] as $file)unlink($file);if(is_dir($dir))rmdir($dir); }
    }
}
