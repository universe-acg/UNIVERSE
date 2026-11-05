<?php
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261105090000 extends AbstractMigration
{
    public function getDescription(): string {return 'Persist menu headings, audiences and header dropdown placement.';}
    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE universe_menu ADD header VARCHAR(255) DEFAULT NULL');
        $this->addSql("ALTER TABLE universe_menu_element ADD audience VARCHAR(20) DEFAULT 'everyone' NOT NULL, ADD placement VARCHAR(16) DEFAULT 'primary' NOT NULL");
        $this->addSql("UPDATE universe_menu_element SET audience='attendees' WHERE COALESCE(link, custom_link) LIKE '/dashboard/attendee/%'");
        $this->addSql("UPDATE universe_menu_element SET audience='organizers' WHERE COALESCE(link, custom_link) LIKE '/dashboard/organizer/%'");
        $this->addSql("UPDATE universe_menu_element SET audience='guests' WHERE COALESCE(link, custom_link) LIKE '/signup/%'");
        $this->addSql("UPDATE universe_menu_element SET audience='authenticated' WHERE COALESCE(link, custom_link) LIKE '/dashboard/change-%'");
    }
    public function down(Schema $schema): void {$this->addSql('ALTER TABLE universe_menu DROP header');$this->addSql('ALTER TABLE universe_menu_element DROP audience, DROP placement');}
}
