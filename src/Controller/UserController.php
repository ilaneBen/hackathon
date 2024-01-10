<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', 'user_')]
class UserController extends AbstractController
{
    #[Route('/signup', name: 'signup', methods: ['GET'])]
    public function signUp_view(): Response
    {
        return $this->render('user/signup.html.twig');
    }

    #[Route('/signin', name: 'signin', methods: ['GET'])]
    public function signIn_view(): Response
    {
        return $this->render('user/signin.html.twig');
    }

    #[Route('/signout', name: 'signout', methods: ['GET'])]
    public function signout(): void
    {
        return;
    }
}
