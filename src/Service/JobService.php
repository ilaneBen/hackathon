<?php

namespace App\Service;

use App\Entity\Job;
use App\Entity\Project;
use Doctrine\ORM\EntityManagerInterface;

class JobService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Crée un objet Job associé à un projet avec des informations spécifiées.
     *
     * @param Project $project le projet auquel le job est associé
     * @param string  $name    le nom du job
     * @param array   $output  la sortie du job
     *
     * @return Job L'objet Job nouvellement créé
     */
    public function createJob(Project $project, string $name, array $output, bool $resultat): Job
    {
        $job = new Job();
        $job->setName($name);
        $job->setProject($project);
        $job->setResultat(empty($output));
        $job->setDetail(['result' => $output]);
        $job->setResultat($resultat);

        $this->entityManager->persist($job);

        return $job;
    }
}
