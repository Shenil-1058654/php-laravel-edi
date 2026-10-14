<?php

declare(strict_types=1);

namespace EDI\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Lokale opslag van EDI-bestanden volgens de vaste mapstructuur (zie FlowLocation).
 * Een bestand begint in "new" en gaat na verwerking naar "processed" of "failed".
 */
final class EdiFileStore
{
    public function __construct(
        private readonly Filesystem $disk,
    ) {
    }

    public function put(FlowLocation $location, string $filename, string $contents): string
    {
        $path = $location->path($filename);
        $this->disk->put($path, $contents);

        return $path;
    }

    /**
     * @return list<string> Bestandsnamen die nog verwerkt moeten worden
     */
    public function pending(FlowLocation $location): array
    {
        return array_map(
            basename(...),
            $this->disk->files($location->directory(FileStatus::New)),
        );
    }

    public function markProcessed(FlowLocation $location, string $filename): string
    {
        return $this->move($location, $filename, FileStatus::Processed);
    }

    public function markFailed(FlowLocation $location, string $filename): string
    {
        return $this->move($location, $filename, FileStatus::Failed);
    }

    private function move(FlowLocation $location, string $filename, FileStatus $to): string
    {
        $target = $location->path($filename, $to);
        $this->disk->move($location->path($filename), $target);

        return $target;
    }
}
