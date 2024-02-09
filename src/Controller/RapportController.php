<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Model\JobViewModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rapport', name: 'rapport_')]
class RapportController extends AbstractController
{
    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function showRapport(Rapport $rapport): Response
    {
        // Récupérer l'utilisateur actuel
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');

            return $this->redirectToRoute('home');
        }

        // Vérifier si un utilisateur est connecté et s'il est le propriétaire du projet
        if ($currentUser !== $rapport->getProject()->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à ce rapport.');

            return $this->redirectToRoute('home'); // Remplacez 'home' par le nom de votre route d'accueil
        }

        $formattedJobs = [];

        foreach ($rapport->getJob() as $job) {
            $jobViewModel = new JobViewModel($job);
            $formattedJobs[] = [
                'jobViewModel' => $jobViewModel,
                'details' => $jobViewModel->getDetails(), // Ajoutez cette ligne pour obtenir les détails
            ];
        }

        return $this->render('rapport/show.html.twig', [
            'rapport' => $rapport,
            'formattedJobs' => $formattedJobs,
        ]);
    }

    #[Route('/pdf/{id}', name: 'show_pdf', methods: ['GET'])]
    public function pdfRapport(Rapport $rapport): Response
    {
        // Récupérer l'utilisateur actuel
        $currentUser = $this->getUser();

        if (!$currentUser) {
            $this->addFlash('error', 'Vous devez être connecté pour accéder à cette page.');

            return $this->redirectToRoute('home');
        }

        // Vérifier si un utilisateur est connecté et s'il est le propriétaire du projet
        if ($currentUser !== $rapport->getProject()->getUser()) {
            $this->addFlash('error', 'Vous n\'avez pas accès à ce projet.');

            return $this->redirectToRoute('home'); // Remplacez 'home' par le nom de votre route d'accueil
        }

        $formattedJobs = [];

        foreach ($rapport->getJob() as $job) {
            $jobViewModel = new JobViewModel($job);
            $formattedJobs[] = [
                'jobViewModel' => $jobViewModel,
                'details' => $jobViewModel->getDetails(), // Ajoutez cette ligne pour obtenir les détails
            ];
        }

        $html = $this->renderView('pdf/rapportPDF.html.twig', [
            'rapport' => $rapport,
            'formattedJobs' => $formattedJobs,
        ]);
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);

        // (Optional) Set paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Stream the generated PDF to the browser
        $response = new Response($dompdf->output());
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="rapport.pdf"');

        return $response;
    }
}
