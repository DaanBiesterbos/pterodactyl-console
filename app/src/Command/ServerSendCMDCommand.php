<?php

namespace App\Command;

use App\Service\PteroDactylService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Throwable;

#[AsCommand(
    name: 'server:send-cmd',
    description: 'Sends a command to a server',
)]
class ServerSendCMDCommand extends AbstractServerCommand
{
    protected function configure(): void
    {
        $this->addArgument('cmd', InputArgument::REQUIRED, 'A server command');
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

        $io->title("Send server command");

        try {
            $command = $input->getArgument('cmd');
            if (empty($command)) {
                $io->error('The command argument is required!');

                return Command::FAILURE;
            }

            $serverId = $input->getArgument('serverId');
            if (empty($serverId)) {
                if (!$input->isInteractive()) {
                    $io->error('The serverId  argument is required in non interactive mode!');

                    return Command::FAILURE;
                }
            } else {
                return $this->sendCmdToServer($io, $serverId, $command);
            }

            $serverId = $this->selectServerInteractively($io, $input, $output);
            if ($serverId === null) {
                return Command::FAILURE;
            }

            return $this->sendCmdToServer($io, $serverId, $command);

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
    private function sendCmdToServer(SymfonyStyle $io, string $serverId, string $cmd): int
    {
        if ($this->pterodactyl->sendAdminCommand($serverId, $cmd)) {
            $io->success("Command was sent successfully.");

            return Command::SUCCESS;
        }

        $io->error("Failed to send command...");

        return Command::FAILURE;
    }
}
