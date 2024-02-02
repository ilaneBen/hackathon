<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project', name: 'project_')]
class AdminProjectController extends AbstractController
{
    #[Route('/{id}', name: 'index', methods: ['GET'])]
    public function dashboard(User $user): Response
    {
        if (!$currentUser = $this->getUser()) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page');
            return $this->redirectToRoute('user_signup');
        }

        if (!in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            $this->addFlash('error', 'Vous n\'avez pas les accès nécessaires pour accéder à cette page');
            return $this->redirectToRoute('user_signup');
        }

        return $this->render('admin/project/index.html.twig', [
            'user' => $user
        ]);
    }
}
