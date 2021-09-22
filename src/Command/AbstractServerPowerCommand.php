<?php

namespace App\Command;

use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;

/**
 * Abstract server power command
 */
abstract class AbstractServerPowerCommand extends AbstractServerCommand
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     */
    protected function awaitInstall(StyleInterface $io, string $uuid): void
    {
        if ($this->pterodactyl->isInstalling($uuid)) {
            $io->warning("Waiting for installation to complete...");
            while ($this->pterodactyl->isInstalling($uuid)) {
                sleep(5);
            }
        }
    }
}