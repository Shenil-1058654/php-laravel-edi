<?php

declare(strict_types=1);

namespace EDI\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemManager;

/**
 * Bouwt een filesystem voor een externe server op basis van de opgegeven gegevens.
 * De verbinding wordt pas gemaakt bij het eerste gebruik.
 */
final class RemoteStorageFactory
{
    public function __construct(
        private readonly FilesystemManager $filesystems,
    ) {
    }

    public function make(RemoteConnection $connection): Filesystem
    {
        return $this->filesystems->build($connection->toDiskConfig());
    }
}
