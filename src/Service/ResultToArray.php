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
        // Récupérer la sortie du processus
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

    public function resultToArrayJs(Process $process): array
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
