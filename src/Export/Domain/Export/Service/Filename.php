<?php

namespace App\Export\Domain\Export\Service;

interface Filename
{
    /**
     * Name without extension.
     *
     * @return string
     */
    public function getFilename(): string;

    /**
     * File extension.
     *
     * @return string
     */
    public function getExtension(): string;

    /** Full file name. Name with extension.
     *
     * @return string
     */
    public function getBasename(): string;
}
