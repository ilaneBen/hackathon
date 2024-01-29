<?php

namespace App\Service;

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

    /**
     * Crée un rapport avec les jobs associés pour un projet donné.
     *
     * @param Project $project le projet associé au rapport
     * @param array   $jobs    un tableau de jobs à associer au rapport
     *
     * @return rapport L'objet Rapport nouvellement créé
     */
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
