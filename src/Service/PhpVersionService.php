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
        // Supposant que le chemin vers le fichier composer.json est correct
        $composerJsonPath = realpath($directory.'/composer.json');

        // Lire le contenu de composer.json
        $composerJsonContents = file_get_contents($composerJsonPath);

        // Gérer le cas où la lecture échoue
        if (false === $composerJsonContents) {
            return null;
        }

        // Décoder le contenu JSON
        $composerData = json_decode($composerJsonContents, true);

        // Gérer les erreurs de décodage JSON
        if (JSON_ERROR_NONE !== json_last_error()) {
            return null;
        }

        // Récupérer la version PHP depuis la section "require"
        return [$composerData['require']['php'] ?? null];
    }
}
