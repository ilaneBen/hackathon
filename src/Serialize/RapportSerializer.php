<?php

namespace App\Serialize;

use App\Entity\Rapport;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;


class RapportSerializer
{

    public function __construct(
        private RouterInterface $router,
        private CsrfTokenManagerInterface $csrf

    ) {}

    public function serialize(array $rapportArray = []): array
    {
        $serializedRapports = [];
        foreach ($rapportArray as $rapport) {
            $serializedRapports[] = $this->serializeOne($rapport);
        }

        return $serializedRapports;
    }

    public function serializeOne(Rapport $rapport): array
    {
        $serializedRapport = [
            'id' => $rapport->getId(),
            'date' => $rapport->getDate()->format('d-m-Y H:i'),
            'showUrl' => $this->router->generate('app_rapport_show', ['id'=>$rapport->getId()]),
            'deleteProject' => $this->router->generate('app_rapport_delete', ['id'=>$rapport->getId()]),
            'deleteCsrf' => $this->csrf->refreshToken('delete' . $rapport->getId())->getValue(),

            // 'showUrl' => $this->router->generate('api_rapport_show', ['id' => $rapport->getId()]),
        ];
        
        return $serializedRapport;
    }
}
