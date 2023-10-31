<?php

declare(strict_types=1);

namespace KCode\Modulith\ArchiveExtractors;

use PharData;

class Tar extends Extractor
{
    /**
     * {@inheritdoc}
     */
    public function extract(string $pathToArchive, string $pathToDirectory): void
    {
        $archive = new PharData($pathToArchive);
        $archive->extractTo($pathToDirectory);
    }
}
