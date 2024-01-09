<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Gitonomy\Git\Repository as GitRepository;
use GitElephant\Repository;

class GitCloneController extends AbstractController
{
    #[Route('/git/clone', name: 'app_git_clone')]
    public function index(): Response
    {
        $repositoryPath = '__DIR__/gitclone'; // Chemin où le dépôt sera cloné
        $repositoryUrl = 'https://github.com/exakat/php-static-analysis-tools.git'; // URL de votre dépôt Git
        
        // Créer une instance Repository
        $repository = new GitRepository($repositoryPath, [
            'url' => $repositoryUrl
        ]);
        
        try {
            // Cloner le dépôt
            $repository ->clo
            $message = "Dépôt cloné avec succès dans $repositoryPath";
        } catch (\Exception $e) {
            $message = "Erreur lors du clonage du dépôt : " . $e->getMessage();
        }
        
        // Retourner une réponse, par exemple, via une vue Twig
        return $this->render('git_clone/index.html.twig', [
            'message' => $message,
        ]);
    }
}

