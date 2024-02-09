<?php

namespace App\Controller\Api;

use App\Entity\Rapport;
use App\Serialize\RapportSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rapport', name: 'rapport_')]
class ApiRapportController extends AbstractController
{
    public function __construct(
        private RapportSerializer $rapportSerializer,
    ) {
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        // Vérifier si un utilisateur est connecté
        if (!$user = $this->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => 'Il faut être connecté pour accéder à cette ressource',
            ]);
        }

        if (!$rapport) {
            return $this->json([
                'code' => 400,
                'message' => "Ce rapport n'existe pas.",
            ]);
        }

        // Vérifier si l'utilisateur actuel est le propriétaire du projet
        if ($user !== $rapport->getProject()->getUser()) {
            return $this->json([
                'code' => 403,
                'message' => "Vous n'êtes pas le propriétaire du rapport",
            ]);
        }

        $inputBag = $request->request;

        // Vérifier si le token CSRF est valide
        if (!$inputBag->has('deleteCsrf')) {
            return $this->json([
                'code' => 403,
                'message' => 'Token manquant.',
            ]);
        }

        if ($this->isCsrfTokenValid('delete'.$rapport->getId(), $inputBag->get('deleteCsrf'))) {
            $entityManager->remove($rapport);
            $entityManager->flush();

            return $this->json([
                'code' => 200,
                'message' => 'Le rapport a été supprimé.',
            ]);
        }

        return $this->json([
            'code' => 403,
            'message' => 'Token invalide.',
        ]);
    }
}
