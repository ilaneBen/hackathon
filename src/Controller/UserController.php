<?php

namespace App\Controller;

use App\Serialize\UserSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', 'user_')]
class UserController extends AbstractController
{
    public function __construct(
        private UserSerializer $userSerializer
    ) {
    }

    #[Route('/signup', name: 'signup', methods: ['GET'])]
    public function signUp_view(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('user/signup.html.twig');
    }

    #[Route('/signin', name: 'signin', methods: ['GET'])]
    public function signIn_view(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('user/signin.html.twig');
    }

    #[Route('/account', name: 'account', methods: ['GET'])]
    public function myAccount_view(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('user_signin');
        }

        $user = $this->userSerializer->serializeOne($this->getUser());

        return $this->render('user/account.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/signout', name: 'signout', methods: ['GET'])]
    public function signout(): void
    {
        return;
    }
}
