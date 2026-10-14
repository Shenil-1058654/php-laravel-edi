<?php

declare(strict_types=1);

namespace EDI\Storage;

enum FileStatus: string
{
    case New = 'new';
    case Processed = 'processed';
    case Failed = 'failed';
}
