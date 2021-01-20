<?php

namespace App\Clients\Infrastructure\Document\FileGenerator;

interface XlsFileGenerator
{
    /**
     * @param array $data
     * @param array $variables
     *
     * @return resource|false The path resource or false on failure.
     */
    public function generate(array $data, array $variables = []);
}
