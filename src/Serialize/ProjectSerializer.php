<?php

namespace App\Serialize;

use App\Entity\Project;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ProjectSerializer
{
    public function __construct(
        private RouterInterface $router,
        private RapportSerializer $rapportSerializer,
        private CsrfTokenManagerInterface $csrf
    ) {
    }

    public function serialize(array $projectArray = []): array
    {
        $serializedProjects = [];
        foreach ($projectArray as $project) {
            $serializedProjects[] = $this->serializeOne($project);
        }

        return $serializedProjects;
    }

    public function serializeOne(Project $project): array
    {
        $serializedProject = [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'url' => $project->getUrl(),
            'statut' => $project->isStatut(),
            'rapport' => $this->rapportSerializer->serialize($project->getRapport()->getValues()),
            'showUrl' => $this->router->generate('project_show', ['id' => $project->getId()]),
            'deleteUrl' => $this->router->generate('api_project_delete', ['id' => $project->getId()]),
            'editUrl' => $this->router->generate('api_project_edit', ['id' => $project->getId()]),
            'deleteCsrf' => $this->csrf->refreshToken('delete' . $project->getId())->getValue(),
        ];

        return $serializedProject;
    }
}
