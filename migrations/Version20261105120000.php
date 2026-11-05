<?php
namespace DoctrineMigrations;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
final class Version20261105120000 extends AbstractMigration
{
    public function getDescription(): string { return 'Persist direct-conversation identities, read state and idempotent message sends.'; }
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE universe_chat ADD direct_key VARCHAR(80) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_chat_direct ON universe_chat (direct_key)');
        $this->addSql('ALTER TABLE universe_chat_user ADD last_read_message_id INT DEFAULT 0 NOT NULL, ADD muted TINYINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE universe_message ADD client_id VARCHAR(64) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_chat_message_client ON universe_message (universe_chat_id,sender_id,client_id)');
    }
    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX uniq_chat_direct ON universe_chat');
        $this->addSql('ALTER TABLE universe_chat DROP direct_key');
        $this->addSql('ALTER TABLE universe_chat_user DROP last_read_message_id, DROP muted');
        $this->addSql('DROP INDEX uniq_chat_message_client ON universe_message');
        $this->addSql('ALTER TABLE universe_message DROP client_id');
    }
}
