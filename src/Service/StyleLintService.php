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
        $stylelintExecutable = $this->getStyleLintExecutablePath();

        $process = new Process([$stylelintExecutable, $directory.'/**/*.{css,scss}', '--formatter', 'json']);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }

    /**
     * Obtient le chemin de l'exécutable de stylelint.
     *
     * @return string le chemin de l'exécutable de stylelint
     */
    private function getStyleLintExecutablePath(): string
    {
        // Ajoutez ici la logique pour obtenir le chemin de l'exécutable de stylelint
        // Assurez-vous d'avoir installé stylelint dans votre projet via npm

        return 'node_modules/.bin/stylelint';
    }
}
