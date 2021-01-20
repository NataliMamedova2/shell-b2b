<?php

namespace App\Translations\Action\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Translation\Bundle\Catalogue\CatalogueFetcher;
use Translation\Bundle\Catalogue\CatalogueManager;
use Translation\Bundle\Catalogue\CatalogueWriter;
use Translation\Bundle\Model\CatalogueMessage;
use Translation\Bundle\Service\CacheClearer;
use Translation\Bundle\Service\ConfigurationManager;
use Translation\Bundle\Service\StorageManager;

final class ParseJsonCommand extends Command
{
    protected static $defaultName = 'translation:parse-json';

    /**
     * @var TranslatorBagInterface
     */
    private $translator;
    /**
     * @var StorageManager
     */
    private $storageManager;
    /**
     * @var CacheClearer
     */
    private $cacheClearer;
    /**
     * @var CatalogueWriter
     */
    private $catalogueWriter;
    /**
     * @var ConfigurationManager
     */
    private $configurationManager;
    /**
     * @var CatalogueManager
     */
    private $catalogueManager;
    /**
     * @var CatalogueFetcher
     */
    private $catalogueFetcher;

    public function __construct(
        TranslatorBagInterface $translator,
        StorageManager $storageManager,
        CatalogueWriter $catalogueWriter,
        ConfigurationManager $configurationManager,
        CatalogueManager $catalogueManager,
        CatalogueFetcher $catalogueFetcher,
        CacheClearer $cacheClearer
    ) {
        $this->translator = $translator;
        $this->storageManager = $storageManager;
        $this->cacheClearer = $cacheClearer;
        $this->catalogueWriter = $catalogueWriter;
        $this->configurationManager = $configurationManager;
        $this->catalogueManager = $catalogueManager;
        $this->catalogueFetcher = $catalogueFetcher;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('Extract translations from json file.')
            ->setDefinition([
                new InputOption('file', 'f', InputOption::VALUE_REQUIRED, 'Json file to parse translations.'),
            ])
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $errorIo = $io->getErrorStyle();

        $output->writeln([
            'Parse translation from json',
            '===========================',
        ]);

        $file = $input->getOption('file');

        if (null === $file || false === file_exists($file)) {
            $errorIo->error(
                sprintf('File: %s not found', $file)
            );

            return 0;
        }

        $petsJson = file_get_contents($file);
        $collection = json_decode($petsJson, true);

        $domain = 'jsonfile';
        $configName = 'jsonfile';

        $config = $this->configurationManager->getConfiguration($configName);

        $locales = [
            'uk', 'en',
        ];

        foreach ($locales as $locale) {
            $output->writeln([
                '',
                sprintf('Locale %s', $locale),
            ]);

            /** @var MessageCatalogueInterface $catalogue */
            $catalogue = $this->translator->getCatalogue($locale);

            $newCount = 0;
            foreach (array_keys($collection) as $key) {
                if (false === $catalogue->has($key, $domain)) {
                    ++$newCount;
                    $catalogue->set($key, $key, $domain);
                }
            }

            $this->catalogueWriter->writeCatalogues($config, [$catalogue]);

            // Get a catalogue manager and load it with all the catalogues
            $this->catalogueManager->load($this->catalogueFetcher->getCatalogues($config));

            $storage = $this->storageManager->getStorage($configName);
            /** @var CatalogueMessage[] $messages */
            $messages = $this->catalogueManager->getMessages($locale, $domain);

            $deletedCount = 0;
            foreach ($messages as $message) {
                if (false === in_array($message->getKey(), array_keys($collection))) {
                    ++$deletedCount;
                    $storage->delete($message->getLocale(), $message->getDomain(), $message->getKey());
                }
            }

            $this->cacheClearer->clearAndWarmUp($locale);

            $io = new SymfonyStyle($input, $output);
            $io->table(['Type', 'Count'], [
                ['Total defined messages', count($collection)],
                ['New messages', $newCount],
                ['Deleted messages', $deletedCount],
            ]);
        }

        return 0;
    }
}
