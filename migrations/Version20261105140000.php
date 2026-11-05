<?php

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261105140000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add 13 targeted indexes for high-traffic query patterns.';
    }

    public function up(Schema $schema): void
    {
        // Tier 1 — every authenticated page load
        $this->addSql('CREATE INDEX idx_notif_user_read ON universe_notification (userid, isread)');
        $this->addSql('CREATE INDEX idx_settings_key ON universe_settings (`key`)');
        $this->addSql('CREATE INDEX idx_cat_featured_order ON universe_category (featured, featuredorder)');

        // Tier 2 — content listing pages
        $this->addSql('CREATE INDEX idx_event_published ON universe_event (published)');
        $this->addSql('CREATE INDEX idx_evdate_start ON universe_event_date (startdate)');
        $this->addSql('CREATE INDEX idx_evdate_event_start ON universe_event_date (event_id, startdate)');
        $this->addSql('CREATE INDEX idx_order_status ON universe_order (status)');
        $this->addSql('CREATE INDEX idx_blog_hidden ON universe_blog_post (hidden)');
        $this->addSql('CREATE INDEX idx_reels_hidden ON universe_reels (hidden)');
        $this->addSql('CREATE INDEX idx_listing_published ON universe_listing (published)');

        // Tier 3 — help center + facilities
        $this->addSql('CREATE INDEX idx_helpart_hidden_cat ON universe_help_center_article (hidden, category_id)');
        $this->addSql('CREATE INDEX idx_helpcat_hidden_parent ON universe_help_center_category (hidden, parent_id)');
        $this->addSql('CREATE INDEX idx_booking_facility_date ON universe_athletic_booking (facility_id, booking_date)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX idx_notif_user_read ON universe_notification');
        $this->addSql('DROP INDEX idx_settings_key ON universe_settings');
        $this->addSql('DROP INDEX idx_cat_featured_order ON universe_category');
        $this->addSql('DROP INDEX idx_event_published ON universe_event');
        $this->addSql('DROP INDEX idx_evdate_start ON universe_event_date');
        $this->addSql('DROP INDEX idx_evdate_event_start ON universe_event_date');
        $this->addSql('DROP INDEX idx_order_status ON universe_order');
        $this->addSql('DROP INDEX idx_blog_hidden ON universe_blog_post');
        $this->addSql('DROP INDEX idx_reels_hidden ON universe_reels');
        $this->addSql('DROP INDEX idx_listing_published ON universe_listing');
        $this->addSql('DROP INDEX idx_helpart_hidden_cat ON universe_help_center_article');
        $this->addSql('DROP INDEX idx_helpcat_hidden_parent ON universe_help_center_category');
        $this->addSql('DROP INDEX idx_booking_facility_date ON universe_athletic_booking');
    }
}
