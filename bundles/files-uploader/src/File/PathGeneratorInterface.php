<?php

namespace FilesUploader\File;

interface PathGeneratorInterface
{
    /**
     * @param string $fileName
     * @param array $options
     * [
     *      'pathPrefix' => 'directoryName',
     *      'depth' => 1,
     *      'step' => 2,
     * ]
     * @return mixed
     */
    public function generate(string $fileName, array $options = []);
}
