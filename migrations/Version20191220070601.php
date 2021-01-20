<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191220070601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add histories tables';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql(
            'CREATE TABLE cards_history (id SERIAL NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX type_cbf526234432de36f06d05586b0e96a2_idx ON cards_history (type)');
        $this->addSql('CREATE INDEX object_id_cbf526234432de36f06d05586b0e96a2_idx ON cards_history (object_id)');
        $this->addSql('CREATE INDEX discriminator_cbf526234432de36f06d05586b0e96a2_idx ON cards_history (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_cbf526234432de36f06d05586b0e96a2_idx ON cards_history (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_cbf526234432de36f06d05586b0e96a2_idx ON cards_history (blame_id)');
        $this->addSql('CREATE INDEX created_at_cbf526234432de36f06d05586b0e96a2_idx ON cards_history (created_at)');
        $this->addSql('COMMENT ON COLUMN cards_history.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE cards_stop_list_history (id SERIAL NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX type_48aaa0f86ea471d34e69f6a9be548315_idx ON cards_stop_list_history (type)');
        $this->addSql('CREATE INDEX object_id_48aaa0f86ea471d34e69f6a9be548315_idx ON cards_stop_list_history (object_id)');
        $this->addSql('CREATE INDEX discriminator_48aaa0f86ea471d34e69f6a9be548315_idx ON cards_stop_list_history (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_48aaa0f86ea471d34e69f6a9be548315_idx ON cards_stop_list_history (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_48aaa0f86ea471d34e69f6a9be548315_idx ON cards_stop_list_history (blame_id)');
        $this->addSql('CREATE INDEX created_at_48aaa0f86ea471d34e69f6a9be548315_idx ON cards_stop_list_history (created_at)');
        $this->addSql('COMMENT ON COLUMN cards_stop_list_history.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE fuel_limits_history (id SERIAL NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX type_a8d676f62f7915a592bcbe0fea363b81_idx ON fuel_limits_history (type)');
        $this->addSql('CREATE INDEX object_id_a8d676f62f7915a592bcbe0fea363b81_idx ON fuel_limits_history (object_id)');
        $this->addSql('CREATE INDEX discriminator_a8d676f62f7915a592bcbe0fea363b81_idx ON fuel_limits_history (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_a8d676f62f7915a592bcbe0fea363b81_idx ON fuel_limits_history (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_a8d676f62f7915a592bcbe0fea363b81_idx ON fuel_limits_history (blame_id)');
        $this->addSql('CREATE INDEX created_at_a8d676f62f7915a592bcbe0fea363b81_idx ON fuel_limits_history (created_at)');
        $this->addSql('COMMENT ON COLUMN fuel_limits_history.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE invoices_history (id SERIAL NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX type_e41472755f3a53613742e34eaed7c509_idx ON invoices_history (type)');
        $this->addSql('CREATE INDEX object_id_e41472755f3a53613742e34eaed7c509_idx ON invoices_history (object_id)');
        $this->addSql('CREATE INDEX discriminator_e41472755f3a53613742e34eaed7c509_idx ON invoices_history (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_e41472755f3a53613742e34eaed7c509_idx ON invoices_history (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_e41472755f3a53613742e34eaed7c509_idx ON invoices_history (blame_id)');
        $this->addSql('CREATE INDEX created_at_e41472755f3a53613742e34eaed7c509_idx ON invoices_history (created_at)');
        $this->addSql('COMMENT ON COLUMN invoices_history.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql(
            'CREATE TABLE invoices_items_history (id SERIAL NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))'
        );
        $this->addSql('CREATE INDEX type_55c83f3c34ed169bf4fcb1f353665eb0_idx ON invoices_items_history (type)');
        $this->addSql('CREATE INDEX object_id_55c83f3c34ed169bf4fcb1f353665eb0_idx ON invoices_items_history (object_id)');
        $this->addSql('CREATE INDEX discriminator_55c83f3c34ed169bf4fcb1f353665eb0_idx ON invoices_items_history (discriminator)');
        $this->addSql('CREATE INDEX transaction_hash_55c83f3c34ed169bf4fcb1f353665eb0_idx ON invoices_items_history (transaction_hash)');
        $this->addSql('CREATE INDEX blame_id_55c83f3c34ed169bf4fcb1f353665eb0_idx ON invoices_items_history (blame_id)');
        $this->addSql('CREATE INDEX created_at_55c83f3c34ed169bf4fcb1f353665eb0_idx ON invoices_items_history (created_at)');
        $this->addSql('COMMENT ON COLUMN invoices_items_history.created_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE cards_history');
        $this->addSql('DROP TABLE cards_stop_list_history');
        $this->addSql('DROP TABLE fuel_limits_history');
        $this->addSql('DROP TABLE invoices_history');
        $this->addSql('DROP TABLE invoices_items_history');
    }
}
