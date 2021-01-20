<?php

namespace App\Clients\Action\Backend\Client\DocumentAction;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

final class DocumentFormDto
{
    /**
     * @Assert\NotBlank()
     * @Assert\Choice(callback={"\App\Clients\Domain\Document\ValueObject\Type", "getNames"})
     */
    public $type;

    /**
     * @var UploadedFile
     *
     * @Assert\File(
     *      maxSize="10M",
     *      mimeTypes={
     *          "application/pdf",
     *          "application/vnd.ms-excel"
     *      },
     * )
     */
    public $document;
}
