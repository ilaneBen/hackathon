<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/project', name: 'project_')]
class ProjectController extends AbstractController
{
	public function __construct (private Security $security)
	{
	}

	#[Route('/', name: 'index', methods: ['GET'])]
	public function index (ProjectRepository $projectRepository): Response
	{
		$currentUser = $this->security->getUser();

		if (!$currentUser) {

			$this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
			return $this->redirectToRoute('home');
		}

		$projects = $projectRepository->findBy(['user' => $currentUser]);

		return $this->render('project/index.html.twig', [
			'projects' => $projects,
		]);
	}


	#[Route('/new', name: 'new', methods: ['GET', 'POST'])]
	public function new (Request $request, EntityManagerInterface $entityManager): Response
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
	public function show (Project $project): Response
	{
		// Récupérer l'utilisateur actuel
		$currentUser = $this->security->getUser();

		// Vérifier si un utilisateur est connecté et s'il est le propriétaire du projet
		if ($currentUser !== $project->getUser()) {
			$this->addFlash('error', 'Vous n\'avez pas accès à ce projet.');
			return $this->redirectToRoute('home'); // Remplacez 'home' par le nom de votre route d'accueil
		}

		$message = "";

		return $this->render('project/show.html.twig', [
			'project' => $project,
			'message' => $message,
		]);
	}

	#[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit (Request $request, Project $project, EntityManagerInterface $entityManager): Response
	{
		// Récupérer l'utilisateur actuel
		$currentUser = $this->security->getUser();

		// Vérifier si un utilisateur est connecté
		if (!$currentUser) {
			$this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');
			return new RedirectResponse($this->generateUrl('home'));
		}

		// Vérifier si l'utilisateur actuel est le propriétaire du projet
		if ($currentUser !== $project->getUser()) {
			$this->addFlash('error', 'Vous n\'avez pas accès à ce projet.');
			return $this->redirectToRoute('project_index'); // Rediriger vers une page appropriée pour les projets de l'utilisateur
		}

		$form = $this->createForm(ProjectType::class, $project);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$entityManager->flush();

			return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
		}

		return $this->render('project/edit.html.twig', [
			'project' => $project,
			'form' => $form,
		]);
	}

	#[Route('/{id}', name: 'delete', methods: ['POST'])]
	public function delete (Request $request, Project $project, EntityManagerInterface $entityManager): Response
	{
		// Récupérer l'utilisateur actuel
		$currentUser = $this->security->getUser();

		// Vérifier si un utilisateur est connecté
		if (!$currentUser) {
			$this->addFlash('error', 'Vous devez être connecté pour accéder à cette fonctionnalité.');
			return new RedirectResponse($this->generateUrl('app_login'));
		}

		// Vérifier si l'utilisateur actuel est le propriétaire du projet
		if ($currentUser !== $project->getUser()) {
			$this->addFlash('error', 'Vous n\'avez pas accès à ce projet.');
			return $this->redirectToRoute('project_index'); // Rediriger vers une page appropriée pour les projets de l'utilisateur
		}

		if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
			$entityManager->remove($project);
			$entityManager->flush();
		}

		return $this->redirectToRoute('project_index', [], Response::HTTP_SEE_OTHER);
	}


}
