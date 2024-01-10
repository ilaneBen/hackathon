<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
    public function signUp(
        Request $request,
        EntityManagerInterface $em,
        LoginAuthenticator $loginAuthenticator,
        UserPasswordHasherInterface $passwordHasher
    ): Response {

        $inputBag = $request->request;
        $email = $inputBag->get('email');
        $password = $inputBag->get('password');
        $firstName = $inputBag->get('firstName');
        $name = $inputBag->get('name');

        if (!$email || !$password || !$firstName || !$name) {
            return $this->json(['code' => 400, 'message' => "Veuillez remplir tous les champs."]);
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $email]);
        if ($user) {
            return $this->json(['code' => 400, 'message' => "Un utilisateur avec cet email existe déjà."]);
        }

        $user = new User;
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $password
        );

        $user->setEmail($email);
        $user->setPassword($hashedPassword);
        $user->setFirstName($firstName);
        $user->setName($name);

        $em->persist($user);
        $em->flush();

        $loginAuthenticator->authenticate($request);

        return $this->json(['code' => 200, 'message' => "User created and connected"]);
    }
}
