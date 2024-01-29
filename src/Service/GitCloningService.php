<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Process;

class GitCloningService
{
    /**
     * Clone un dépôt Git depuis une URL vers une destination spécifiée.
     *
     * @param string $repositoryUrl L'URL du dépôt Git à cloner
     * @param string $destination   le chemin de destination où le dépôt sera cloné
     *
     * @return Process L'objet Process Symfony représentant la commande de clonage
     */
    public function cloneRepository(string $repositoryUrl, string $destination): Process
    {
        $process = new Process(['git', 'clone', $repositoryUrl, $destination]);
        $process->run();

        return $process;
    }

    /**
     * Nettoie le répertoire cloné en supprimant les fichiers et dossiers avec les pleins accès.
     *
     * @param string $cloneDirectory le chemin du répertoire cloné à nettoyer
     */
    public function cleanCloneDirectory(string $cloneDirectory): void
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($cloneDirectory)) {
            $filesystem->chmod($cloneDirectory, 0777, 0000, true);
            $filesystem->remove($cloneDirectory);
        }
    }
}
