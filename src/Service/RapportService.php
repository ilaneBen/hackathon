<?php

namespace App\Service;

use App\Entity\Project;
use App\Entity\Rapport;
use App\Repository\RapportRepository;
use Doctrine\ORM\EntityManagerInterface;

class RapportService
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private RapportRepository $rapportRepository
    ) {
    }

    /**
     * Crée un rapport avec les jobs associés pour un projet donné.
     *
     * @param Project $project le projet associé au rapport
     * @param array   $jobs    un tableau de jobs à associer au rapport
     * @param string   $directory    le nom du dossier temporaire
     *
     * @return rapport L'objet Rapport nouvellement créé
     */
    public function createRapport(Project $project, array $jobs, string $directory): Rapport
    {
        $rapport = new Rapport();
        $rapport->setProject($project);
        $rapport->setDate(new \DateTimeImmutable('now'));
        $rapport->setContent('Rapport ' . $rapport->getId());
        $rapport->setTempDir($directory);

        foreach ($jobs as $job) {
            $job->setRapport($rapport);
            $job->setDate(new \DateTimeImmutable('now'));
            $rapport->addJob($job);
        }

        $this->entityManager->persist($rapport);

        return $rapport;
    }

    /**
     * Récupère les stats des rapports depuis la bdd.
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

        $rapports = $this->rapportRepository->findAll();
        $data['nbObjects'] = count($rapports);

        $countSortedRapports = [];
        for ($i = 0; $i < 2; $i++) {
            foreach ($months as $key => $month) {
                $count = count(
                    array_filter(
                        $rapports,
                        static fn (Rapport $rapport) =>
                        intval($rapport->getDate()->format('m')) == $key + 1 &&
                            intval($rapport->getDate()->format('Y')) == intval($now - $i)
                    )
                );

                $countSortedRapports[$now - $i][$month] = $count;
            }
        }

        $data['countSorted'] = $countSortedRapports;

        return $data;
    }
}
