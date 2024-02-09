<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Serialize\ProjectSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project', name: 'project_')]
class ProjectController extends AbstractController
{
    public function __construct(
        private ProjectSerializer $projectSerializer,
    ) {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');

            return $this->redirectToRoute('home');
        }

        return $this->render('project/index.html.twig', []);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Project $project): Response
    {
        // Récupérer l'utilisateur actuel
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');

            return $this->redirectToRoute('home');
        }

        // Vérifier si un utilisateur est connecté et s'il est le propriétaire du projet
        if ($currentUser !== $project->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à ce projet.');

            return $this->redirectToRoute('home'); // Remplacez 'home' par le nom de votre route d'accueil
        }

        return $this->render('project/show.html.twig', [
            'project' => $this->projectSerializer->serializeOne($project),
        ]);
    }
}
