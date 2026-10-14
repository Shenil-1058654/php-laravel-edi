<?php

declare(strict_types=1);

namespace EDI\Storage;

enum TransportProtocol: string
{
    case Sftp = 'sftp';
    case Ftp = 'ftp';

    public function defaultPort(): int
    {
        return match ($this) {
            self::Sftp => 22,
            self::Ftp => 21,
        };
    }
}
