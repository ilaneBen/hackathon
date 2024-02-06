<?php

namespace App\Security;

use App\Service\UserService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

class LoginAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return $request->request->has('csrf');
    }

    public function authenticate(Request $request): Passport
    {
        $inputBag = $request->request;

        $password = $inputBag->get('password');
        $csrfToken = $inputBag->get('csrf');
        $type = $inputBag->get('type');

        $user = $this->userService->authenticateUser($request);

        return new Passport(
            new UserBadge($user->getEmail()),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge($type, $csrfToken),
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
            'message' => strtr($exception->getMessage(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
