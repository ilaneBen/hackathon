<?php

namespace App\Controller\Api\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Serialize\UserSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/user', 'user_')]
class ApiAdminUserController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserSerializer $userSerializer,
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        // Vérifier si un utilisateur est connecté
        if (!$user = $this->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => 'Il faut être connecté pour accéder à cette ressource',
            ]);
        }

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json([
                'code' => 403,
                'message' => 'Vous n\'avez pas les droits nécessaires pour accéder à cette ressource',
            ]);
        }

        $users = $userRepository->exceptConnected($user->getId());

        return $this->json([
            'code' => 200,
            'users' => $this->userSerializer->serialize($users),
        ]);
    }

    #[Route('/new', name: 'new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si un utilisateur est connecté
        if (!$user = $this->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => 'Il faut être connecté pour accéder à cette ressource',
            ]);
        }

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->json([
                'code' => 403,
                'message' => 'Vous n\'avez pas les droits nécessaires pour accéder à cette ressource',
            ]);
        }

        $inputBag = $request->request;

        $email = $inputBag->get('email');
        $name = $inputBag->get('name');
        $firstName = $inputBag->get('firstName');
        $password = $inputBag->get('password');

        if (!$email || !$name || !$firstName || !$password) {
            return $this->json([
                'code' => 403,
                'message' => 'Champs manquants',
            ]);
        }

        $user = new User();

        $user->setName($name);
        $user->setFirstName($firstName);
        $user->setEmail($email);

        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $inputBag->get('password')
        );

        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json([
            'code' => 200,
            'user' => $this->userSerializer->serializeOne($user),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si un utilisateur est connecté
        if (!$currentUser = $this->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => 'Il faut être connecté pour accéder à cette ressource',
            ]);
        }

        if (!in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json([
                'code' => 403,
                'message' => 'Vous n\'avez pas les droits nécessaires pour accéder à cette ressource',
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
            'user' => $this->userSerializer->serializeOne($user),
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(User $user, Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si un utilisateur est connecté
        if (!$currentUser = $this->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => 'Il faut être connecté pour accéder à cette ressource',
            ]);
        }

        if (!in_array('ROLE_ADMIN', $currentUser->getRoles())) {
            return $this->json([
                'code' => 403,
                'message' => 'Vous n\'avez pas les droits nécessaires pour accéder à cette ressource',
            ]);
        }

        if (!$user) {
            return $this->json([
                'code' => 400,
                'message' => "Cet utilisateur n'existe pas",
            ]);
        }

        $inputBag = $request->request;

        // Vérifier si le token CSRF est valide
        if (!$inputBag->has('deleteCsrf')) {
            return $this->json([
                'code' => 403,
                'message' => 'Token manquant',
            ]);
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $inputBag->get('deleteCsrf'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->json([
            'code' => 200,
            'message' => "L'utilisateur a bien été supprimé",
        ]);
    }
}
