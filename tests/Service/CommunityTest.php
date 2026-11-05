<?php
namespace App\Tests\Service;
use App\Service\{ChatService,StreamPlayer,CommunityDemoData};
use PHPUnit\Framework\TestCase;
final class CommunityTest extends TestCase
{
    public function testMessagesAreValidatedWithoutDroppingUnicode(): void
    {
        self::assertSame('Γεια σου 😊',ChatService::validateMessage(' Γεια σου 😊 '));
        self::assertSame('a'.str_repeat('😊',3999),ChatService::validateMessage('a'.str_repeat('😊',3999),'valid-message-id-001'));
        foreach (['','   ',str_repeat('x',4001)] as $value) {
            try { ChatService::validateMessage($value); self::fail('Invalid message accepted.'); } catch (\InvalidArgumentException) { self::assertTrue(true); }
        }
    }
    public function testStreamUrlsUseKnownPlayersAndTheCurrentTwitchParent(): void
    {
        self::assertSame('video',StreamPlayer::resolve('/uploads/videos/campus-preview.webm')['kind']);
        self::assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ',StreamPlayer::resolve('https://youtu.be/dQw4w9WgXcQ')['url']);
        self::assertSame('https://player.vimeo.com/video/12345',StreamPlayer::resolve('https://vimeo.com/12345')['url']);
        self::assertStringContainsString('parent=campus.example.test',StreamPlayer::resolve('https://www.twitch.tv/demo_channel','campus.example.test')['url']);
        foreach (['javascript:alert(1)','//evil.test/player','http://example.test','/uploads/videos/../x.webm'] as $url) self::assertNull(StreamPlayer::resolve($url));
        self::assertSame('external',StreamPlayer::resolve('https://www.acg.edu/')['kind']);
    }
    public function testCommunityFixturesHaveRealMediaAndDiverseScores(): void
    {
        $data=(new CommunityDemoData(dirname(__DIR__,2)))->load(); self::assertCount(60,$data['reels']); self::assertCount(4,$data['lives']); self::assertCount(5,$data['rooms']);
        self::assertCount(60,array_unique(array_column($data['reels'],'title')));
        foreach ($data['reels'] as $row) { self::assertFileExists(dirname(__DIR__,2).'/data/demo-media/'.$row['mainImage']); self::assertLessThanOrEqual(1000,mb_strlen($row['content'])); }
        self::assertGreaterThan(0,CommunityDemoData::points('student-001')); self::assertSame(0,CommunityDemoData::points('demo-admin'));
    }
}
