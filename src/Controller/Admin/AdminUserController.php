<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', name: 'user_')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
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

        return $this->render('admin/user/index.html.twig', []);
    }
}
