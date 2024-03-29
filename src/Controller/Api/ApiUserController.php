<?php

namespace App\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', 'user_')]
class ApiUserController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    #[Route('/signin', name: 'signin', methods: ['POST'])]
    public function signIn(): Response
    {
        return $this->json([
            'code' => 200,
            'message' => 'Connected',
            'role' => $this->getUser()->getRoles()[0],
        ]);
    }

    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signUp(): Response
    {
        return $this->json([
            'code' => 200,
            'message' => 'User created and connected',
        ]);
    }

    #[Route('/user/edit', name: 'edit', methods: ['POST'])]
    public function edit(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si un utilisateur est connecté
        if (!$user = $this->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => 'Il faut être connecté pour accéder à cette ressource',
            ]);
        }

        $inputBag = $request->request;

        $email = $inputBag->get('email');
        $name = $inputBag->get('name');
        $firstName = $inputBag->get('firstName');

        if (!$email || !$name || !$firstName) {
            return $this->json([
                'code' => 403,
                'message' => 'Champs manquants',
            ]);
        }

        $user->setName($name);
        $user->setFirstName($firstName);
        $user->setEmail($email);

        if ($inputBag->has('password') && strlen($inputBag->get('password'))) {
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $inputBag->get('password')
            );

            $user->setPassword($hashedPassword);
        }

        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'message' => "L'utilisateur a bien été modifié.",
        ]);
    }
}
