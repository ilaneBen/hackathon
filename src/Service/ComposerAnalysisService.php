<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class ComposerAnalysisService
{
    /**
     * Exécute l'audit de sécurité Composer sur le répertoire spécifié.
     *
     * @param string $directory le répertoire sur lequel effectuer l'audit de sécurité Composer
     *
     * @return process L'objet Process Symfony représentant l'audit de sécurité Composer
     */
    public function runComposerAudit(string $directory): Process
    {
        $process = new Process(['composer', 'audit', '--locked', '--format=json']);
        $process->setWorkingDirectory($directory);
        $process->run();

        return $process;
    }
}
