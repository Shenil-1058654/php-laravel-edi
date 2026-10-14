<?php

declare(strict_types=1);

namespace EDITest\Storage;

use EDI\Storage\RemoteConnection;
use EDI\Storage\RemoteFileTransfer;
use EDI\Storage\RemoteStorageFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Een lokale fake disk speelt de externe SFTP-server; de overdrachtlogica is identiek.
 */
final class RemoteFileTransferTest extends TestCase
{
    private Filesystem $remote;

    private Filesystem $local;

    private RemoteFileTransfer $transfer;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('remote');
        Storage::fake('exports');
        $this->remote = Storage::disk('remote');
        $this->local = Storage::disk('exports');
        $this->transfer = new RemoteFileTransfer();
    }

    public function testUploadCopiesFileToRemote(): void
    {
        $this->local->put('duca/inbound/newcold/new/a.csv', 'inhoud');

        $target = $this->transfer->upload($this->remote, $this->local, 'duca/inbound/newcold/new/a.csv', '/in');

        self::assertSame('/in/a.csv', $target);
        self::assertSame('inhoud', $this->remote->get('in/a.csv'));
        self::assertTrue($this->local->exists('duca/inbound/newcold/new/a.csv'));
    }

    public function testUploadCanRenameFile(): void
    {
        $this->local->put('a.csv', 'inhoud');

        $target = $this->transfer->upload($this->remote, $this->local, 'a.csv', 'in', 'ORDER_1.csv');

        self::assertSame('in/ORDER_1.csv', $target);
        self::assertTrue($this->remote->exists('in/ORDER_1.csv'));
    }

    public function testDownloadFetchesFilesAndDeletesRemote(): void
    {
        $this->remote->put('out/a.edi', 'een');
        $this->remote->put('out/b.edi', 'twee');

        $downloaded = $this->transfer->download($this->remote, 'out', $this->local, 'imports/new');

        sort($downloaded);
        self::assertSame(['imports/new/a.edi', 'imports/new/b.edi'], $downloaded);
        self::assertSame('een', $this->local->get('imports/new/a.edi'));
        self::assertSame([], $this->remote->files('out'));
    }

    public function testDownloadKeepsRemoteWhenAskedTo(): void
    {
        $this->remote->put('out/a.edi', 'een');

        $this->transfer->download($this->remote, 'out', $this->local, 'imports/new', deleteRemote: false);

        self::assertTrue($this->remote->exists('out/a.edi'));
    }

    public function testDownloadSkipsExistingLocalFileAndKeepsRemote(): void
    {
        $this->local->put('imports/new/a.edi', 'lokaal');
        $this->remote->put('out/a.edi', 'extern');

        $downloaded = $this->transfer->download($this->remote, 'out', $this->local, 'imports/new');

        self::assertSame([], $downloaded);
        self::assertSame('lokaal', $this->local->get('imports/new/a.edi'));
        self::assertTrue($this->remote->exists('out/a.edi'));
    }

    public function testFactoryBuildsDiskWithoutConnecting(): void
    {
        $disk = $this->app->make(RemoteStorageFactory::class)->make(
            RemoteConnection::fromArray(['host' => 'sftp.invalid', 'username' => 'u', 'password' => 'p']),
        );

        self::assertInstanceOf(Filesystem::class, $disk);
    }
}
