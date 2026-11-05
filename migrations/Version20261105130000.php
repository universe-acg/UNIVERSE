<?php
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261105130000 extends AbstractMigration
{
    public function getDescription(): string { return 'Support complete media captions, stable reel identities and owned streaming sessions.'; }
    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_C6AE6E246FBC9426 ON universe_reels');
        $this->addSql('ALTER TABLE universe_reels CHANGE content content VARCHAR(1000) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C6AE6E24989D9B62 ON universe_reels (slug)');
        $this->addSql('ALTER TABLE universe_lives ADD owner_id INT DEFAULT NULL, CHANGE title title VARCHAR(255) DEFAULT NULL, CHANGE link link VARCHAR(2048) DEFAULT NULL');
        $this->addSql('ALTER TABLE universe_lives ADD CONSTRAINT FK_9C0A5E1F7E3C61F9 FOREIGN KEY (owner_id) REFERENCES universe_user (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_9C0A5E1F7E3C61F9 ON universe_lives (owner_id)');
    }
    public function down(Schema $schema): void { $this->throwIrreversibleMigrationException('Longer captions and stream URLs cannot be safely truncated.'); }
}
