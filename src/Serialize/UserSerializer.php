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

    /**
     * Sérialise un tableau d'utilisateurs en un tableau associatif pour l'affichage ou l'envoi en tant que réponse API.
     *
     * @param array $userArray le tableau d'utilisateurs à sérialiser
     *
     * @return array le tableau associatif représentant les utilisateurs sérialisés
     */
    public function serialize(array $userArray = []): array
    {
        $serializedUsers = [];
        foreach ($userArray as $user) {
            $serializedUsers[] = $this->serializeOne($user);
        }

        return $serializedUsers;
    }

    /**
     * Sérialise un utilisateur en un tableau associatif pour l'affichage ou l'envoi en tant que réponse API.
     *
     * @param user $user L'utilisateur à sérialiser
     *
     * @return array le tableau associatif représentant l'utilisateur sérialisé
     */
    public function serializeOne(User $user): array
    {
        $serializedUser = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'name' => $user->getName(),
            'firstName' => $user->getFirstName(),
        ];

        return $serializedUser;
    }
}
