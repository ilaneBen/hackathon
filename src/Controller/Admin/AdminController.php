<?php

namespace App\Controller\Admin;

use App\Entity\Job;
use App\Entity\Project;
use App\Entity\Rapport;
use App\Repository\JobRepository;
use App\Repository\ProjectRepository;
use App\Repository\RapportRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/', name: 'dashboard', methods: ['GET'])]
    public function dashboard(): Response
    {
        if (!$user = $this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('user_signin');
        }

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->addFlash('error', 'Vous n\'avez pas les accès nécessaires pour accéder à cette page');
            return $this->redirectToRoute('user_signin');
        }



        return $this->render('admin/index.html.twig', [
            'message' => sprintf("Bienvenue %s %s", $user->getName(), $user->getFirstName()),
        ]);
    }
}
