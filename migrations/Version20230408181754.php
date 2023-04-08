<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230408181754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE champion_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE matchup_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pick_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE champion (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE matchup (id INT NOT NULL, pick_id INT DEFAULT NULL, opponent_id INT DEFAULT NULL, won_games INT DEFAULT NULL, won_lanes INT DEFAULT NULL, total_games INT DEFAULT NULL, total_lanes INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D5ED5651F54A307A ON matchup (pick_id)');
        $this->addSql('CREATE INDEX IDX_D5ED56517F656CDC ON matchup (opponent_id)');
        $this->addSql('CREATE TABLE pick (id INT NOT NULL, player_id INT DEFAULT NULL, champion_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_99CD0F9B99E6F5DF ON pick (player_id)');
        $this->addSql('CREATE INDEX IDX_99CD0F9BFA7FD7EB ON pick (champion_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(255) DEFAULT NULL, creation_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, delete_account_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, valid_email BOOLEAN DEFAULT NULL, last_connection TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('ALTER TABLE matchup ADD CONSTRAINT FK_D5ED5651F54A307A FOREIGN KEY (pick_id) REFERENCES pick (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE matchup ADD CONSTRAINT FK_D5ED56517F656CDC FOREIGN KEY (opponent_id) REFERENCES champion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pick ADD CONSTRAINT FK_99CD0F9B99E6F5DF FOREIGN KEY (player_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE pick ADD CONSTRAINT FK_99CD0F9BFA7FD7EB FOREIGN KEY (champion_id) REFERENCES champion (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE champion_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE matchup_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pick_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('ALTER TABLE matchup DROP CONSTRAINT FK_D5ED5651F54A307A');
        $this->addSql('ALTER TABLE matchup DROP CONSTRAINT FK_D5ED56517F656CDC');
        $this->addSql('ALTER TABLE pick DROP CONSTRAINT FK_99CD0F9B99E6F5DF');
        $this->addSql('ALTER TABLE pick DROP CONSTRAINT FK_99CD0F9BFA7FD7EB');
        $this->addSql('DROP TABLE champion');
        $this->addSql('DROP TABLE matchup');
        $this->addSql('DROP TABLE pick');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
