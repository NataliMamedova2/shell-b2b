<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191127165835 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'fuel_limits table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE fuel_limits (id UUID NOT NULL, client_1c_id VARCHAR(255) NOT NULL, card_number VARCHAR(255) NOT NULL, fuel_code VARCHAR(255) NOT NULL, day_limit BIGINT NOT NULL, week_limit BIGINT NOT NULL, month_limit BIGINT NOT NULL, purse_activity BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_60AAD212BF396750 ON fuel_limits (id)');
        $this->addSql('COMMENT ON COLUMN fuel_limits.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN fuel_limits.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fuel_limits.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE fuel_cards');
        $this->addSql('ALTER TABLE cards ADD status SMALLINT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE cards DROP card_status');
        $this->addSql('ALTER TABLE cards ALTER day_limit TYPE BIGINT USING day_limit::bigint');
        $this->addSql('ALTER TABLE cards ALTER day_limit DROP DEFAULT');
        $this->addSql('ALTER TABLE cards ALTER week_limit TYPE BIGINT USING week_limit::bigint');
        $this->addSql('ALTER TABLE cards ALTER week_limit DROP DEFAULT');
        $this->addSql('ALTER TABLE cards ALTER month_limit TYPE BIGINT USING month_limit::bigint');
        $this->addSql('ALTER TABLE cards ALTER month_limit DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE fuel_cards (id UUID NOT NULL, client_1c_id VARCHAR(255) NOT NULL, card_number VARCHAR(255) NOT NULL, fuel_code VARCHAR(255) NOT NULL, day_limit VARCHAR(255) NOT NULL, week_limit VARCHAR(255) NOT NULL, month_limit VARCHAR(255) NOT NULL, purse_activity BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE UNIQUE INDEX uniq_6e620757bf396750 ON fuel_cards (id)');
        $this->addSql('COMMENT ON COLUMN fuel_cards.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN fuel_cards.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN fuel_cards.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('DROP TABLE fuel_limits');
        $this->addSql('ALTER TABLE cards ADD card_status BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE cards DROP status');
        $this->addSql('ALTER TABLE cards ALTER day_limit TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE cards ALTER day_limit DROP DEFAULT');
        $this->addSql('ALTER TABLE cards ALTER week_limit TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE cards ALTER week_limit DROP DEFAULT');
        $this->addSql('ALTER TABLE cards ALTER month_limit TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE cards ALTER month_limit DROP DEFAULT');
    }
}
