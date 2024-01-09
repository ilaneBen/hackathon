<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Form\UserType;
use App\Security\LoginAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', 'user_')]
class UserController extends AbstractController
{
    #[Route('/signin', name: 'signin', methods: ['GET'])]
    public function signIn(Request $request, LoginAuthenticator $loginAuthenticator): Response
    {
        $loginAuthenticator->authenticate($request);

        return $this->json(['code' => 200, 'message' => 'Connected']);
    }

    #[Route('/signup', name: 'signup', methods: ['GET', 'POST'])]
    public function signUp(
        Request $request, 
        EntityManagerInterface $em, 
        LoginAuthenticator $loginAuthenticator, 
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        // dd('signup');

        $inputBag = $request->request;
        $email = $inputBag->get('email');
        $password = $inputBag->get('password');
        $firstName = $inputBag->get('firstName');
        $name = $inputBag->get('name');

        if(!$email || !$password || !$firstName || !$name){
            return $this->json(['code' => 400, 'message' => "Bad request"]);
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