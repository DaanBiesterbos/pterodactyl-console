<?php

namespace App\Model;

class Server extends AbstractServerModel
{
    protected bool $serverOwner = false;

    protected ?string $identifier;

    protected ?int $internalId;

    protected ?string $uuid;

    protected ?string $name;

    protected ?string $node;

    protected ?string $status;

    protected ?string $description;

    protected ?ServerLimits $limits;

    protected ?FeatureLimits $featureLimits;

    protected ?SftpDetails $sftpDetails;

    protected ?string $invocation;

    protected ?string $dockerImage;

    /**
     * @var array[]
     */
    protected array $eggFeatures = [];

    protected ?bool $suspended;

    protected ?bool $installing;

    protected ?bool $transferring;

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $server = new static();
        $server->setName($data['name'] ?? null);
        $server->setDescription($data['description'] ?? null);
        $server->setDockerImage($data['docker_image'] ?? null);
        $server->setIdentifier($data['identifier'] ?? null);
        $server->setInternalId($data['internal_id'] ?? null);
        $server->setServerOwner($data['server_owner'] ?? null);
        $server->setNode($data['node'] ?? null);
        $server->setStatus($data['status'] ?? null);
        $server->setUuid($data['uuid'] ?? null);
        $server->setTransferring($data['is_transferring'] ?? null);
        $server->setSuspended($data['is_suspended'] ?? null);
        $server->setInstalling($data['is_installing'] ?? null);
        $server->setInvocation($data['invocation'] ?? null);

        if (!empty($data['egg_features'])) {
            $server->setEggFeatures($data['egg_features']);
        }

        if (!empty($data['limits'])) {
            $server->setLimits(ServerLimits::fromArray($data['limits']));
        }

        if (!empty($data['feature_limits'])) {
            $server->setFeatureLimits(FeatureLimits::fromArray($data['feature_limits']));
        }

        if (!empty($data['sftp_details'])) {
            $server->setSftpDetails(SftpDetails::fromArray($data['sftp_details']));
        }

        return $server;
    }

    /**
     * @return bool
     */
    public function isServerOwner(): bool
    {
        return $this->serverOwner;
    }

    /**
     * @param bool $serverOwner
     */
    public function setServerOwner(bool $serverOwner): void
    {
        $this->serverOwner = $serverOwner;
    }

    /**
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    /**
     * @param string|null $identifier
     */
    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @return int|null
     */
    public function getInternalId(): ?int
    {
        return $this->internalId;
    }

    /**
     * @param int|null $internalId
     */
    public function setInternalId(?int $internalId): void
    {
        $this->internalId = $internalId;
    }

    /**
     * @return string|null
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string|null $uuid
     */
    public function setUuid(?string $uuid): void
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getNode(): ?string
    {
        return $this->node;
    }

    /**
     * @param string|null $node
     */
    public function setNode(?string $node): void
    {
        $this->node = $node;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return ServerLimits
     */
    public function getLimits(): ServerLimits
    {
        return $this->limits ?? new ServerLimits();
    }

    /**
     * @param ServerLimits|null $limits
     */
    public function setLimits(?ServerLimits $limits): void
    {
        $this->limits = $limits;
    }

    /**
     * @return string|null
     */
    public function getInvocation(): ?string
    {
        return $this->invocation;
    }

    /**
     * @param string|null $invocation
     */
    public function setInvocation(?string $invocation): void
    {
        $this->invocation = $invocation;
    }

    /**
     * @return string|null
     */
    public function getDockerImage(): ?string
    {
        return $this->dockerImage;
    }

    /**
     * @param string|null $dockerImage
     */
    public function setDockerImage(?string $dockerImage): void
    {
        $this->dockerImage = $dockerImage;
    }

    /**
     * @return array
     */
    public function getEggFeatures(): array
    {
        return $this->eggFeatures;
    }

    /**
     * @param array $eggFeatures
     */
    public function setEggFeatures(array $eggFeatures): void
    {
        $this->eggFeatures = $eggFeatures;
    }

    /**
     * @return bool|null
     */
    public function isSuspended(): ?bool
    {
        return $this->suspended;
    }

    /**
     * @param bool|null $suspended
     */
    public function setSuspended(?bool $suspended): void
    {
        $this->suspended = $suspended;
    }

    /**
     * @return bool|null
     */
    public function isInstalling(): ?bool
    {
        return $this->installing;
    }

    /**
     * @param bool|null $installing
     */
    public function setInstalling(?bool $installing): void
    {
        $this->installing = $installing;
    }

    /**
     * @return bool|null
     */
    public function isTransferring(): ?bool
    {
        return $this->transferring;
    }

    /**
     * @param bool|null $transferring
     */
    public function setTransferring(?bool $transferring): void
    {
        $this->transferring = $transferring;
    }

    /**
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->status;
    }

    /**
     * @param string|null $status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return FeatureLimits
     */
    public function getFeatureLimits(): FeatureLimits
    {
        return $this->featureLimits ?? new FeatureLimits();
    }

    /**
     * @param FeatureLimits|null $featureLimits
     */
    public function setFeatureLimits(?FeatureLimits $featureLimits): void
    {
        $this->featureLimits = $featureLimits;
    }

    /**
     * @return SftpDetails
     */
    public function getSftpDetails(): SftpDetails
    {
        return $this->sftpDetails ?? new SftpDetails('', 22);
    }

    /**
     * @param SftpDetails|null $sftpDetails
     */
    public function setSftpDetails(?SftpDetails $sftpDetails): void
    {
        $this->sftpDetails = $sftpDetails;
    }
}