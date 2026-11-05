<?php
namespace App\Tests\Security;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
final class UserIdentityTest extends TestCase
{
    public function testCanonicalIdentifiersAndRoleIsolation(): void {
        $user=new User();$user->setUsername('Student-001')->setEmail('Student@Example.Test');
        $user->setRoles(['ROLE_ATTENDEE']);
        self::assertSame('student-001',$user->getUsernameCanonical());
        self::assertSame('student@example.test',$user->getEmailCanonical());
        self::assertContains('ROLE_USER',$user->getRoles());self::assertFalse($user->hasRole('ROLE_ADMINISTRATOR'));
        $user->addRole('ROLE_ATTENDEE');self::assertCount(2,$user->getRoles());
    }
    public function testPlainPasswordIsErased(): void {
        $user=new User();$user->setPassword('stored-hash');$user->setPlainPassword('temporary-password');$user->eraseCredentials();
        self::assertNull($user->getPlainPassword());self::assertSame('stored-hash',$user->getPassword());
    }
}
