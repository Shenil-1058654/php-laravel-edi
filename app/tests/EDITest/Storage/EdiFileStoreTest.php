<?php

declare(strict_types=1);

namespace EDITest\Storage;

use EDI\Storage\EdiFileStore;
use EDI\Storage\FlowLocation;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

final class EdiFileStoreTest extends TestCase
{
    private EdiFileStore $store;

    private FlowLocation $location;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('exports');
        $this->store = new EdiFileStore(Storage::disk('exports'));
        $this->location = new FlowLocation('duca', 'inbound', 'newcold');
    }

    public function testPutWritesFileInNewFolder(): void
    {
        $path = $this->store->put($this->location, 'a.csv', 'inhoud');

        self::assertSame('duca/inbound/newcold/new/a.csv', $path);
        Storage::disk('exports')->assertExists($path);
        self::assertSame(['a.csv'], $this->store->pending($this->location));
    }

    public function testMarkProcessedMovesFile(): void
    {
        $this->store->put($this->location, 'a.csv', 'inhoud');

        $path = $this->store->markProcessed($this->location, 'a.csv');

        self::assertSame('duca/inbound/newcold/processed/a.csv', $path);
        Storage::disk('exports')->assertExists($path);
        Storage::disk('exports')->assertMissing('duca/inbound/newcold/new/a.csv');
        self::assertSame([], $this->store->pending($this->location));
    }

    public function testMarkFailedMovesFile(): void
    {
        $this->store->put($this->location, 'a.csv', 'inhoud');

        $path = $this->store->markFailed($this->location, 'a.csv');

        self::assertSame('duca/inbound/newcold/failed/a.csv', $path);
        Storage::disk('exports')->assertExists($path);
    }
}
