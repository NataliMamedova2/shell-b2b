<?php

namespace FilesUploader\HttpFoundation;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Mime\MimeTypes;

final class Base64EncodedFile extends File
{
    /**
     * @param string $encoded
     * @param bool $strict
     * @param bool $checkPath
     */
    public function __construct($encoded, $strict = true, $checkPath = true)
    {
        parent::__construct($this->restoreToTemporary($encoded, $strict), $checkPath);
    }

    /**
     * @param string $encoded
     * @param bool $strict
     *
     * @return string
     * @throws FileException
     */
    private function restoreToTemporary($encoded, $strict = true): string
    {
        if (strpos($encoded, 'data:') === 0) {
            if (strpos($encoded, 'data://') !== 0) {
                $encoded = substr_replace($encoded, 'data://', 0, 5);
            }
            $source = @fopen($encoded, 'r');
            if ($source === false) {
                throw new FileException('Unable to decode strings as base64');
            }
            $meta = stream_get_meta_data($source);
            if ($strict) {
                if (!isset($meta['base64']) || $meta['base64'] !== true) {
                    throw new FileException('Unable to decode strings as base64');
                }
            }
            if (false === $path = tempnam($directory = sys_get_temp_dir(), 'Base64EncodedFile')) {
                throw new FileException(sprintf('Unable to create a file into the "%s" directory', $path));
            }
            if ([] !== $extension = (new MimeTypes())->getExtensions($meta['mediatype'])) {
                $path .= '.' . current($extension);
            }
            if (false === $target = @fopen($path, 'w+b')) {
                throw new FileException(sprintf('Unable to write the file "%s"', $path));
            }
            if (false === @stream_copy_to_stream($source, $target)) {
                throw new FileException(sprintf('Unable to write the file "%s"', $path));
            }
            if (false === @fclose($target)) {
                throw new FileException(sprintf('Unable to write the file "%s"', $path));
            }
            if (false === @fclose($source)) {
                throw new FileException(sprintf('Unable to close data stream'));
            }

            return $path;
        }
        if (false === $decoded = base64_decode($encoded, $strict)) {
            throw new FileException('Unable to decode strings as base64');
        }
        if (false === $path = tempnam($directory = sys_get_temp_dir(), 'Base64EncodedFile')) {
            throw new FileException(sprintf('Unable to create a file into the "%s" directory', $directory));
        }
        if (false === file_put_contents($path, $decoded, FILE_BINARY)) {
            throw new FileException(sprintf('Unable to write the file "%s"', $path));
        }

        return $path;
    }
}
