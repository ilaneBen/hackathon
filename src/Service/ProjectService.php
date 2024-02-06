<?php

namespace App\Service;

use App\Entity\Project;
use App\Repository\ProjectRepository;

class ProjectService
{
    public function __construct(
        private ProjectRepository $projectRepository
    ) {
    }

    /**
     * Récupère les stats des projets depuis la bdd.
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

        //projets
        $projects = $this->projectRepository->findAll();
        $data['nbObjects'] = count($projects);

        $countSortedProjects = [];
        for ($i = 0; $i < 2; $i++) {
            foreach ($months as $key => $month) {
                $count = count(
                    array_filter(
                        $projects,
                        static fn (Project $project) =>
                        intval($project->getDate()->format('m')) == $key + 1 &&
                            intval($project->getDate()->format('Y')) == intval($now - $i)
                    )
                );

                $countSortedProjects[$now - $i][$month] = $count;
            }
        }


        $data['countSorted'] = $countSortedProjects;

        return $data;
    }
}
