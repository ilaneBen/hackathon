<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class PhpStanAnalysisService
{
    /**
     * Exécute l'analyse PHPStan sur le répertoire spécifié.
     *
     * @param string $directory le répertoire sur lequel effectuer l'analyse PHPStan
     *
     * @return Process L'objet Process Symfony représentant l'analyse
     */
    public function runPhpStanAnalysis(string $directory): Process
    {
        $process = new Process(['../../vendor/bin/phpstan', '--error-format=json', 'analyse', $directory]);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }
}
