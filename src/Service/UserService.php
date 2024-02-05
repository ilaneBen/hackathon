<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class UserService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    ) {
    }

    /**
     * Connecte l'utilisateur et/ou l'inscrit
     *
     * @param Request $request la requête
     *
     * @return User l'utilisateur connecté
     */
    public function authenticateUser(Request $request): User
    {

        $inputBag = $request->request;

        $email = $inputBag->get('email');
        $password = $inputBag->get('password');
        $firstName = $inputBag->get('firstName');
        $name = $inputBag->get('name');
        $type = $inputBag->get('type');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        if (!$email || !$password || !$type) {
            throw new AuthenticationException('Identifiants manquants');
        }

        $user = $this->userRepository->findOneBy(['email' => $email]);

        if ('signup' === $type) {
            if (!$firstName || !$name) {
                throw new AuthenticationException('Identifiants manquants');
            }
            if ($user) {
                throw new AuthenticationException('L\'utilisateur existe déjà');
            }
        } elseif ('signin' === $type) {
            if (!$user) {
                throw new AuthenticationException('Identifiants invalides');
            }
        }

        if ('signup' === $type && !$user) {
            $inputBag = $request->request;

            $user = new User();
            $hashedPassword = $this->passwordHasher->hashPassword(
                $user,
                $password
            );

            $user->setEmail($email);
            $user->setPassword($hashedPassword);
            $user->setFirstName($firstName);
            $user->setName($name);

            $this->em->persist($user);
            $this->em->flush();
        }

        return $user;
    }
}
