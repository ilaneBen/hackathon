<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Service\ComposerAnalysisService;
use App\Service\EmailService;
use App\Service\EslintAnalysisService;
use Symfony\Component\Uid\Uuid;
use App\Service\GitCloningService;
use App\Service\JobService;
use App\Service\PhpCsAnalysisService;
use App\Service\PhpStanAnalysisService;
use App\Service\PhpVersionService;
use App\Service\RapportService;
use App\Service\ResultToArray;
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
        private EslintAnalysisService $eslintService,
        private JobService $jobService,
        private RapportService $rapportService,
        private EmailService $emailService,
        private ResultToArray $resultToArray,
        private EntityManagerInterface $entityManager,
    ) {
    }

    #[Route('/clone/{project}', name: 'clone')]
    public function gitClone(Project $project, Request $request): Response
    {
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
        $directory = 'repoClone_' . $repoUid;
        // Cloner le dépôt Git
        $this->gitCloningService->cloneRepository($repositoryUrl, $directory);

        // Chemin relatif du répertoire de destination
        $destination = realpath(__DIR__ . '/../../../public/' . $directory);
        // Vérifier que le dossier repoClone_.$repoUid existe bien après le clonage
        if (!is_dir($destination)) {
            return $this->json([
                'code' => 500,
                'message' => 'Le répertoire de destination n\'existe pas.',
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

            // Exécuter les analyses
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
