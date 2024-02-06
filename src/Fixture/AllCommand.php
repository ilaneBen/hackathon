<?php

namespace App\Fixture;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

class AllCommand extends Command
{
    protected static $defaultName = 'app:create-all';

    protected function configure()
    {
        $this->setDescription('Exécute les commandes pour créer des rapports, des jobs, des projets et des utilisateurs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        // Exécutez la commande pour créer des utilisateurs.
        $this->runCommand('app:create-users', $io);

        // Exécutez la commande pour créer des projets.
        $this->runCommand('app:create-projects', $io);

        // Exécutez la commande pour créer des rapports.
        $this->runCommand('app:create-rapports', $io);

        // Exécutez la commande pour créer des jobs.
        $this->runCommand('app:create-jobs', $io);

        $io->success('Toutes les commandes ont été exécutées avec succès.');

        return Command::SUCCESS;
    }

    private function runCommand($command, SymfonyStyle $io)
    {
        $process = new Process(['php', 'bin/console', $command]);
        $process->run();

        if (!$process->isSuccessful()) {
            $io->error(sprintf('Erreur lors de l\'exécution de la commande "%s".', $command));
            $io->error($process->getErrorOutput());
            exit(1); // Vous pouvez ajuster cette sortie en fonction de vos besoins.
        }

        $io->success(sprintf('La commande "%s" a été exécutée avec succès.', $command));
    }
}
