<?php
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261105080000 extends AbstractMigration
{
    public function getDescription(): string {return 'Persist threaded comment replies.';}
    public function up(Schema $schema): void {
        $this->addSql('ALTER TABLE universe_comment ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE universe_comment ADD CONSTRAINT FK_COMMENT_PARENT FOREIGN KEY (parent_id) REFERENCES universe_comment (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_COMMENT_PARENT ON universe_comment (parent_id)');
    }
    public function down(Schema $schema): void {$this->addSql('ALTER TABLE universe_comment DROP FOREIGN KEY FK_COMMENT_PARENT');$this->addSql('ALTER TABLE universe_comment DROP parent_id');}
}
