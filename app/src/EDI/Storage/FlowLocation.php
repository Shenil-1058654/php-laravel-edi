<?php

declare(strict_types=1);

namespace EDI\Storage;

use InvalidArgumentException;

/**
 * Vaste maplocatie van een EDI-flow: {client}/{flow}/{partner}/{status}.
 *
 * Bijvoorbeeld: duca/inbound/newcold/new
 */
final readonly class FlowLocation
{
    public function __construct(
        public string $client,
        public string $flow,
        public string $partner,
    ) {
        foreach (['client' => $client, 'flow' => $flow, 'partner' => $partner] as $name => $value) {
            preg_match('/^[a-z0-9][a-z0-9_-]*$/', $value) === 1
                or throw new InvalidArgumentException("Ongeldige {$name}-slug: '{$value}'.");
        }
    }

    public function directory(FileStatus $status = FileStatus::New): string
    {
        return "{$this->client}/{$this->flow}/{$this->partner}/{$status->value}";
    }

    public function path(string $filename, FileStatus $status = FileStatus::New): string
    {
        basename($filename) === $filename && $filename !== '' && $filename !== '..'
            or throw new InvalidArgumentException("Ongeldige bestandsnaam: '{$filename}'.");

        return $this->directory($status) . '/' . $filename;
    }
}
