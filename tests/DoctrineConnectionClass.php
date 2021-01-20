<?php

namespace Tests;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Ramsey\Uuid\Doctrine\UuidType;

final class DoctrineConnectionClass
{
    public static function createEntityManager()
    {
        $paths = [dirname(__DIR__).'/src'];
        $isDevMode = false;
        $proxyDir = null;
        $cache = null;
        $useSimpleAnnotationReader = false;
        $dbParams = [
            'url' => $_ENV['DATABASE_URL'],
        ];

        if (false === Type::hasType('uuid')) {
            Type::addType('uuid', UuidType::class);
        }

        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
        $entityManager = EntityManager::create($dbParams, $config);

        $namingStrategy = new UnderscoreNamingStrategy(CASE_LOWER, true);
        $entityManager->getConfiguration()
            ->setNamingStrategy($namingStrategy);

        return $entityManager;
    }
}
