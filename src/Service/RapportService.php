<?php

namespace App\Service;

use App\Entity\Job;
use App\Entity\Project;
use App\Entity\Rapport;
use Doctrine\ORM\EntityManagerInterface;

class RapportService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createJob(Project $project, string $name, string $output): Job
    {
        $job = new Job();
        $job->setName($name);
        $job->setProject($project);
        $job->setResultat(empty($output));
        $job->setDetail(['result' => $output]);

        $this->entityManager->persist($job);

        return $job;
    }

    /**
     * Obtient la version PHP à partir du fichier composer.json dans le répertoire spécifié.
     *
     * @param string $directory Le répertoire du projet
     *
     * @return string|null La version PHP ou null en cas d'erreur
     */
    public function getPhpVersionFromComposerJson(string $directory): ?string
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
        return $composerData['require']['php'] ?? null;
    }

    public function createRapport(Project $project, array $jobs): Rapport
    {
        $rapport = new Rapport();
        $rapport->setProject($project);
        $rapport->setDate(new \DateTimeImmutable('now'));
        $rapport->setContent('Rapport '.$rapport->getId());

        foreach ($jobs as $job) {
            $job->setRapport($rapport);
            $rapport->addJob($job);
        }

        $this->entityManager->persist($rapport);

        return $rapport;
    }
}
