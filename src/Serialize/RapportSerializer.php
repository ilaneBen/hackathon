<?php

namespace App\Serialize;

use Symfony\Component\Routing\RouterInterface;

class RapportSerializer
{
    public function __construct(
        private RouterInterface $router,
    ) {}

    public function serialize(array $rapportArray = []): array
    {
        $serializedRapports = [];
        foreach ($rapportArray as $rapport) {
            $serializedRapports[] = [
                'id' => $rapport->getId(),
                'date' => $rapport->getDate()->format('d-m-Y H:i'),
                // 'showUrl' => $this->router->generate('api_rapport_show', ['id' => $rapport->getId()]),
            ];
        }

        return $serializedRapports;
    }
}
