<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/git', name: 'git_')]
class GitCloneController extends AbstractController
{
	#[Route('/clone', name: 'clone')]
	public function index(): Response
	{
		// Message pour affichage dans la vue
		$message = 'Bienvenue sur la page de clonage de dépôt Git !';

		// Renvoyer une réponse avec une vue Twig
		return $this->render('project/show.html.twig', [
			'message' => $message,
		]);
	}
}
