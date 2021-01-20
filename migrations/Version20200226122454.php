<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200226122454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify drivers tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE drivers_cars_numbers DROP CONSTRAINT FK_78C868E6C3423909');
        $this->addSql('ALTER TABLE drivers_cars_numbers ALTER driver_id SET NOT NULL');
        $this->addSql(
            'ALTER TABLE drivers_cars_numbers ADD CONSTRAINT FK_78C868E6C3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE drivers_phones DROP CONSTRAINT FK_713DDA0FC3423909');
        $this->addSql('ALTER TABLE drivers_phones ALTER driver_id SET NOT NULL');
        $this->addSql(
            'ALTER TABLE drivers_phones ADD CONSTRAINT FK_713DDA0FC3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE drivers_cars_numbers DROP CONSTRAINT fk_78c868e6c3423909');
        $this->addSql('ALTER TABLE drivers_cars_numbers ALTER driver_id DROP NOT NULL');
        $this->addSql(
            'ALTER TABLE drivers_cars_numbers ADD CONSTRAINT fk_78c868e6c3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
        $this->addSql('ALTER TABLE drivers_phones DROP CONSTRAINT fk_713dda0fc3423909');
        $this->addSql('ALTER TABLE drivers_phones ALTER driver_id DROP NOT NULL');
        $this->addSql(
            'ALTER TABLE drivers_phones ADD CONSTRAINT fk_713dda0fc3423909 FOREIGN KEY (driver_id) REFERENCES drivers (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }
}
