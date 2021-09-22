<?php

namespace App\Service;

use App\Model\Server;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PteroDactylService
{
    private const POWER_START_COMMAND = 'start';
    private const POWER_RESTART_COMMAND = 'restart';
    private const POWER_STOP_COMMAND = 'stop';
    private const POWER_KILL_COMMAND = 'kill';

    protected HttpClientInterface $httpClient;

    /**
     * @param HttpClientInterface $pterodactylClient  Scoped client, autowired
     */
    public function __construct(HttpClientInterface $pterodactylClient)
    {
        $this->httpClient = $pterodactylClient;
    }

    /**
     * @return HttpClientInterface
     */
    public function getHttpClient(): HttpClientInterface
    {
        return $this->httpClient;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     *
     * @return Server[]
     */
    public function listServers(): iterable
    {
        $response = $this->httpClient->request('GET', 'api/client');

        if ($response->getStatusCode() === 403) {
            throw new UnauthorizedHttpException('Authorization');
        }
        if ($response->getStatusCode() === 404) {
            throw new NotFoundHttpException();
        }

        $decodedPayload = $response->toArray();

        if (!empty($decodedPayload['data'])) {
            foreach ($decodedPayload['data'] as $listItem) {
                $objectType = $listItem['object'] ?? null;
                if ($objectType === 'server' && !empty($listItem['attributes'])) {
                    yield Server::fromArray($listItem['attributes']);
                }
            }
        }
    }

    /**
     * @param string $serverId  A server uuid
     * @return Server
     */
    public function getServer(string $serverId): Server
    {
        $response = $this->httpClient->request('GET', 'api/client/servers/' . $serverId);
        if ($response->getStatusCode() === 403) {
            throw new UnauthorizedHttpException('Authorization');
        }

        if ($response->getStatusCode() === 404) {
            throw new NotFoundHttpException();
        }

        $decodedPayload = $response->toArray();

        return  Server::fromArray($decodedPayload['attributes']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function reinstallServer(string $serverId): bool
    {
        $response = $this->httpClient->request('POST', '/api/client/servers/' . $serverId . '/settings/reinstall');

        if ($response->getStatusCode() === 403) {
            throw new UnauthorizedHttpException('Authorization');
        }

        if ($response->getStatusCode() === 404) {
            throw new NotFoundHttpException();
        }

        return $response->getStatusCode() < 300;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function startServer(string $serverId): bool
    {
        return $this->sendPowerCommand($serverId, static::POWER_START_COMMAND);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function restartServer(string $serverId): bool
    {
        return $this->sendPowerCommand($serverId, static::POWER_RESTART_COMMAND);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function stopServer(string $serverId): bool
    {
        return $this->sendPowerCommand($serverId, static::POWER_STOP_COMMAND);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function killServer(string $serverId): bool
    {
        return $this->sendPowerCommand($serverId, static::POWER_KILL_COMMAND);
    }

    /**
     * @var string $serverId  A server UUID
     * @var string $powerSignal  start|stop|restart|kill
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function sendPowerCommand(string $serverId, string $powerSignal): bool
    {
        $response = $this->httpClient->request('POST', '/api/client/servers/' . $serverId . '/power', [
            'json' => [
                'signal' => $powerSignal
            ]
        ]);

        if ($response->getStatusCode() === 403) {
            throw new UnauthorizedHttpException('Authorization');
        }

        if ($response->getStatusCode() === 404) {
            throw new NotFoundHttpException();
        }

        return $response->getStatusCode() < 300;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function sendAdminCommand(string $serverId, string $command): string
    {
        $response = $this->httpClient->request('POST', '/api/client/servers/' . $serverId . '/command', [
            'json' => [
                'command' => $command
            ]
        ]);

        if ($response->getStatusCode() === 403) {
            throw new UnauthorizedHttpException('Authorization');
        }

        if ($response->getStatusCode() === 404) {
            throw new NotFoundHttpException();
        }

        return $response->getStatusCode() < 300;
    }

    /**
     * @param string $serverId
     * @return bool
     */
    public function isInstalling(string $serverId): bool
    {
        return  $this->getServer($serverId)->isInstalling() === true;
    }
}