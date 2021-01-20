<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200225132753 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create drivers tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE drivers_cars_numbers (id UUID NOT NULL, driver_id UUID DEFAULT NULL, number VARCHAR(12) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_78C868E6C3423909 ON drivers_cars_numbers (driver_id)');
        $this->addSql('COMMENT ON COLUMN drivers_cars_numbers.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN drivers_cars_numbers.driver_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN drivers_cars_numbers.created_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql(
            'CREATE TABLE drivers (id UUID NOT NULL, client_1c_id VARCHAR(255) NOT NULL, email VARCHAR(64) DEFAULT NULL, status SMALLINT DEFAULT 0 NOT NULL, note VARCHAR(250) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, first_name VARCHAR(30) NOT NULL, middle_name VARCHAR(30) DEFAULT NULL, last_name VARCHAR(30) NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('COMMENT ON COLUMN drivers.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN drivers.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN drivers.updated_at IS \'(DC2Type:datetime_immutable)\'');

        $this->addSql(
            'CREATE TABLE drivers_phones (id UUID NOT NULL, driver_id UUID DEFAULT NULL, number VARCHAR(13) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX IDX_713DDA0FC3423909 ON drivers_phones (driver_id)');
        $this->addSql('COMMENT ON COLUMN drivers_phones.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN drivers_phones.driver_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN drivers_phones.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'ALTER TABLE drivers_cars_numbers ADD CONSTRAINT FK_78C868E6C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql(
            'ALTER TABLE drivers_phones ADD CONSTRAINT FK_713DDA0FC3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE drivers_cars_numbers DROP CONSTRAINT FK_78C868E6C3423909');
        $this->addSql('ALTER TABLE drivers_phones DROP CONSTRAINT FK_713DDA0FC3423909');
        $this->addSql('DROP TABLE drivers_cars_numbers');
        $this->addSql('DROP TABLE drivers');
        $this->addSql('DROP TABLE drivers_phones');
    }
}
