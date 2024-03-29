<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Service\ComposerAnalysisService;
use App\Service\EmailService;
use App\Service\EslintAnalysisService;
use App\Service\GitCloningService;
use App\Service\JobService;
use App\Service\NpmAndYarnAnalysisService;
use App\Service\PhpCsAnalysisService;
use App\Service\PhpStanAnalysisService;
use App\Service\PhpVersionService;
use App\Service\RapportService;
use App\Service\ResultToArray;
use App\Service\StyleLintService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

#[Route('/git', name: 'git_')]
class ApiGitCloneController extends AbstractController
{
    public function __construct(
        private GitCloningService $gitCloningService,
        private ComposerAnalysisService $composerAnalysisService,
        private PhpCsAnalysisService $phpCsAnalysisService,
        private PhpStanAnalysisService $phpStanAnalysisService,
        private PhpVersionService $phpVersionService,
        private EslintAnalysisService $eslintService,
        private JobService $jobService,
        private RapportService $rapportService,
        private EmailService $emailService,
        private ResultToArray $resultToArray,
        private EntityManagerInterface $entityManager,
        private StyleLintService $lintService,
        private NpmAndYarnAnalysisService $npmAndYarnAnalysisService
    ) {
    }

    #[Route('/clone/{project}', name: 'clone')]
    public function gitClone(Project $project, Request $request): Response
    {
        // Vérifier si un utilisateur est connecté
        if (!$user = $this->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => 'Il faut être connecté pour accéder à cette ressource',
            ]);
        }

        if (!$project) {
            return $this->json([
                'code' => 400,
                'message' => "Ce projet n'existe pas",
            ]);
        }

        // Vérifier si l'utilisateur actuel est le propriétaire du projet
        if ($user !== $project->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => "Vous n'êtes pas le propriétaire du projet",
            ]);
        }

        $repoUid = Uuid::v4()->jsonSerialize();
        // dd($repoUid);
        // Récupérer l'URL du dépôt Git depuis la requête
        $repositoryUrl = $project->getUrl();
        // Vérifier si une URL de dépôt a été fournie
        if (!$repositoryUrl) {
            return $this->json([
                'code' => 500,
                'message' => 'Aucun dépôt sélectionné',
            ]);
        }
        $directory = 'repoClone_'.$repoUid;
        // Cloner le dépôt Git
        $this->gitCloningService->cloneRepository($repositoryUrl, $directory);

        // Chemin relatif du répertoire de destination
        $destination = realpath(__DIR__.'/../../../public/'.$directory);

        // Vérifier que le dossier repoClone_.$repoUid existe bien après le clonage (return si repo git privé ou si repo gi inexistant)
        if (!is_dir($destination)) {
            return $this->json([
                'code' => 500,
                'message' => 'Le répertoire git n\'a pas été trouvé.',
            ]);
        }
        try {
            $inputBag = $request->request;
            $jobs = [];

            // Utilisez les valeurs des cases à cocher
            $useComposer = $inputBag->get('useComposer');
            $usePHPStan = $inputBag->get('usePHPStan');
            $usePHPCS = $inputBag->get('usePHPCS');
            $usePHPVersion = $inputBag->get('usePHPVersion');
            $useEslint = $inputBag->get('useEslint');
            $usestyleLine = $inputBag->get('useStyleLine');
            $nodeAudit = $inputBag->get('useAuditJS');

            // Exécuter les analyses
            if ($nodeAudit) {
                $nodeAuditProcess = $this->npmAndYarnAnalysisService->runAudit($destination);
                $jobs[] = $this->jobService->createJob($project, $this->npmAndYarnAnalysisService->isYarnAuditCommand($nodeAuditProcess), $this->resultToArray->resultToArray($nodeAuditProcess), $nodeAudit);
            }
            if ($usestyleLine) {
                $styleLineProcess = $this->lintService->runStyleLintAnalysis($destination);
                $jobs[] = $this->jobService->createJob($project, 'Style Lint', $this->resultToArray->resultToArrayStyle($styleLineProcess), $usestyleLine);
            }
            // Exécuter PHPStan
            if ($useComposer) {
                $composerAuditProcess = $this->composerAnalysisService->runComposerAudit($destination);
                $jobs[] = $this->jobService->createJob($project, 'Composer Audit', $this->resultToArray->resultToarray($composerAuditProcess), $useComposer);
            }
            if ($usePHPStan) {
                $phpStanProcess = $this->phpStanAnalysisService->runPhpStanAnalysis($destination);
                $jobs[] = $this->jobService->createJob($project, 'PHP STAN', $this->resultToArray->resultToarray($phpStanProcess), $usePHPStan);
            }
            if ($usePHPCS) {
                $phpCsProcess = $this->phpCsAnalysisService->runPhpCsAnalysis($destination);
                $jobs[] = $this->jobService->createJob($project, 'PHP Cs', $this->resultToArray->resultToarray($phpCsProcess), $usePHPCS);
            }
            if ($usePHPVersion) {
                $phpVersion = $this->phpVersionService->getPhpVersionFromComposerJson($destination);
                $jobs[] = $this->jobService->createJob($project, 'PHP Version', $phpVersion, $usePHPVersion);
            }
            if ($useEslint) {
                $eslintProcess = $this->eslintService->runEslintAnalysis($destination);
                $test = $this->jobService->createJob($project, 'Eslint', $this->resultToArray->resultToarray($eslintProcess), $useEslint);
                $jobs[] = $test;
            }
            $rapport = $this->rapportService->createRapport($project, $jobs, $directory);
            // Nettoyer le répertoire cloné une fois terminé
            $this->gitCloningService->cleanCloneDirectory($destination);
            // Sauvegarder les entités et renvoyer la réponse
            $this->entityManager->flush();
            // Email sender !!! APRES LE FLUSH SINON IMPOSSIBLE DE RÉCUPÉRER ID RAPPORT !!!
            $this->emailService->sendEmail($project, $rapport);

            return $this->json([
                'rapportId' => $rapport->getId(),
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
