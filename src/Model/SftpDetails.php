<?php

namespace App\Model;

class SftpDetails extends AbstractServerModel
{
    public function __construct(
        protected ?string $ip,
        protected ?int $port
    ) {}

    public static function fromArray(array $data): static
    {
        return new static($data['ip'] ?? null, $data['port'] ?? null);
    }

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }
}