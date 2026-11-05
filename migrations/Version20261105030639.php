<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20261105030639 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE universe_blog_post DROP INDEX UNIQ_C1970FEA76ED395, ADD INDEX IDX_C1970FEA76ED395 (user_id)');
        $this->addSql('ALTER TABLE universe_reels DROP INDEX UNIQ_C6AE6E24A76ED395, ADD INDEX IDX_C6AE6E24A76ED395 (user_id)');
        $this->addSql('ALTER TABLE universe_reels_likes DROP INDEX UNIQ_415BA8297802C31, ADD INDEX IDX_415BA8297802C31 (reel_id)');
        $this->addSql('ALTER TABLE universe_reels_likes DROP INDEX UNIQ_415BA829A76ED395, ADD INDEX IDX_415BA829A76ED395 (user_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_user_reel ON universe_reels_likes (user_id, reel_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE universe_blog_post DROP INDEX IDX_C1970FEA76ED395, ADD UNIQUE INDEX UNIQ_C1970FEA76ED395 (user_id)');
        $this->addSql('ALTER TABLE universe_reels DROP INDEX IDX_C6AE6E24A76ED395, ADD UNIQUE INDEX UNIQ_C6AE6E24A76ED395 (user_id)');
        $this->addSql('ALTER TABLE universe_reels_likes DROP INDEX IDX_415BA829A76ED395, ADD UNIQUE INDEX UNIQ_415BA829A76ED395 (user_id)');
        $this->addSql('ALTER TABLE universe_reels_likes DROP INDEX IDX_415BA8297802C31, ADD UNIQUE INDEX UNIQ_415BA8297802C31 (reel_id)');
        $this->addSql('DROP INDEX uniq_user_reel ON universe_reels_likes');
    }
}
