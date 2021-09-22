<?php

namespace App\Model;

class ServerLimits extends AbstractServerModel
{
    protected ?int $memory;

    protected ?int $swap;

    protected ?int $disk;

    protected ?int $io;

    protected ?int $cpu;

    protected ?int $threads;

    protected ?bool $oomDisabled;


    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $limits = new static();
        $limits->setCpu($data['cpu'] ?? null);
        $limits->setDisk($data['disk'] ?? null);
        $limits->setIo($data['io'] ?? null);
        $limits->setMemory($data['memory'] ?? null);
        $limits->setOomDisabled((bool) $data['oom_disabled'] ?? null);
        $limits->setSwap($data['swap'] ?? null);
        $limits->setThreads($data['threads'] ?? null);

        return $limits;
    }

    /**
     * @return int|null
     */
    public function getMemory(): ?int
    {
        return $this->memory;
    }

    /**
     * @param int|null $memory
     */
    public function setMemory(?int $memory): void
    {
        $this->memory = $memory;
    }

    /**
     * @return int|null
     */
    public function getSwap(): ?int
    {
        return $this->swap;
    }

    /**
     * @param int|null $swap
     */
    public function setSwap(?int $swap): void
    {
        $this->swap = $swap;
    }

    /**
     * @return int|null
     */
    public function getDisk(): ?int
    {
        return $this->disk;
    }

    /**
     * @param int|null $disk
     */
    public function setDisk(?int $disk): void
    {
        $this->disk = $disk;
    }

    /**
     * @return int|null
     */
    public function getIo(): ?int
    {
        return $this->io;
    }

    /**
     * @param int|null $io
     */
    public function setIo(?int $io): void
    {
        $this->io = $io;
    }

    /**
     * @return int|null
     */
    public function getCpu(): ?int
    {
        return $this->cpu;
    }

    /**
     * @param int|null $cpu
     */
    public function setCpu(?int $cpu): void
    {
        $this->cpu = $cpu;
    }

    /**
     * @return int|null
     */
    public function getThreads(): ?int
    {
        return $this->threads;
    }

    /**
     * @param int|null $threads
     */
    public function setThreads(?int $threads): void
    {
        $this->threads = $threads;
    }

    /**
     * @return bool|null
     */
    public function getOomDisabled(): ?bool
    {
        return $this->oomDisabled;
    }

    /**
     * @param bool|null $oomDisabled
     */
    public function setOomDisabled(?bool $oomDisabled): void
    {
        $this->oomDisabled = $oomDisabled;
    }
}