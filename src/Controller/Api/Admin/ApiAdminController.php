<?php

namespace App\Controller\Api\Admin;

use App\Entity\Job;
use App\Entity\Project;
use App\Entity\Rapport;
use App\Repository\JobRepository;
use App\Repository\ProjectRepository;
use App\Repository\RapportRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiAdminController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function gitClone(
        ProjectRepository $projectRepository,
        RapportRepository $rapportRepository,
        JobRepository $jobRepository,
        UserRepository $userRepository,
    ): Response {
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
        $now = new \DateTime('now');

        //projets
        $projects = $projectRepository->findAll();
        $data['projectsData']['nbObjects'] = count($projects);

        $countSortedProjects = [];
        foreach ($months as $key => $month) {
            $count = count(
                array_filter(
                    $projects,
                    static fn (Project $project) =>
                    intval($project->getDate()->format('m')) == $key + 1 &&
                        intval($project->getDate()->format('Y')) == intval($now->format('Y'))
                )
            );

            $countSortedProjects[$month] = $count;
        }

        $data['projectsData']['countSorted'] = $countSortedProjects;

        //rapports
        $rapports = $rapportRepository->findAll();
        $data['rapportsData']['nbObjects'] = count($rapports);

        $countSortedRapports = [];
        foreach ($months as $key => $month) {
            $count = count(
                array_filter(
                    $rapports,
                    static fn (Rapport $rapport) =>
                    intval($rapport->getDate()->format('m')) == $key + 1 &&
                        intval($rapport->getDate()->format('Y')) == intval($now->format('Y'))
                )
            );

            $countSortedRapports[$month] = $count;
        }

        $data['rapportsData']['countSorted'] = $countSortedRapports;

        //jobs
        $jobs = $jobRepository->findAll();
        $data['jobsData']['nbObjects'] = count($jobs);

        $countSortedJobs = [];
        foreach ($months as $key => $month) {
            $count = count(
                array_filter(
                    $jobs,
                    static fn (Job $job) =>
                    intval($job->getDate()->format('m')) == $key + 1 &&
                        intval($job->getDate()->format('Y')) == intval($now->format('Y'))
                )
            );

            $countSortedJobs[$month] = $count;
        }

        $data['jobsData']['countSorted'] = $countSortedJobs;

        //users
        $users = $userRepository->findAll();
        $data['usersData']['nbObjects'] = count($users);

        $data['usersData']['podium'] = $userRepository->getPodium();

        foreach ($data['usersData']['podium'] as $key => $value) {
            if ($key === 0) {
                $place = "first";
            } else if ($key === 1) {
                $place = "second";
            }
            if ($key === 2) {
                $place = "third";
            }

            $data['usersData']['podium'][$key] = [
                ...$value,
                'place' => $place,
                'placeNumber' => $key + 1,
            ];
        }

        // dd($userRepository->getPodium());

        // $data['usersData']['podium'] = $podium;

        return $this->json([
            'code' => 200,
            'data' => $data
        ]);
    }
}
