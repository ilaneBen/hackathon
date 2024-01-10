<?php

namespace App\Controller\Api;

use App\Entity\Job;
use App\Entity\Rapport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

#[Route('/git', name: 'git_')]
class ApiGitCloneController extends AbstractController
{
	#[Route('/clone', name: 'clone')]
	public function gitClone(Request $request, EntityManagerInterface $entityManager): Response
	{
		// Récupérer l'URL du dépôt Git depuis la requête
		$repositoryUrl = $request->query->get('repository');

		// Vérifier si une URL de dépôt a été fournie
		if (!$repositoryUrl) {
			return new Response('Aucune URL de dépôt spécifiée.', Response::HTTP_BAD_REQUEST);
		}

		// Chemin relatif du répertoire de destination
		$destination = realpath(__DIR__ . "/../../../public/repoClone"); // Utiliser un chemin relatif par rapport à la racine du projet

		// Créer un processus pour exécuter la commande git clone dans le répertoire spécifié
		$process = new Process(["git", "clone", $repositoryUrl, "repoClone"]);
		$process->run(); // Exécute la commande git clone
		try {
			// $cd = new Process(["cd", "repoClone"]);
			// $cd->run();
			// $pwd = new Process(["pwd"]);
			// $pwd->run();
			// dd($pwd->getOutput());
			// Exécuter et enregistrer la commande Composer Audit
			$composerAudit = new Process(["composer", "audit", "--locked", "--format=json", $destination]);
			$composerAudit->run();

			$composerAuditOutput = $composerAudit->getOutput();
			
			$detail = empty($composerAuditOutput) ? ['result' => 'Aucune faille'] : ['result' => $composerAuditOutput];


			// executer php stan
			$phpStan = new Process(["php", "-d", "memory_limit=512M", realpath(__DIR__ . "/../../../vendor/bin/phpstan"), "analyse", "-c", "phpstan.neon.dist", $destination]);
			$phpStan->run();
			$phpStanOutput = $phpStan->getOutput();
			
			$detailPhpStan = empty($phpStanOutput) ? ['result' => 'Aucune faille'] : ['result' => $phpStanOutput];
			
			// Préparer le détail pour le stockage
			dd($composerAudit, $phpStan);

			// Enregistrer le résultat de Composer Audit en tant que job
			$composerAuditJob = new Job();
			$composerAuditJob->setName('Composer Audit');
			$composerAuditJob->setResultat(true);
			$composerAuditJob->setDetail($detail); // Assigner le détail sous forme de tableau
			$entityManager->persist($composerAuditJob);
			$entityManager->flush();

			$phpStanJob = new Job();
			$phpStanJob->setName('PHP STAN');
			$phpStanJob->setResultat(true);
			$phpStanJob->setDetail($detailPhpStan); // Assigner le détail sous forme de tableau
			$entityManager->persist($phpStanJob);
			$entityManager->flush();

			$raport = new Rapport();
			$raport->addJob($composerAuditJob);
			$raport->addJob($phpStanJob);
			$raport->setDate(new \DateTimeImmutable('now'));
			$raport->setContent($composerAuditJob->getName());
			$composerAuditJob->setRapport($raport);
			$phpStanJob->setRapport($raport);
			$entityManager->persist($composerAuditJob, $phpStanJob);
			//$entityManager->flush();
			$entityManager->persist($raport);
			$entityManager->flush();


			$filesystem = new Filesystem();
			$filesystem->remove('repoClone');
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

	private function isValidGitUrl($url)
	{
		// Expression régulière pour valider l'URL Git
		$regex = '/^(github|https?|ssh):\/\/[^\s@]+(@|:\/\/)([^\/:]+)[:\/]Leslie\/([^\/:]+)\/(.+).git$/i';


		// Vérification avec la regex
		return preg_match($regex, $url);
	}
}
