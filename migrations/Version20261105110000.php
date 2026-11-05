<?php
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261105110000 extends AbstractMigration
{
    public function getDescription(): string { return 'Allow full Help Center answers and reusable search tags.'; }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE universe_help_center_article CHANGE content content LONGTEXT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_3C5A4E9D6FBC9426 ON universe_help_center_article');
    }
    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException('Full articles exceed the old 255-character limit and tags may be shared.');
    }
}
