<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class GitCloningService
{
    /**
     * Clone un dépôt Git depuis une URL vers une destination spécifiée.
     *
     * @param string $repositoryUrl L'URL du dépôt Git à cloner
     * @param string $destination   le chemin de destination où le dépôt sera cloné
     *
     * @return process L'objet Process Symfony représentant la commande de clonage
     */
    public function cloneRepository(string $repositoryUrl, string $destination): Process
    {
        $process = new Process(['git', 'clone', $repositoryUrl, $destination]);
        $process->run();

        return $process;
    }
}
