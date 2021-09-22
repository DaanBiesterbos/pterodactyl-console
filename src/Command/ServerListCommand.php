<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

#[AsCommand(
    name: 'server:list',
    description: 'List your servers',
)]
class ServerListCommand extends AbstractServerCommand
{
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $servers = $this->pterodactyl->listServers();
            foreach ($servers as $server) {
                $this->renderServerInfo($io, $server);
            }

            $io->success('Command finished successfully.');

            return Command::SUCCESS;

        } catch (UnauthorizedHttpException $e) {
            $io->error("You are not authorized to access this resource. Verify that the PTERODACTYL_API_KEY is correctly configured.");
            return Command::INVALID;
        } catch (NotFoundHttpException $e) {
            $io->error("The URI is invalid. Check your configuration.");
            return Command::INVALID;
        } catch (Throwable $e) {
            $io->error("Unknown error has occurred: {$e->getMessage()} on line {$e->getLine()} in {$e->getFile()}");
        }

        return Command::FAILURE;
    }
}

