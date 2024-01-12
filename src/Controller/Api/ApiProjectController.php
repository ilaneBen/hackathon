<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Serialize\ProjectSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project', name: 'project_')]
class ApiProjectController extends AbstractController
{
	public function __construct (
        private ProjectSerializer $projectSerializer
    ) {}

	#[Route('/new', name: 'new', methods: ['POST'])]
	public function new (Request $request, EntityManagerInterface $entityManager): Response
	{
        if(!$user = $this->getUser()){
            return $this->json([
				'code' => 403, 
				'message' => "Il faut être connecté pour accéder à cette ressource",
			]);
        }

        $inputBag = $request->request;

		if (!$inputBag->has('name') || !$inputBag->has('url')) {
            return $this->json([
				'code' => 403, 
				'message' => "Champs manquants",
			]);
        }

		$project = new Project();

        $project->setName($inputBag->get('name'));
        $project->setUrl($inputBag->get('url'));
        $project->setStatut(false);
        $project->setUser($user);

        $entityManager->persist($project);
        $entityManager->flush();

		return $this->json([
			'code' => 200, 
			'message' => 'Le project a été créé.', 
			'project' => $this->projectSerializer->serializeOne($project),
		]);
	}

	#[Route('/{id}/edit', name: 'edit', methods: ['POST'])]
	public function edit (Request $request, Project $project, EntityManagerInterface $entityManager): Response
	{
		// Vérifier si un utilisateur est connecté
		if (!$user = $this->getUser()) {
			return $this->json([
				'code' => 403, 
				'message' => "Il faut être connecté pour accéder à cette ressource",
			]);
		}

		// Vérifier si l'utilisateur actuel est le propriétaire du projet
		if ($user !== $project->getUser()) {
            return $this->json([
				'code' => 403,
				'message' => "Vous n'êtes pas le propriétaire du projet",
			]);
        }

        $inputBag = $request->request;

		if (!$inputBag->has('name') || !$inputBag->has('url')) {
            return $this->json([
				'code' => 403, 
				'message' => "Champs manquants",
			]);
        }

        $project->setName($inputBag->get('name'));
        $project->setUrl($inputBag->get('url'));

        $entityManager->flush();

		return $this->json([
			'code' => 200, 
			'message' => 'Le project a été modifié.',
		]);
	}

	#[Route('/{id}', name: 'delete', methods: ['POST'])]
	public function delete (Request $request, Project $project, EntityManagerInterface $entityManager): Response
	{
		// Vérifier si un utilisateur est connecté
		if (!$user = $this->getUser()) {
			return $this->json([
				'code' => 403, 
				'message' => "Il faut être connecté pour accéder à cette ressource",
			]);
		}

		// Vérifier si l'utilisateur actuel est le propriétaire du projet
		if ($user !== $project->getUser()) {
            return $this->json([
				'code' => 403, 
				'message' => "Vous n'êtes pas le propriétaire du projet",
			]);
        }

        $inputBag = $request->request;

		// Vérifier si l'utilisateur actuel est le propriétaire du projet
		if (!$inputBag->has('csrf')) {
            return $this->json([
				'code' => 403, 
				'message' => "Token manquant",
			]);
        }

		if ($this->isCsrfTokenValid('delete' . $project->getId(), $inputBag->get('csrf'))) {
			$entityManager->remove($project);
			$entityManager->flush();
		}

		return $this->json([
			'code' => 200, 
			'message' => 'Le project a été supprimé.',
		]);
    }

}
