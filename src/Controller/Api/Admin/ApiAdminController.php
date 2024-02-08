<?php

namespace App\Controller\Api\Admin;

use App\Service\JobService;
use App\Service\ProjectService;
use App\Service\RapportService;
use App\Service\UserService;
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
        ProjectService $projectService,
        RapportService $rapportService,
        JobService $jobService,
        UserService $userService,
    ): Response {
        $data = [];

        $data['projectsData'] = $projectService->getStats();
        $data['rapportsData'] = $rapportService->getStats();
        $data['jobsData'] = $jobService->getStats();
        $data['usersData'] = $userService->getStats();

        return $this->json([
            'code' => 200,
            'data' => $data,
        ]);
    }
}
