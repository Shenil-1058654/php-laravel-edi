<?php

declare(strict_types=1);

namespace EDITest\Storage;

use EDI\Storage\FileStatus;
use EDI\Storage\FlowLocation;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FlowLocationTest extends TestCase
{
    public function testBuildsDirectoryAndPath(): void
    {
        $location = new FlowLocation('duca', 'inbound', 'newcold');

        self::assertSame('duca/inbound/newcold/new', $location->directory());
        self::assertSame('duca/inbound/newcold/processed', $location->directory(FileStatus::Processed));
        self::assertSame('duca/inbound/newcold/new/a.csv', $location->path('a.csv'));
        self::assertSame('duca/inbound/newcold/failed/a.csv', $location->path('a.csv', FileStatus::Failed));
    }

    #[DataProvider('invalidSlugs')]
    public function testRejectsInvalidSlugs(string $client, string $flow, string $partner): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FlowLocation($client, $flow, $partner);
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function invalidSlugs(): array
    {
        return [
            'path traversal' => ['..', 'inbound', 'newcold'],
            'slash' => ['duca', 'in/bound', 'newcold'],
            'uppercase' => ['Duca', 'inbound', 'newcold'],
            'empty' => ['duca', 'inbound', ''],
        ];
    }

    #[DataProvider('invalidFilenames')]
    public function testRejectsInvalidFilenames(string $filename): void
    {
        $this->expectException(InvalidArgumentException::class);

        (new FlowLocation('duca', 'inbound', 'newcold'))->path($filename);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function invalidFilenames(): array
    {
        return [
            'subdirectory' => ['sub/a.csv'],
            'parent' => ['..'],
            'empty' => [''],
        ];
    }
}
