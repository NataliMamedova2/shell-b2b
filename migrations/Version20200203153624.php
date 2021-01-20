<?php

declare(strict_types=1);

namespace Migrations;

use App\Clients\Domain\Document\Document;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use DoctrineBatchUtils\BatchProcessing\SimpleBatchIteratorAggregate;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200203153624 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $query = $em->createQuery(
            sprintf('SELECT d FROM %s d', Document::class)
        );

        $batchSize = 10000;
        $iterable = SimpleBatchIteratorAggregate::fromQuery($query, $batchSize);
        foreach ($iterable as $entity) {
            /** @var Document $document */
            $document = $entity[0];

            $file = $document->getFile();
            $params = [
                'id' => $document->getId(),
                'fileName' => basename($file->getName(), '.'.$file->getExtension()),
            ];
            $this->addSql('UPDATE documents SET file_name = :fileName WHERE id = :id', $params);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.entity_manager');

        $query = $em->createQuery(
            sprintf('SELECT d FROM %s d', Document::class)
        );

        $batchSize = 10000;
        $iterable = SimpleBatchIteratorAggregate::fromQuery($query, $batchSize);
        foreach ($iterable as $entity) {
            /** @var Document $document */
            $document = $entity[0];

            $file = $document->getFile();
            $params = [
                'id' => $document->getId(),
                'fileName' => $file->getName().'.'.$file->getExtension(),
            ];
            $this->addSql('UPDATE documents SET file_name = :fileName WHERE id = :id', $params);
        }
    }
}