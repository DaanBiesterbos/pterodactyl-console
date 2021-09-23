<?php

namespace App\Command;

use App\Model\Server;
use App\Service\PteroDactylService;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\StyleInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Abstract server command
 */
abstract class AbstractServerCommand extends Command
{
    public function __construct(
        protected PteroDactylService $pterodactyl
    ) {
        parent::__construct(null);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function selectServerInteractively(StyleInterface $io, InputInterface $input, OutputInterface $output): ?string
    {
        $servers = $this->pterodactyl->listServers();
        $serverItems = [];
        $serverIds = [];

        foreach ($servers as $server) {
            $serverName =  $server->getName() . ', ' . $server->getDescription() . ' (' . $server->getUuid() . ')';
            $serverIds[$serverName] = $server->getUuid();
            $serverItems[] = $serverName;
        }

        if (empty($serverItems)) {
            $io->error("There are no services linked to your account, or you lack sufficient user permissions.");

            return null;
        }

        do {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion(
                'Select a server',
                $serverItems
            );

            $question->setErrorMessage('Server %s is invalid.');

            $selectedServer = $helper->ask($input, $output, $question);

            $output->writeln('You have selected: ' . $selectedServer);

            $isServerConfirmed = $io->confirm('Is this correct?');
            if ($isServerConfirmed) {
                $io->text("Selected server: {$selectedServer}");
                return $serverIds[$selectedServer];
            } else if(false === $io->confirm('Do you want to try again?')) {
                $io->error("No server was selected.");
                return null;
            }

        } while (true);
    }

    /**
     * @param SymfonyStyle $io
     * @param Server $server
     */
    protected function renderServerInfo(SymfonyStyle $io, Server $server)
    {
        $io->title("Server: {$server->getName()} | {$server->getDescription()}");
        $io->table(
            ['Key', 'Value'],
            [
                ['uuid', $server->getUuid()],
                ['identifier', $server->getIdentifier()],
                ['internal_id', $server->getInternalId()],
                ['name', $server->getName()],
                ['node', $server->getNode()],
                ['sftp', $server->getSftpDetails()->getIp() . ':' . $server->getSftpDetails()->getPort()],
                ['max_memory', $server->getLimits()->getMemory()],
                ['max_swap', $server->getLimits()->getSwap()],
                ['max_disk', $server->getLimits()->getDisk()],
                ['max_io', $server->getLimits()->getIo()],
                ['max_cpu', $server->getLimits()->getCpu()],
                ['max_threads', $server->getLimits()->getThreads()],
                ['max_databases', $server->getFeatureLimits()->getDatabases()],
                ['max_allocations', $server->getFeatureLimits()->getAllocations()],
                ['max_backups', $server->getFeatureLimits()->getBackups()],
                ['status', $server->getStatus()],
                ['suspended', ($server->isSuspended()) ? 'yes' : 'no'],
                ['installing', ($server->isInstalling()) ? 'yes' : 'no'],
                ['transferring', ($server->isTransferring()) ? 'yes' : 'no'],
                ['server_owner', ($server->isServerOwner()) ? 'yes' : 'no'],
            ]
        );
    }
}