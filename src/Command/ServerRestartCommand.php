<?php

namespace App\Command;

use App\Service\PteroDactylService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

#[AsCommand(
    name: 'server:restart',
    description: 'Send restart signal to a server.',
)]
class ServerRestartCommand extends AbstractServerPowerCommand
{
    /**
     * Configure command
     */
    protected function configure(): void
    {
        $this->addArgument('serverId', InputArgument::OPTIONAL, 'A server UUID');
        $this->addOption(
            'ignore-install',
            'ig',
            InputOption::VALUE_NONE,
            'Do not wait for pending installations'
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Restart Server");

        try {
            $serverId = $input->getArgument('serverId');
            if (empty($serverId)) {
                if (!$input->isInteractive()) {
                    $io->error('The serverId argument is required in non interactive mode!');

                    return Command::FAILURE;
                }
            } else {
                return $this->executeRestart($io, $serverId, (bool) $input->getOption('ignore-install'));
            }

            $serverId = $this->selectServerInteractively($io, $input, $output);
            if ($serverId === null) {
                return Command::FAILURE;
            }

            return $this->executeRestart($io, $serverId, (bool) $input->getOption('ignore-install'));

        } catch (UnauthorizedHttpException $e) {
            $io->error("You are not authorized to access this resource. Verify that the PTERODACTYL_API_KEY is correctly configured.");
            return Command::INVALID;
        } catch (NotFoundHttpException $e) {
            $io->error("Resource not found. ");
            return Command::INVALID;
        } catch (Throwable $e) {
            $io->error("Unknown error has occurred: {$e->getMessage()} on line {$e->getLine()} in {$e->getFile()}");
        }

        return Command::FAILURE;
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    private function executeRestart(SymfonyStyle $io, string $serverId, bool $installIgnored = false): int
    {
        if (false === $installIgnored) {
            $this->awaitInstall($io, $serverId);
        }

        if ($this->pterodactyl->restartServer($serverId)) {
            $io->success("Restart signal was sent successfully!");

            return Command::SUCCESS;
        }

        $io->error("Failed to send restart signal...");

        return Command::FAILURE;
    }
}
