<?php

namespace App\Service;

use App\Entity\Job;
use App\Entity\Project;
use App\Repository\JobRepository;
use Doctrine\ORM\EntityManagerInterface;

class JobService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private JobRepository $jobRepository
    ) {
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

    /**
     * Récupère les stats des jobs depuis la bdd.
     *
     * @return array le tableau de données
     */
    public function getStats(): array
    {
        $data = [];

        $months = [
            'Janvier',
            'Février',
            'Mars',
            'Avril',
            'Mai',
            'Juin',
            'Juillet',
            'Août',
            'Septembre',
            'Octobre',
            'Novembre',
            'Décembre',
        ];
        $now = intval((new \DateTime('now'))->format('Y'));

        $jobs = $this->jobRepository->findAll();
        $data['nbObjects'] = count($jobs);

        $countSortedJobs = [];
        for ($i = 0; $i < 2; $i++) {
            foreach ($months as $key => $month) {
                $count = count(
                    array_filter(
                        $jobs,
                        static fn (Job $job) =>
                        intval($job->getDate()->format('m')) == $key + 1 &&
                            intval($job->getDate()->format('Y')) == intval($now - $i)
                    )
                );

                $countSortedJobs[$now - $i][$month] = $count;
            }
        }

        $data['countSorted'] = $countSortedJobs;

        return $data;
    }
}
