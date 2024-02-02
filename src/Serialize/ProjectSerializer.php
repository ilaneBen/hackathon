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

    /**
     * Sérialise un tableau de projets en un tableau associatif pour l'affichage ou l'envoi en tant que réponse API.
     *
     * @param array $projectArray le tableau de projets à sérialiser
     *
     * @return array le tableau associatif représentant les projets sérialisés
     */
    public function serialize(array $projectArray = []): array
    {
        $serializedProjects = [];
        foreach ($projectArray as $project) {
            $serializedProjects[] = $this->serializeOne($project);
        }

        return $serializedProjects;
    }

    /**
     * Sérialise un projet en un tableau associatif pour l'affichage ou l'envoi en tant que réponse API.
     *
     * @param Project $project le projet à sérialiser
     *
     * @return array le tableau associatif représentant le projet sérialisé
     */
    public function serializeOne(Project $project): array
    {
        $serializedProject = [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'url' => $project->getUrl(),
            'rapport' => $this->rapportSerializer->serialize($project->getRapport()->getValues()),
            'indexUrl' => $this->router->generate('project_index'),
            'showUrl' => $this->router->generate('project_show', ['id' => $project->getId()]),
            'deleteUrl' => $this->router->generate('api_project_delete', ['id' => $project->getId()]),
            'editUrl' => $this->router->generate('api_project_edit', ['id' => $project->getId()]),
            'adminEdit' => $this->router->generate('api_admin_project_edit', ['id' => $project->getId()]),
            'adminDelete' => $this->router->generate('api_admin_project_delete', ['id' => $project->getId()]),
            'deleteCsrf' => $this->csrf->refreshToken('delete' . $project->getId())->getValue(),
            'cloneUrl' => $this->router->generate('api_git_clone', ['project' => $project->getId()]),
        ];

        return $serializedProject;
    }
}
