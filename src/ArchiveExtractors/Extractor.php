<?php

declare(strict_types=1);

namespace KCode\Modulith\ArchiveExtractors;

abstract class Extractor
{
    abstract public function extract(string $pathToArchive, string $pathToDirectory): void;
}
