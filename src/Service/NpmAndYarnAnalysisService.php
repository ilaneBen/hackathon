<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class NpmAndYarnAnalysisService
{
    /**
     * Exécute l'audit de sécurité npm ou yarn sur le répertoire spécifié.
     *
     * @param string $directory le répertoire sur lequel effectuer l'audit de sécurité
     *
     * @return Process L'objet Process Symfony représentant l'audit de sécurité
     */
    public function runAudit(string $directory): Process
    {
        if (file_exists($directory.'/yarn.lock')) {
            return $this->runYarnAudit($directory);
        } else {
            return $this->runNpmAudit($directory);
        }
    }

    /**
     * Exécute l'audit de sécurité npm sur le répertoire spécifié.
     *
     * @param string $directory le répertoire sur lequel effectuer l'audit de sécurité npm
     *
     * @return Process L'objet Process Symfony représentant l'audit de sécurité npm
     */
    private function runNpmAudit(string $directory): Process
    {
        $process = new Process(['npm', 'audit', '--locked', '--json']);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }

    /**
     * Exécute l'audit de sécurité yarn sur le répertoire spécifié.
     *
     * @param string $directory le répertoire sur lequel effectuer l'audit de sécurité yarn
     *
     * @return Process L'objet Process Symfony représentant l'audit de sécurité yarn
     */
    private function runYarnAudit(string $directory): Process
    {
        $process = new Process(['yarn', 'audit', '--locked', '--json']);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }

    public function isYarnAuditCommand(Process $actualCommand): string
    {
        $expectedCommand = ['yarn', 'audit', '--locked', '--json'];
        $actualCommandString = $actualCommand->getCommandLine();

        if ($actualCommandString === implode(' ', array_map('escapeshellarg', $expectedCommand))) {
            return $name = 'yarn audit';
        } else {
            return $name = 'npm audit';
        }
    }
}
