<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
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
    name: 'server:reinstall',
    description: 'Reinstall a server',
)]
class InstallCommand extends AbstractServerCommand
{
    /**
     * Configure command
     */
    protected function configure(): void
    {
        $this->addArgument('serverId', InputArgument::OPTIONAL, 'A server UUID');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title("Reinstall Server");

        try {
            $serverId = $input->getArgument('serverId');
            if (empty($serverId)) {
                if (!$input->isInteractive()) {
                    $io->error('The serverId argument is required in non interactive mode!');

                    return Command::FAILURE;
                }
            } else {
                return $this->executeReinstall($io, $serverId);
            }

            $serverId = $this->selectServerInteractively($io, $input, $output);
            if ($serverId === null) {
                return Command::FAILURE;
            }

            return $this->executeReinstall($io, $serverId);

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
    private function executeReinstall(SymfonyStyle $io, mixed $serverId): int
    {
        if ($this->pterodactyl->reinstallServer($serverId)) {
            $io->success("Successfully started server reinstall.");

            return Command::SUCCESS;
        }

        $io->error("Failed to start server reinstall");

        return Command::FAILURE;
    }
}
