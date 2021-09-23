<?php

namespace App\Model;

class FeatureLimits extends AbstractServerModel
{
    protected ?int $databases;
    protected ?int $allocations;
    protected ?int $backups;

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $featureLimits = new static();
        $featureLimits->setAllocations($data['allocations'] ?? null);
        $featureLimits->setDatabases($data['databases'] ?? null);
        $featureLimits->setBackups($data['backups'] ?? null);

        return $featureLimits;
    }

    /**
     * @return int|null
     */
    public function getDatabases(): ?int
    {
        return $this->databases;
    }

    /**
     * @param int|null $databases
     */
    public function setDatabases(?int $databases): void
    {
        $this->databases = $databases;
    }

    /**
     * @return int|null
     */
    public function getAllocations(): ?int
    {
        return $this->allocations;
    }

    /**
     * @param int|null $allocations
     */
    public function setAllocations(?int $allocations): void
    {
        $this->allocations = $allocations;
    }

    /**
     * @return int|null
     */
    public function getBackups(): ?int
    {
        return $this->backups;
    }

    /**
     * @param int|null $backups
     */
    public function setBackups(?int $backups): void
    {
        $this->backups = $backups;
    }
}