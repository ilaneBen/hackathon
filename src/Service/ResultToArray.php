<?php

namespace App\Service;

use Symfony\Component\Process\Process;

class ResultToArray
{
    /**
     * Convertit la sortie JSON du processus PHP CodeSniffer en tableau.
     *
     * @param Process $process le processus
     *
     * @return array le tableau résultant du décodage JSON
     */
    public function resultToArray(Process $process): array
    {
        // Vérifier si la commande est "yarn audit"
        $expectedCommand = ['yarn', 'audit', '--locked', '--json'];
        $actualCommand = $process->getCommandLine();

        if ($actualCommand === implode(' ', array_map('escapeshellarg', $expectedCommand))) {
            // Récupérer la sortie du processus
            $output = $process->getOutput();

            // Vérifier si la sortie est vide et renvoyer un tableau vide
            if (null === $output || '' === trim($output)) {
                return [];
            }

            // Rassembler toutes les lignes NDJSON en une seule chaîne JSON
            $lines = explode("\n", $output);
            $result = [];

            foreach ($lines as $line) {
                if (!empty($line)) {
                    $result[] = json_decode($line, true);
                }
            }

            return $result;
        } else {
            // Si la commande n'est pas "yarn audit", traiter la sortie comme d'habitude
            $output = $process->getOutput();

            // Vérifier si la sortie est vide et renvoyer un tableau vide
            if (null === $output || '' === trim($output)) {
                return [];
            }

            // Décoder la chaîne JSON en tableau
            $outputData = json_decode($output, true);

            // Vérifier s'il y a une erreur de décodage JSON
            if (null === $outputData && JSON_ERROR_NONE !== json_last_error()) {
                exit('Erreur lors du décodage JSON : '.json_last_error_msg());
            }

            $arrayResult = [];

            // Parcourir les éléments et les ajouter au tableau
            foreach ($outputData as $element) {
                $arrayResult[] = $element;
            }

            // Si aucune faille n'est trouvée, ajouter un message au tableau
            if (empty($arrayResult)) {
                $arrayResult[] = 'Aucune faille trouvée.';
            }

            return $arrayResult;
        }
    }

    public function resultToArrayStyle(Process $process): array
    {
        // Récupérer la sortie du processus
        $output = $process->getErrorOutput();

        // Vérifier si la sortie est vide et renvoyer un tableau vide
        if (null === $output || '' === trim($output)) {
            return [];
        }

        // Décoder la chaîne JSON en tableau
        $outputData = json_decode($output, true);

        // Vérifier s'il y a une erreur de décodage JSON
        if (null === $outputData && JSON_ERROR_NONE !== json_last_error()) {
            exit('Erreur lors du décodage JSON : '.json_last_error_msg());
        }

        $arrayResult = [];

        // Parcourir les éléments et les ajouter au tableau
        foreach ($outputData as $element) {
            $arrayResult[] = $element;
        }

        // Si aucune faille n'est trouvée, ajouter un message au tableau
        if (empty($arrayResult)) {
            $arrayResult[] = 'Aucune faille trouvée.';
        }

        return $arrayResult;
    }
}
