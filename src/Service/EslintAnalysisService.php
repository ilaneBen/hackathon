<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class EslintAnalysisService
{
    /**
     * Exécute l'analyse Eslint sur le répertoire spécifié.
     *
     * @param string $directory le répertoire sur lequel effectuer l'analyse Eslint
     *
     * @return Process L'objet Process Symfony représentant l'analyse Eslint
     */
    public function runEslintAnalysis(string $directory): Process
    {
        $process = new Process(['../../node_modules/.bin/eslint', $directory, '-f', 'json']);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }
}
