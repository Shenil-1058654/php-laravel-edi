<?php

declare(strict_types=1);

namespace EDI\Storage;

use InvalidArgumentException;

/**
 * Verbindingsgegevens voor een externe SFTP- of FTP-server.
 *
 * De gegevens komen meestal uit de database (per partner) of uit de .env.
 */
final readonly class RemoteConnection
{
    public function __construct(
        public TransportProtocol $protocol,
        public string $host,
        public string $username,
        #[\SensitiveParameter]
        public string $password,
        public string $root = '/',
        public ?int $port = null,
        public int $timeout = 10,
        public ?string $privateKey = null,
        public bool $passive = true,
    ) {
    }

    /**
     * @param array<string, mixed> $config Sleutels: protocol, host, username, password, root, port, timeout, private_key, passive
     */
    public static function fromArray(array $config): self
    {
        return new self(
            protocol: TransportProtocol::from($config['protocol'] ?? 'sftp'),
            host: $config['host'] ?? throw new InvalidArgumentException('Verbinding mist "host".'),
            username: $config['username'] ?? throw new InvalidArgumentException('Verbinding mist "username".'),
            password: $config['password'] ?? throw new InvalidArgumentException('Verbinding mist "password".'),
            root: $config['root'] ?? '/',
            port: isset($config['port']) ? (int) $config['port'] : null,
            timeout: (int) ($config['timeout'] ?? 10),
            privateKey: $config['private_key'] ?? null,
            passive: (bool) ($config['passive'] ?? true),
        );
    }

    /**
     * Configuratie in het formaat van een Laravel filesystem disk.
     *
     * @return array<string, mixed>
     */
    public function toDiskConfig(): array
    {
        $config = [
            'driver' => $this->protocol->value,
            'host' => $this->host,
            'username' => $this->username,
            'password' => $this->password,
            'port' => $this->port ?? $this->protocol->defaultPort(),
            'root' => $this->root,
            'timeout' => $this->timeout,
            'throw' => true,
        ];

        $this->protocol === TransportProtocol::Ftp and $config['passive'] = $this->passive;
        $this->privateKey === null or $config['privateKey'] = $this->privateKey;

        return $config;
    }
}
