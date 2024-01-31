<?php

namespace App\Service;

class PhpVersionService
{
    /**
     * Obtient la version PHP à partir du fichier composer.json dans le répertoire spécifié.
     *
     * @param string $directory Le répertoire du projet
     *
     * @return string|null La version PHP ou null en cas d'erreur
     */
    public function getPhpVersionFromComposerJson(string $directory): ?array
    {
        $composerJsonPath = realpath($directory.'/composer.json');

        if (!file_exists($composerJsonPath)) {
            return ['Pas de fichier composer.json'];
        }

        $composerJsonContents = file_get_contents($composerJsonPath);

        if (false === $composerJsonContents) {
            return null;
        }

        $composerData = json_decode($composerJsonContents, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            return null;
        }

        return [$composerData['require']['php'] ?? null];
    }
}
