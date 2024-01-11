<?php

namespace App\Serialize;

use Symfony\Component\Routing\RouterInterface;

class UserSerializer
{
    public function __construct(
        private RouterInterface $router,
        private ProjectSerializer $projectSerializer,
    ) {}

    public function serialize(array $userArray = []): array
    {
        $serializedUsers = [];
        foreach ($userArray as $user) {
            $serializedUsers[] = [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'firstName' => $user->getFirstName(),
                'job' => $this->projectSerializer->serialize($user->getProject()->getValues()),
                'showUrl' => '',
                'editUrl' => '',
            ];
        }

        return $serializedUsers;
    }
}
