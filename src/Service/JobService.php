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
     * @param string  $output  la sortie du job
     *
     * @return job L'objet Job nouvellement créé
     */
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
}