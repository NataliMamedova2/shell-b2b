<?php

use App\Clients\Domain\Document\Document;
use App\Clients\Domain\Document\ValueObject\File;
use App\Clients\Domain\Invoice\Invoice;
use League\FactoryMuffin\FactoryMuffin;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Document::class)
    ->setMaker(
        function ($class) use ($fm) {
            /* @var Document $class */

            /** @var Invoice $invoice */
            $invoice = $fm->instance(Invoice::class);

            return $class::createFromInvoice(
                $invoice,
                new File('tests/', 'act-check-template', 'xlsx'),
                new \DateTimeImmutable()
            );
        }
    );
