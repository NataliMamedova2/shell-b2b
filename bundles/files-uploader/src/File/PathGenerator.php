<?php

namespace FilesUploader\File;

final class PathGenerator implements PathGeneratorInterface
{
    /**
     * @var string
     */
    private $pathPrefix;

    /**
     * @var int
     */
    private $depth = 2;

    /**
     * @var int
     */
    private $step = 2;

    /**
     * Return relative path.
     *
     * @param string $fileName
     * @param array  $options
     *
     * @return string
     */
    public function generate(string $fileName, array $options = []): string
    {
        if (!empty($options)) {
            $this->setOptions($options);
        }

        $path = '';
        if (!empty($this->pathPrefix)) {
            $path = $this->pathPrefix.DIRECTORY_SEPARATOR;
        }

        if (!empty($fileName)) {
            $path .= $this->generatePath($fileName).DIRECTORY_SEPARATOR;
        }

        return self::fixPath($path);
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            if (false === property_exists($this, $key)) {
                throw new \RuntimeException(sprintf("property '%s' not found in ".__CLASS__, $key));
            }
            $this->{$key} = $value;
        }
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private function generatePath(string $string): string
    {
        $path = '';
        for ($i = 0; $i < $this->step * $this->depth; $i += $this->step) {
            if ($i > 0) {
                $path .= '/';
            }
            $path .= substr($string, $i, $this->step);
        }

        return $path;
    }

    /**
     * Resolve paths with ../, //, etc...
     *
     * @param string $path
     *
     * @return string
     */
    private static function fixPath($path): string
    {
        if (func_num_args() > 1) {
            return self::fixPath(implode(DIRECTORY_SEPARATOR, func_get_args()));
        }

        $replace = ['#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#'];

        do {
            $path = preg_replace($replace, DIRECTORY_SEPARATOR, $path, -1, $n);
        } while ($n > 0);

        return $path;
    }
}
