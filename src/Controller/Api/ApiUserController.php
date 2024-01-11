<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', 'user_')]
class ApiUserController extends AbstractController
{
    #[Route('/signin', name: 'signin', methods: ['POST'])]
    public function signIn(): Response
    {
        return $this->json(['code' => 200, 'message' => 'Connected']);
    }

    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signUp(): Response
    {
        return $this->json(['code' => 200, 'message' => 'User created and connected']);
    }
}
