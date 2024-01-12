<?php

namespace App\Serialize;

use App\Entity\User;
use Symfony\Component\Routing\RouterInterface;

class UserSerializer
{
    public function __construct(
        private RouterInterface $router,
        private ProjectSerializer $projectSerializer,
    ) {
    }

    public function serialize(array $userArray = []): array
    {
        $serializedUsers = [];
        foreach ($userArray as $user) {
            $serializedUsers[] = $this->serializeOne($user);
        }

        return $serializedUsers;
    }

    public function serializeOne(User $user): array
    {
        $serializedUser = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'firstName' => $user->getFirstName()
        ];

        return $serializedUser;
    }
}
