<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class StyleLintService
{
    /**
     * Exécute l'analyse de stylelint sur le répertoire spécifié.
     *
     * @param string $directory le répertoire sur lequel effectuer l'analyse de stylelint
     *
     * @return Process L'objet Process Symfony représentant l'analyse de stylelint
     */
    public function runStyleLintAnalysis(string $directory): Process
    {
        $process = new Process(['../../node_modules/.bin/stylelint', $directory.'/**/*.{css,scss}', '--formatter', 'json',  '--ignore-pattern', 'var/**,', 'vendor/**, ', 'build/**, ', 'node_modules/**',
]);
        $process->setWorkingDirectory($directory);

        $process->run();

        return $process;
    }
}
