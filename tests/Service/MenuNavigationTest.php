<?php
namespace App\Tests\Service;
use App\Entity\{Menu, MenuElement, User};
use App\Repository\MenuRepository;
use App\Service\MenuNavigation;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Validation;
final class MenuNavigationTest extends TestCase
{
    private function navigation(?array $roles, ?Menu $menu = null): MenuNavigation
    {
        $security = $this->createMock(Security::class);
        $user = $roles === null ? null : new User();
        if ($user) $user->setRoles($roles);
        $security->method('getUser')->willReturn($user);
        $security->method('isGranted')->willReturnCallback(static fn($role) => in_array($role, $roles ?? [], true));
        $repository = $this->createMock(MenuRepository::class);
        $repository->method('findOneBy')->willReturn($menu);
        return new MenuNavigation($repository, $security);
    }
    private function element(string $link, string $audience = 'everyone'): MenuElement
    {
        $element = new MenuElement();
        $element->setLabel('Example'); $element->setLink($link); $element->setAudience($audience);
        return $element;
    }
    public function testRejectsExecutableAndAmbiguousUrls(): void
    {
        foreach (['javascript:alert(1)', '//example.test', '/\\example.test', "/events\n", 'data:text/html,hello', 'https://example.test/a b'] as $url) {
            self::assertFalse(MenuNavigation::isSafeLink($url), $url);
        }
        foreach (['/events?category=arts', '#main_nav', 'https://www.acg.edu/', 'mailto:demo@example.test', 'tel:+302106009800'] as $url) {
            self::assertTrue(MenuNavigation::isSafeLink($url), $url);
        }
        self::assertSame('/events', MenuNavigation::normalizeLink('/open-events'));
        self::assertSame('/dashboard/change-email', MenuNavigation::normalizeLink('/api/profile/change-email'));
        self::assertSame('/venues?city=Athens', MenuNavigation::normalizeLink('https://universe.alexandroskourtis.me/venues?city=Athens'));
    }
    public function testPublicAndRoleSpecificDestinations(): void
    {
        foreach ([null, ['ROLE_ATTENDEE'], ['ROLE_ORGANIZER'], ['ROLE_ADMINISTRATOR']] as $roles) {
            $navigation = $this->navigation($roles);
            self::assertTrue($navigation->isVisible($this->element('/events')));
            self::assertSame($roles === null, $navigation->isVisible($this->element('/signup/attendee', 'guests')));
            self::assertSame($roles !== null, $navigation->isVisible($this->element('/chat')));
            self::assertSame(in_array('ROLE_ATTENDEE', $roles ?? [], true), $navigation->isVisible($this->element('/dashboard/attendee/my-tickets')));
            self::assertSame(in_array('ROLE_ORGANIZER', $roles ?? [], true), $navigation->isVisible($this->element('/dashboard/organizer/my-events/add')));
            self::assertSame(in_array('ROLE_ADMINISTRATOR', $roles ?? [], true), $navigation->isVisible($this->element('/dashboard/administrator/settings/menus')));
            self::assertFalse($navigation->isVisible($this->element('javascript:alert(1)')));
        }
    }
    public function testMissingMenusAndCustomHeadings(): void
    {
        self::assertSame([], $this->navigation(null)->menu('missing')['items']);
        $menu = new Menu(); $menu->setName('First'); $menu->setHeader('About campus');
        $menu->addMenuElement($this->element('/events'));
        $menu->addMenuElement($this->element('/dashboard', 'authenticated'));
        $result = $this->navigation(null, $menu)->menu('first');
        self::assertSame('About campus', $result['heading']);
        self::assertCount(1, $result['items']);
        self::assertSame('/events', $result['items'][0]['url']);
    }
    public function testEditorRequiresOneSafeDestination(): void
    {
        $validator = Validation::createValidatorBuilder()->enableAttributeMapping()->getValidator();
        $element = $this->element('/events');
        self::assertCount(0, $validator->validate($element));
        $element->setCustomLink('/venues');
        self::assertGreaterThan(0, count($validator->validate($element)));
        $element->setLink(null); $element->setCustomLink('javascript:alert(1)');
        self::assertGreaterThan(0, count($validator->validate($element)));
        $element->setCustomLink(null);
        self::assertGreaterThan(0, count($validator->validate($element)));
    }
}
