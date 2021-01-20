<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191212151445 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Modify cards_changes & companies table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cards_changes DROP CONSTRAINT FK_899CE674E4AF4C20');
        $this->addSql(
            'ALTER TABLE cards_changes ADD CONSTRAINT FK_899CE674E4AF4C20 FOREIGN KEY (card_number) REFERENCES cards (card_number) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );

        $this->addSql('ALTER TABLE companies DROP CONSTRAINT FK_8244AA3A783E3463');
        $this->addSql(
            'ALTER TABLE companies ADD CONSTRAINT FK_8244AA3A783E3463 FOREIGN KEY (manager_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE'
        );
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE cards_changes DROP CONSTRAINT fk_899ce674e4af4c20');
        $this->addSql(
            'ALTER TABLE cards_changes ADD CONSTRAINT fk_899ce674e4af4c20 FOREIGN KEY (card_number) REFERENCES cards (card_number) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );

        $this->addSql('ALTER TABLE companies DROP CONSTRAINT fk_8244aa3a783e3463');
        $this->addSql(
            'ALTER TABLE companies ADD CONSTRAINT fk_8244aa3a783e3463 FOREIGN KEY (manager_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE'
        );

    }
}

    
