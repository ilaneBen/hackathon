<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

class LoginAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoginAuthenticator $loginAuthenticator,
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
    )
    {
        
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-AUTH-TOKEN') || $request->request->get('csrf');
        // return true;
    }

    public function authenticate(Request $request): Passport
    {
        $inputBag = $request->request;

        $email = $inputBag->get('email');
        $password = $inputBag->get('password');
        $firstName = $inputBag->get('firstName');
        $name = $inputBag->get('name');
        $csrfToken = $inputBag->get('csrf');

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        if ($email && $password) {
            $user = $this->userRepository->findOneBy(['email' => $email]);

            if (!$user && $firstName && $name) {
                $inputBag = $request->request;

                $user = new User;
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
        }else{
            $email = $password = "";
        }

        return new Passport(
            new UserBadge($email),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('signin', $csrfToken),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'code' => 403,
            'message' => 'Unauthorized: ' . strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
