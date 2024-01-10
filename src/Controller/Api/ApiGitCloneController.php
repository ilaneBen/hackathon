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

    private function getPhpVersionFromComposerJson(): ?string
    {
        // Assuming the path to the composer.json file
        $composerJsonPath = realpath(__DIR__ . "/../../../public/repoClone/composer.json");

        // Read the contents of composer.json
        $composerJsonContents = file_get_contents($composerJsonPath);

        if ($composerJsonContents === false) {
            // Handle the case when reading fails
            return null;
        }

        // Decode the JSON content
        $composerData = json_decode($composerJsonContents, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON decoding error
            return null;
        }

        // Retrieve the PHP version from the "require" section
        return $composerData['require']['php'] ?? null;
    }

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

		// Attendre que le répertoire soit créé après le clone
		$process->wait(function ($type, $buffer) {
			if (Process::ERR === $type) {
				// Gérer les erreurs éventuelles
			} else {
				// Gérer le succès si nécessaire
			}
		}, null, 300000); // Attendre jusqu'à 300 secondes (ajustez si nécessaire)

        try {
            // Execute the Composer Audit command
            $composerAudit = new Process(["composer", "audit", "--locked", "--format=json"]);
            $composerAudit->setWorkingDirectory(realpath(__DIR__ . "/../../../public/repoClone"));
            $composerAudit->run();
            $composerAuditOutput = $composerAudit->getOutput();

            $detail = empty($composerAuditOutput) ? ['result' => 'Aucune faille'] : ['result' => $composerAuditOutput];

            // Execute PHPStan
            $phpStan = new Process(['../../vendor/bin/phpstan', 'analyse', 'src', 'tests']);
            $phpStan->setWorkingDirectory(realpath(__DIR__ . "/../../../public/repoClone"));
            $phpStan->run();
            $phpStanOutput = $phpStan->getOutput();

            // Get the PHP version from composer.json
            $phpVersion = $this->getPhpVersionFromComposerJson();

            // Execute PHPStan
            $detailPhpStan = empty($phpStanOutput) ? ['result' => 'Aucune faille'] : ['result' => $phpStanOutput];

            // Enregistrer les résultats de Composer Audit, PHPStan, and PHP Version en tant que jobs
            $composerAuditJob = new Job();
            $composerAuditJob->setName('Composer Audit');
            $composerAuditJob->setResultat(true);
            $composerAuditJob->setDetail($detail);
            $entityManager->persist($composerAuditJob);

            $phpStanJob = new Job();
            $phpStanJob->setName('PHP STAN');
            $phpStanJob->setResultat(true);
            $phpStanJob->setDetail($detailPhpStan);
            $entityManager->persist($phpStanJob);

            $phpVersionJob = new Job();
            $phpVersionJob->setName('PHP Version');
            $phpVersionJob->setResultat(true);
            $phpVersionJob->setDetail((array)$phpVersion);
            $entityManager->persist($phpVersionJob);

            // Créer un rapport
            $rapport = new Rapport();
            $rapport->addJob($composerAuditJob);
            $rapport->addJob($phpStanJob);
            $rapport->addJob($phpVersionJob);
            $rapport->setDate(new \DateTimeImmutable('now'));
            $rapport->setContent($composerAuditJob->getName());
            $composerAuditJob->setRapport($rapport);
            $phpStanJob->setRapport($rapport);
            $phpVersionJob->setRapport($rapport);
            $entityManager->persist($rapport);

            // Nettoyer le répertoire cloné une fois terminé
            /*$filesystem = new Filesystem();
            $filesystem->remove('repoClone');*/

            // Sauvegarder les entités et renvoyer la réponse
            $entityManager->flush();

            $message = "Clonage du dépôt Git réussi.";
            return $this->render('git_clone/resultat.html.twig', [
                'message' => $message,
            ]);
        } catch (\Exception $exception) {
            // Handle exceptions appropriately
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
