<?php

namespace App\Controller\Api;

use App\Entity\Job;
use App\Entity\Project;
use App\Entity\Rapport;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/git', name: 'git_')]
class ApiGitCloneController extends AbstractController
{


    private function getPhpVersionFromComposerJson(): ?string
    {
        // Assuming the path to the composer.json file
        $composerJsonPath = realpath(__DIR__.'/../../../public/repoClone/composer.json');

        // Read the contents of composer.json
        $composerJsonContents = file_get_contents($composerJsonPath);

        if (false === $composerJsonContents) {
            // Handle the case when reading fails
            return null;
        }

        // Decode the JSON content
        $composerData = json_decode($composerJsonContents, true);

        if (JSON_ERROR_NONE !== json_last_error()) {
            // Handle JSON decoding error
            return null;
        }

        // Retrieve the PHP version from the "require" section
        return $composerData['require']['php'] ?? null;
    }

    #[Route('/clone/{project}', name: 'clone')]
    public function gitClone(Project $project, Request $request, EntityManagerInterface $entityManager): Response
    {
        //Variable pour localHost
        $localHost = 'localhost';

        // Récupérer l'URL du dépôt Git depuis la requête
        $repositoryUrl = $project->getUrl();

        // Vérifier si une URL de dépôt a été fournie
        if (!$repositoryUrl) {
            return new Response('Aucune URL de dépôt spécifiée.', Response::HTTP_BAD_REQUEST);
        }

        // Chemin relatif du répertoire de destination
        $destination = realpath(__DIR__.'/../../../public/repoClone'); // Utiliser un chemin relatif par rapport à la racine du projet

        // Créer un processus pour exécuter la commande git clone dans le répertoire spécifié
        $process = new Process(['git', 'clone', $repositoryUrl, 'repoClone']);
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
            // Exécuter la commande Composer Audit
            $composerAudit = new Process(['composer', 'audit', '--locked', '--format=json']);
            $composerAudit->setWorkingDirectory(realpath(__DIR__.'/../../../public/repoClone'));
            $composerAudit->run();
            $composerAuditOutput = $composerAudit->getOutput();

            $detail = empty($composerAuditOutput) ? ['result' => 'Aucune faille'] : ['result' => $composerAuditOutput];

            // Exécuter PHPStan
            $phpStan = new Process(['../../vendor/bin/phpstan', 'analyse']);
            $phpStan->setWorkingDirectory(realpath(__DIR__.'/../../../public/repoClone'));
            $phpStan->run();
            $phpStanOutput = $phpStan->getOutput();

            // Get the PHP version from composer.json
            $phpVersion = $this->getPhpVersionFromComposerJson();

            // Execute PHPStan
            $detailPhpStan = empty($phpStanOutput) ? ['result' => 'Aucune faille'] : ['result' => $phpStanOutput];
            $detailPhpStan = empty($phpStanOutput) ? ['result' => 'Aucune faille'] : ['result' => $phpStanOutput];

            // Exécuter PHP MD
            $phpCs = new Process(['../../vendor/bin/phpcs', '../../public/repoClone/src']);
            $phpCs->setWorkingDirectory(realpath(__DIR__.'/../../../public/repoClone'));
            $phpCs->run();
            $phpCsOutput = $phpCs->getOutput();
            // dd($phpCs);

            $detailphpCs = empty($phpCsOutput) ? ['result' => 'Aucune faille'] : ['result' => $phpCsOutput];

            // Enregistrer les résultats de Composer Audit et PHPStan en tant que jobs
            $composerAuditJob = new Job();
            $composerAuditJob->setName('Composer Audit');
            $composerAuditJob->setProject($project);
            $composerAuditJob->setResultat($this->checkAuditOutput($composerAuditOutput));
            $composerAuditJob->setDetail($detail);
            $entityManager->persist($composerAuditJob);

            $phpStanJob = new Job();
            $phpStanJob->setName('PHP STAN');
            $phpStanJob->setProject($project);
            $phpStanJob->setResultat($this->checkAuditOutput($phpStanOutput));
            $phpStanJob->setDetail($detailPhpStan);
            $entityManager->persist($phpStanJob);

            $phpVersionJob = new Job();
            $phpVersionJob->setName('PHP Version');
            $phpVersionJob->setProject($project);
            $phpVersionJob->setResultat(true);
            $phpVersionJob->setDetail((array) $phpVersion);
            $entityManager->persist($phpVersionJob);

            $phpCsJob = new Job();
            $phpCsJob->setName('PHP Cs');
            $phpCsJob->setProject($project);
            $phpCsJob->setResultat($this->checkAuditOutput($phpCsOutput));
            $phpCsJob->setDetail($detailphpCs);
            $entityManager->persist($phpCsJob);

            // Créer un rapport
            $rapport = new Rapport();
            $rapport->setProject($project);
            $rapport->addJob($composerAuditJob);
            $rapport->addJob($phpCsJob);
            $rapport->addJob($phpStanJob);
            $rapport->addJob($phpVersionJob);
            $rapport->setDate(new \DateTimeImmutable('now'));
            $rapport->setContent('Rapport '.$rapport->getId());
            $composerAuditJob->setRapport($rapport);
            $phpStanJob->setRapport($rapport);
            $phpVersionJob->setRapport($rapport);
            $entityManager->persist($rapport);

            // Nettoyer le répertoire cloné une fois terminé
            $filesystem = new Filesystem();
            $cloneDirectory = realpath(__DIR__.'/../../../public/repoClone');

            // Verifier que le dossier repoClone existe bien avant de faire quoi que ce soit
            if ($filesystem->exists($cloneDirectory)) {
                // Suppression récursive du dossier repoClone avec les full access
                $filesystem->chmod($cloneDirectory, 0777, 0000, true);
                $filesystem->remove($cloneDirectory);
            }

            // Sauvegarder les entités et renvoyer la réponse
            $entityManager->flush();

            // Email sender !!! APRES LE FLUSH SINON IMPOSSIBLE DE RECUPERER ID RAPPORT !!!
            $this->sendEmail($project, $rapport);

            $message = 'Analyse du dépôt Git réussi.';

            return $this->render('project/show.html.twig', [
                'project' => $project,
                'message' => $message,
            ]);
        } catch (ProcessFailedException $exception) {
            // Gérer les erreurs
            return new Response($exception->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (TransportExceptionInterface $e) {
            // Gérer les erreurs
            return new Response($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
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

    public function checkAuditOutput($composerAuditOutput)
    {
        if (empty($composerAuditOutput)) {
            return true;
        } else {
            return false;
        }
    }


    private function sendEmail(Project $project, Rapport $rapport)
    {
        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = $this->getParameter('mail_host');
            $mail->SMTPAuth   = true;
            $mail->Username   = '';
            $mail->Password   = '';
            $mail->Port       = $this->getParameter('mail_port');

            $mail->setFrom("codeScan@no-reply.fr", 'CodeScan-No-Reply');
            $mail->addAddress($project->getUser()->getEmail(), 'Rapport d\'analyse');
            $mail->isHTML(true);
            $mail->Subject = "Rapport d'analyse codeScan";
            $mail->Body = "Bonjour, voici le rapport d'analyse de projet ".$project->getName()." réalisé le ".
                $rapport->getDate()->format('d-m-Y')." à ".$rapport->getDate()->format("H:i:s")."<br>";
            $mail->Body .= "Les résultats de l'analyse sont disponibles à l'adresse : <a href='".$_ENV['SITE_BASE_URL']."/rapport/".$rapport->getId()."'>lien vers le rapport</a>";
            $mail->send();
            echo 'Le message a été envoyé';
        } catch (Exception $e) {
            echo "Le message n'a pas pu être envoyé. Erreur du mailer : {$mail->ErrorInfo}";
        }
    }
    // Vérification pour s'assurer que $boolPhp est un booléen
}
