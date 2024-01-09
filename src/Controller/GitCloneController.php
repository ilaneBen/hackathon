<?php

namespace App\Controller;
use App\Entity\Job;
use App\Entity\Rapport;
use Doctrine\ORM\EntityManagerInterface;
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
	public function gitClone(Request $request, EntityManagerInterface $entityManager): Response
	{
		// Récupérer l'URL du dépôt Git depuis la requête
		$repositoryUrl = $request->query->get('repository');

		// Vérifier si une URL de dépôt a été fournie
		if (!$repositoryUrl) {
			return new Response('Aucune URL de dépôt spécifiée.', Response::HTTP_BAD_REQUEST);
		}

		// Chemin relatif du répertoire de destination
		$destination = 'repoClone'; // Utiliser un chemin relatif par rapport à la racine du projet

		// Créer un processus pour exécuter la commande git clone dans le répertoire spécifié
		$process = new Process(["git", "clone", $repositoryUrl, $destination]);

		try {
			// Exécuter la commande git clone
			$process->mustRun();

			// Exécuter et enregistrer la commande Composer Audit
			$composerAuditOutput = $this->executeComposerAudit($destination);
			$php_vOutput = $this->executePHP_V($destination);

			// Préparer le détail pour le stockage
			$detailComposerAudit = empty($composerAuditOutput) ? ['result' => 'Aucune faille'] : ['result' => $composerAuditOutput];
			$detailPhp_v = empty($php_vOutput) ? ['result' => 'Aucune faille'] : ['result' => $php_vOutput];

			// Enregistrer le résultat de Composer Audit en tant que job
			$composerAuditJob = new Job();
			$composerAuditJob->setName('Composer Audit');
			$composerAuditJob->setResultat(empty($composerAuditOutput) ? 'Aucune faille' : $composerAuditOutput);
			$composerAuditJob->setDetail($detailComposerAudit); // Assigner le détail sous forme de tableau
			$entityManager->persist($composerAuditJob);
			$entityManager->flush();

			$Php_vJob = new Job();
			$Php_vJob->setName('Php Version');
			$Php_vJob->setResultat(empty($php_vOutput) ? 'Aucune faille' : $php_vOutput);
			$Php_vJob->setDetail($detailPhp_v); // Assigner le détail sous forme de tableau
			$entityManager->persist($Php_vJob);
			$entityManager->flush();


			//$raport = new Rapport();

			// Renvoyer la sortie comme réponse
			$message = "Clonage du dépôt Git réussi.";
			return $this->render('git_clone/resultat.html.twig', [
				'message' => $message,
			]);
		} catch (ProcessFailedException $exception) {
			// En cas d'échec du clonage ou de Composer Audit, récupérer et renvoyer l'erreur
			return new Response($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
		}
	}


	private function executeComposerAudit(): string
	{
		$composerAuditCommand = 'composer audit --locked --format=json';

		// Créer un processus pour exécuter la commande Composer Audit dans le répertoire cloné
		$process = Process::fromShellCommandline($composerAuditCommand);

		try {
			// Exécuter la commande Composer Audit
			$process->mustRun();

			// Récupérer la sortie de la commande
			return $process->getOutput();
		} catch (ProcessFailedException $exception) {
			// En cas d'échec de Composer Audit, retourner un message d'erreur
			return $exception->getMessage();
		}
	}
	private function executePHP_V(): string
	{
		$php_vCommand = 'php-v';

		// Créer un processus pour exécuter la commande Composer Audit dans le répertoire cloné
		$process = Process::fromShellCommandline($php_vCommand);

		try {
			// Exécuter la commande Composer Audit
			$process->mustRun();

			// Récupérer la sortie de la commande
			return $process->getOutput();
		} catch (ProcessFailedException $exception) {
			// En cas d'échec de Composer Audit, retourner un message d'erreur
			return $exception->getMessage();
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
