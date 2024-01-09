<?php

namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class GitCloneController extends AbstractController
{
	#[Route('/git/clone', name: 'app_git_clone')]
	public function index(): Response
	{
		// Message pour affichage dans la vue
		$message = 'Bienvenue sur la page de clonage de dépôt Git !';

		// Renvoyer une réponse avec une vue Twig
		return $this->render('project/show.html.twig', [
			'message' => $message,
		]);
	}

	#[Route('/git-clone', name: 'git_clone')]
	public function gitClone(Request $request): Response
	{
		// Récupérer l'URL du dépôt Git depuis la requête
		$repositoryUrl = $request->query->get('repository');

		// Vérifier si une URL de dépôt a été fournie
		if (!$repositoryUrl) {
			return new Response('Aucune URL de dépôt spécifiée.', Response::HTTP_BAD_REQUEST);
		}

		// Validation de l'URL Git
		/*if (!$this->isValidGitUrl($repositoryUrl)) {
			return new Response('L\'URL du dépôt n\'est pas valide.', Response::HTTP_BAD_REQUEST);
		}*/

		// Construction de la commande git clone
		$command = "git clone $repositoryUrl";

		// Créer un processus pour exécuter la commande
		$process = Process::fromShellCommandline($command);

		try {
			// Exécuter la commande git clone
			$process->mustRun();

			// Récupérer la sortie de la commande
			$output = $process->getOutput();

			// Renvoyer la sortie comme réponse
			 new Response($output);

			 $message ="";
			return $this->render('project/show.html.twig', [
				'message' => $message,
			]);
		} catch (ProcessFailedException $exception) {
			// En cas d'échec, récupérer et renvoyer l'erreur
			return new Response($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}

	private function isValidGitUrl($url)
	{
		// Expression régulière pour valider l'URL Git
		$regex = '/^(github|https?|ssh):\/\/[^\s@]+(@|:\/\/)([^\/:]+)[:\/]Leslie\/([^\/:]+)\/(.+).git$/i';


		// Vérification avec la regex
		return preg_match($regex, $url);
	}
}
