<?php

// src/Controller/Api/ApiGitCloneController.php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Service\ComposerAnalysisService;
use App\Service\EmailService;
use App\Service\GitCloningService;
use App\Service\JobService;
use App\Service\PhpCsAnalysisService;
use App\Service\PhpStanAnalysisService;
use App\Service\PhpVersionService;
use App\Service\RapportService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/git', name: 'git_')]
class ApiGitCloneController extends AbstractController
{
    #[Route('/clone/{project}', name: 'clone')]
    public function gitClone(
        Project $project,
        Request $request,
        EntityManagerInterface $entityManager,
        GitCloningService $gitCloningService,
        ComposerAnalysisService $composerAnalysisService,
        PhpCsAnalysisService $phpCsAnalysisService,
        PhpStanAnalysisService $phpStanAnalysisService,
        PhpVersionService $phpVersionService,
        JobService $jobService,
        RapportService $rapportService,
        EmailService $emailService
    ): Response {
        // Récupérer l'URL du dépôt Git depuis la requête
        $repositoryUrl = $project->getUrl();

        // Vérifier si une URL de dépôt a été fournie
        if (!$repositoryUrl) {
            return $this->json([
                'code' => 500,
                'message' => 'Aucun dépôt sélectionné',
            ]);
        }

        // Chemin relatif du répertoire de destination
        $destination = realpath(__DIR__.'/../../../public/repoClone');

        // Cloner le dépôt Git
        $gitCloningService->cloneRepository($repositoryUrl, 'repoClone');
        // Vérifier que le dossier repoClone existe bien après le clonage
        if (!is_dir($destination)) {
            return $this->json([
                'code' => 500,
                'message' => 'Le répertoire de destination n\'existe pas.',
            ]);
        }
        try {
            // Exécuter PHPStan
            $phpStanProcess = $phpStanAnalysisService->runPhpStanAnalysis($destination);
            $phpStanOutput = $phpStanProcess->getOutput();
            // Exécuter la commande Composer Audit
            $composerAuditProcess = $composerAnalysisService->runComposerAudit($destination);
            $composerAuditOutput = $composerAuditProcess->getOutput();
            // Get the PHP version from composer.json
            $phpVersion = $phpVersionService->getPhpVersionFromComposerJson($destination);
            // Exécuter PHP MD
            $phpCsProcess = $phpCsAnalysisService->runPhpCsAnalysis($destination);
            $phpCsOutput = $phpCsProcess->getOutput();
            // Créer les jobs et le rapport
            $jobs = [];
            $jobs[] = $jobService->createJob($project, 'Composer Audit', $composerAuditOutput);
            $jobs[] = $jobService->createJob($project, 'PHP STAN', $phpStanOutput);
            $jobs[] = $jobService->createJob($project, 'PHP Version', $phpVersion);
            $jobs[] = $jobService->createJob($project, 'PHP Cs', $phpCsOutput);

            $rapport = $rapportService->createRapport($project, $jobs);

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
            $emailService->sendEmail($project, $rapport);

            return $this->json([
                'code' => 200,
                'message' => 'Analyse du dépôt Git réussi.',
            ]);
        } catch (ProcessFailedException $exception) {
            // Gérer les erreurs
            return $this->json([
                'code' => 500,
                'message' => $exception->getMessage(),
            ]);
        }
    }
}
