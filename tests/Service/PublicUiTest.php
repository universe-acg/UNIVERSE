<?php
namespace App\Tests\Service;

use App\Service\{PublicCatalogue, PublicPageContent};
use App\Twig\PublicUiExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;
use Twig\Loader\ArrayLoader;

final class PublicUiTest extends TestCase
{
    public function testAdministratorEditsArePreservedAndInstalledContentCanBeUpdated(): void
    {
        self::assertTrue(PublicPageContent::mayUpdate(PublicPageContent::LEGACY, null));
        self::assertTrue(PublicPageContent::mayUpdate('Installed text', hash('sha256', 'Installed text')));
        self::assertFalse(PublicPageContent::mayUpdate('Administrator revision', hash('sha256', 'Installed text')));
        self::assertFalse(PublicPageContent::mayUpdate('Unowned content', null));
    }

    public function testInformationPagesHaveDistinctContentAndStableDestinations(): void
    {
        $pages = (new PublicPageContent(dirname(__DIR__, 2)))->load();
        self::assertSame(['about-us', 'privacy-policy', 'cookie-policy', 'legal-notice'], array_keys($pages));
        self::assertCount(4, array_unique(array_column($pages, 'content')));
        self::assertSame('Terms of service', $pages['legal-notice']['title']);
        self::assertStringContainsString('local storage', $pages['cookie-policy']['content']);
        self::assertStringNotContainsString('<script', implode('', array_column($pages, 'content')));
    }

    public function testCatalogueCombinesKeywordSortingVisibilityAndCategory(): void
    {
        $category = new class { public function getSlug(): string { return 'arts'; } };
        $make = static fn(int $id, string $name, bool $hidden = false) => new class($id, $name, $hidden, $category) {
            public function __construct(private int $id, private string $name, private bool $hidden, private object $category) {}
            public function getId(): int { return $this->id; }
            public function getName(): string { return $this->name; }
            public function getDescription(): string { return 'Student campus activities'; }
            public function getCreatedAt(): \DateTimeImmutable { return new \DateTimeImmutable('2026-11-05'); }
            public function isHidden(): bool { return $this->hidden; }
            public function getCategory(): object { return $this->category; }
        };
        $records = [$make(3, 'Theater'), $make(1, 'Gallery'), $make(2, 'Hidden studio', true)];
        $catalogue = new PublicCatalogue();
        self::assertSame(['Gallery', 'Theater'], array_map(fn($row) => $row->getName(), $catalogue->filter($records, ' CAMPUS ', 'title_asc', 'arts')));
        self::assertCount(1, $catalogue->filter($records, 'theater'));
        self::assertSame([], $catalogue->filter($records, 'campus', 'created_desc', 'sports'));
        self::assertSame([], $catalogue->filter($records, 'no matching venue'));
        self::assertSame([1, 3], array_map(fn($row) => $row->getId(), $catalogue->filter($records)));
    }

    public function testCalendarLabelsUseIcuFormattingAndAthensTimezone(): void
    {
        $twig = new Environment(new ArrayLoader(['badge' => "{{ date|format_datetime(pattern='MMM', locale='en', timezone='Europe/Athens')|upper }} {{ date|format_datetime(pattern='d', locale='en', timezone='Europe/Athens') }}"]));
        $twig->addExtension(new IntlExtension());
        self::assertSame('NOV 12', $twig->render('badge', ['date' => new \DateTimeImmutable('2026-11-11T23:30:00Z')]));
    }

    public function testMissingOrNonpublicMediaHasABoundedLocalFallback(): void
    {
        $ui = new PublicUiExtension(dirname(__DIR__, 2));
        self::assertSame('/images/universe-placeholder.svg', $ui->image('uploads/missing-photo.jpg'));
        self::assertSame('/images/universe-placeholder.svg', $ui->image('../.env.local'));
        self::assertSame('/images/universe-placeholder.svg', $ui->image(null));
        self::assertSame('/uploads/landing/UniVerse.png', $ui->image('uploads/landing/UniVerse.png'));
    }
}
