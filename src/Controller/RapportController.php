<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Model\JobViewModel;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rapport')]
class RapportController extends AbstractController
{
    #[Route('/{id}', name: 'app_rapport_show', methods: ['GET'])]
    public function showRapport(Rapport $rapport): Response
    {
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

    #[Route('pdf/{id}', name: 'app_rapport_show_pdf', methods: ['GET'])]
    public function pdfRapport(Rapport $rapport): Response
    {
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
    // Configure dompdf

    #[Route('/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
        $project = $rapport->getProject();
        $message = 'rapport '.$rapport->getContent().' a bien été suprimer';
        if (true) {
            $entityManager->remove($rapport);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_show', ['id' => $project->getId(), 'message' => $message], Response::HTTP_SEE_OTHER);
    }
}
