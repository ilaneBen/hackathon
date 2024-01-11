<?php

namespace App\Serialize;

use Symfony\Component\Routing\RouterInterface;

class ProjectSerializer
{
    public function __construct(
        private RouterInterface $router,
        private RapportSerializer $rapportSerializer,
    ) {}

    public function serialize(array $projectArray = []): array
    {
        $serializedProjects = [];
        foreach ($projectArray as $project) {
            $serializedProjects[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'url' => $project->getUrl(),
                'statut' => $project->isStatut(),
                'rapport' => $this->rapportSerializer->serialize($project->getRapport()->getValues()),
                'showUrl' => $this->router->generate('project_show', ['id' => $project->getId()]),
                // 'deleteUrl' => $this->router->generate('api_project_delete', ['id' => $project->getId()]),
                // 'editUrl' => $this->router->generate('api_project_edit', ['id' => $project->getId()]),
            ];
        }

        return $serializedProjects;
    }
}
