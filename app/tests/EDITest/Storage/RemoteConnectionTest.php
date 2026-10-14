<?php

declare(strict_types=1);

namespace EDITest\Storage;

use EDI\Storage\RemoteConnection;
use EDI\Storage\TransportProtocol;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class RemoteConnectionTest extends TestCase
{
    public function testSftpUsesDefaultPortAndDoesNotSetPassive(): void
    {
        $config = RemoteConnection::fromArray([
            'host' => 'sftp.example.com',
            'username' => 'user',
            'password' => 'secret',
        ])->toDiskConfig();

        self::assertSame('sftp', $config['driver']);
        self::assertSame(22, $config['port']);
        self::assertSame('/', $config['root']);
        self::assertTrue($config['throw']);
        self::assertArrayNotHasKey('passive', $config);
        self::assertArrayNotHasKey('privateKey', $config);
    }

    public function testFtpUsesDefaultPortAndPassiveMode(): void
    {
        $config = RemoteConnection::fromArray([
            'protocol' => 'ftp',
            'host' => 'ftp.example.com',
            'username' => 'user',
            'password' => 'secret',
            'root' => '/in',
        ])->toDiskConfig();

        self::assertSame('ftp', $config['driver']);
        self::assertSame(21, $config['port']);
        self::assertSame('/in', $config['root']);
        self::assertTrue($config['passive']);
    }

    public function testExplicitPortAndPrivateKeyAreUsed(): void
    {
        $connection = new RemoteConnection(
            protocol: TransportProtocol::Sftp,
            host: 'h',
            username: 'u',
            password: 'p',
            port: 2222,
            privateKey: 'KEY',
        );

        $config = $connection->toDiskConfig();

        self::assertSame(2222, $config['port']);
        self::assertSame('KEY', $config['privateKey']);
    }

    public function testMissingHostThrows(): void
    {
        $this->expectException(InvalidArgumentException::class);

        RemoteConnection::fromArray(['username' => 'u', 'password' => 'p']);
    }
}
