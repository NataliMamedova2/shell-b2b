<?php

namespace App\Export\Infrastructure\Service;

use App\Export\Domain\Export\Service\Filename as BaseFilename;
use App\Export\Domain\Export\Service\FilenameGenerator;
use App\Export\Infrastructure\Criteria\Type1C;
use Infrastructure\Interfaces\Repository\Repository;

final class Filename1CGenerator implements FilenameGenerator
{
    private const MAX_LENGTH = 8;
    private const SUFFIX = 'WWW';

    /**
     * @var Repository
     */
    private $exportRepository;

    /**
     * @var int
     */
    private $index = 0;

    public function __construct(Repository $exportRepository)
    {
        $this->exportRepository = $exportRepository;

        $countExported1CFiles = $this->exportRepository->count([
            Type1C::class => true,
        ]);

        $this->index = $countExported1CFiles;
    }

    public function generate(string $ext): BaseFilename
    {
        $index = (string) ++$this->index;
        $indexLength = mb_strlen($index);
        if ($indexLength < self::MAX_LENGTH) {
            $prefix = str_repeat('0', self::MAX_LENGTH - $indexLength);
            $index = $prefix.$index;
        }

        $filename = sprintf('%s_%s', $index, self::SUFFIX);

        return new Filename($filename, $ext);
    }
}
