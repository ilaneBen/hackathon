<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class PhpCsAnalysisService
{
    /**
     * Exécute l'analyse PHP CodeSniffer (PHP CS) sur le répertoire spécifié.
     *
     * @param string $directory le répertoire sur lequel effectuer l'analyse PHP CS
     *
     * @return Process L'objet Process Symfony représentant l'analyse PHP CS
     */
    public function runPhpCsAnalysis(string $directory): Process
    {
        $process = new Process(['../../vendor/bin/phpcs', '--report=json', '--extensions=php', $directory]);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }
}
