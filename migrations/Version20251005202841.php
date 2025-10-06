<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251005202841 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create event sourcing table with unique constraint to prevent duplicate events';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE book_id_seq CASCADE');
        $this->addSql('CREATE TABLE event (id SERIAL NOT NULL, event_type VARCHAR(255) NOT NULL, aggregate_id VARCHAR(255) NOT NULL, aggregate_type VARCHAR(255) NOT NULL, payload JSON NOT NULL, metadata JSON DEFAULT NULL, version INT NOT NULL, occurred_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_aggregate ON event (aggregate_id, aggregate_type)');
        $this->addSql('CREATE INDEX idx_event_type ON event (event_type)');
        $this->addSql('CREATE INDEX idx_occurred_at ON event (occurred_at)');
        $this->addSql('CREATE UNIQUE INDEX uniq_aggregate_version ON event (aggregate_id, aggregate_type, version)');
        $this->addSql('COMMENT ON COLUMN event.occurred_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE book');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE book_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE book (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, isbn VARCHAR(20) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN book.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN book.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE event');
    }
}
