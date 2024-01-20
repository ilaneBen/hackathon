<?php

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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/git', name: 'git_')]
class ApiGitCloneController extends AbstractController
{
    public function __construct(
        private GitCloningService $gitCloningService,
        private ComposerAnalysisService $composerAnalysisService,
        private PhpCsAnalysisService $phpCsAnalysisService,
        private PhpStanAnalysisService $phpStanAnalysisService,
        private PhpVersionService $phpVersionService,
        private JobService $jobService,
        private RapportService $rapportService,
        private EmailService $emailService,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/clone/{project}', name: 'clone')]
    public function gitClone(Project $project, Request $request): Response
    {
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
        $this->gitCloningService->cloneRepository($repositoryUrl, 'repoClone');
        // Vérifier que le dossier repoClone existe bien après le clonage
        if (!is_dir($destination)) {
            return $this->json([
                'code' => 500,
                'message' => 'Le répertoire de destination n\'existe pas.',
            ]);
        }
        try {
            // Exécuter PHPStan
            $phpStanProcess = $this->phpStanAnalysisService->runPhpStanAnalysis($destination);
            // Exécuter la commande Composer Audit
            $composerAuditProcess = $this->composerAnalysisService->runComposerAudit($destination);
            // Get the PHP version from composer.json
            $phpVersion = $this->phpVersionService->getPhpVersionFromComposerJson($destination);
            // Exécuter PHP MD
            $phpCsProcess = $this->phpCsAnalysisService->runPhpCsAnalysis($destination);
            // Créer les jobs et le rapport
            $jobs = [];
            $jobs[] = $this->jobService->createJob($project, 'Composer Audit', $composerAuditProcess->getOutput());
            $jobs[] = $this->jobService->createJob($project, 'PHP STAN', $phpStanProcess->getOutput());
            $jobs[] = $this->jobService->createJob($project, 'PHP Version', $phpVersion);
            $jobs[] = $this->jobService->createJob($project, 'PHP Cs', $phpCsProcess->getOutput());
            $rapport = $this->rapportService->createRapport($project, $jobs);
            // Nettoyer le répertoire cloné une fois terminé
            $this->gitCloningService->cleanCloneDirectory($destination);
            // Sauvegarder les entités et renvoyer la réponse
            $this->entityManager->flush();
            // Email sender !!! APRES LE FLUSH SINON IMPOSSIBLE DE RÉCUPÉRER ID RAPPORT !!!
            $this->emailService->sendEmail($project, $rapport);

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
