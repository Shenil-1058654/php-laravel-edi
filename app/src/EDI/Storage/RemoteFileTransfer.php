<?php

declare(strict_types=1);

namespace EDI\Storage;

use Illuminate\Contracts\Filesystem\Filesystem;

/**
 * Verstuurt en haalt bestanden op tussen een lokale en een externe disk (SFTP/FTP).
 * Controleert na elke overdracht de bestandsgrootte voordat er iets verwijderd of als gelukt gemeld wordt.
 */
final class RemoteFileTransfer
{
    /**
     * Stuurt een lokaal bestand naar de externe server.
     *
     * @return string Pad van het bestand op de externe server
     */
    public function upload(
        Filesystem $remote,
        Filesystem $local,
        string $localPath,
        string $remoteDirectory,
        ?string $remoteName = null,
    ): string {
        $target = rtrim($remoteDirectory, '/') . '/' . ($remoteName ?? basename($localPath));

        $stream = $local->readStream($localPath)
            ?? throw new FileTransferException("Kan lokaal bestand niet lezen: {$localPath}");

        try {
            $remote->writeStream($target, $stream);
        } finally {
            is_resource($stream) and fclose($stream);
        }

        $remote->size($target) === $local->size($localPath)
            or throw new FileTransferException("Upload onvolledig: {$target}");

        return $target;
    }

    /**
     * Haalt alle bestanden uit een externe map op en verwijdert ze daarna (optioneel) op de server.
     * Een bestand dat lokaal al bestaat wordt overgeslagen en blijft op de server staan,
     * zodat er nooit iets overschreven of kwijtgeraakt wordt.
     *
     * @return list<string> Lokale paden van de opgehaalde bestanden
     */
    public function download(
        Filesystem $remote,
        string $remoteDirectory,
        Filesystem $local,
        string $localDirectory,
        bool $deleteRemote = true,
    ): array {
        $downloaded = [];

        foreach ($remote->files($remoteDirectory) as $remotePath) {
            $target = rtrim($localDirectory, '/') . '/' . basename($remotePath);

            if ($local->exists($target)) {
                continue;
            }

            $this->fetch($remote, $remotePath, $local, $target);
            $deleteRemote and $remote->delete($remotePath);
            $downloaded[] = $target;
        }

        return $downloaded;
    }

    private function fetch(Filesystem $remote, string $remotePath, Filesystem $local, string $target): void
    {
        $stream = $remote->readStream($remotePath)
            ?? throw new FileTransferException("Kan extern bestand niet lezen: {$remotePath}");

        try {
            $local->writeStream($target, $stream);
        } finally {
            is_resource($stream) and fclose($stream);
        }

        $local->size($target) === $remote->size($remotePath)
            or throw new FileTransferException("Download onvolledig: {$remotePath}");
    }
}
