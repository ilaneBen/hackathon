<?php

namespace App\Model;

use App\Entity\Job;

class JobViewModel
{
    public function __construct(private Job $job)
    {
    }

    public function getId(): int
    {
        return $this->job->getId();
    }

    public function getName(): string
    {
        return $this->job->getName();
    }

    public function getResultat(): bool
    {
        return $this->job->isResultat();
    }

    public function getProjectName(): string
    {
        return $this->job->getProject()->getName();
    }

    public function getDetails(): array
    {
        $details = $this->job->getDetail();

        switch ($this->job->getName()) {
            case 'Composer Audit':
                return [
                    'Version' => $details['version'] ?? 'Non spécifié',
                ];

            case 'PHP STAN':
                return [
                ];

            case 'PHP Version':
                return [
                    'Version minimale requise' => $details['result'][0] ?? 'Non spécifié',
                ];

            case 'PHP Cs':
                $phpCsResults = [];

                foreach ($details['result'] as $index => $result) {
                    $files = [];

                    foreach ($result as $filePath => $fileDetails) {
                        if ('errors' !== $filePath && 'warnings' !== $filePath && 'fixable' !== $filePath) {
                            $files[] = [
                                'Chemin du fichier' => $filePath,
                                'Erreurs' => $fileDetails['errors'] ?? 0,
                                'Avertissements' => $fileDetails['warnings'] ?? 0,
                                'Messages' => $fileDetails['messages'] ?? [],
                            ];
                        }
                    }

                    $phpCsResults[] = [
                        'Index' => $index + 1,
                        'Erreurs' => $result['errors'] ?? 0,
                        'Avertissements' => $result['warnings'] ?? 0,
                        'Réparable' => $result['fixable'] ?? 0,
                        'Fichiers' => $files,
                        // Ajoutez d'autres éléments si nécessaire
                    ];
                }

                return [
                    'Résultats de PHP Cs' => $phpCsResults,
                ];

            default:
                // Si le type de job n'est pas géré, retournez simplement les détails bruts
                return $details;
        }
    }
}
