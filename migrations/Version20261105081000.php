<?php
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261105081000 extends AbstractMigration
{
    public function getDescription(): string {return 'Match the comment parent index to Doctrine metadata.';}
    public function up(Schema $schema): void {$this->addSql('ALTER TABLE universe_comment RENAME INDEX IDX_COMMENT_PARENT TO IDX_4678CDDE727ACA70');}
    public function down(Schema $schema): void {$this->addSql('ALTER TABLE universe_comment RENAME INDEX IDX_4678CDDE727ACA70 TO IDX_COMMENT_PARENT');}
}
