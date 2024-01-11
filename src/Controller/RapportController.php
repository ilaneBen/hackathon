<?php

namespace App\Controller;

use App\Entity\Rapport;
use App\Form\RapportType;
use App\Repository\RapportRepository;
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
	public function show(Rapport $rapport): Response
	{


$detail = [];
        $jobs = $rapport->getJob();
        $detailJob = null; // Initialisez la variable pour stocker le détail du travail

        foreach ($jobs as $job) {
            $detailJob = $job->getDetail();

        }
        $encodeDetail = json_encode($detailJob, false);

        $decodeDetail = json_decode($encodeDetail, false);
         $detail=$decodeDetail;

        $data = [
			'rapport' => $rapport,
            'jobs' => $detail,
		];
		$html = $this->renderView('rapport/show.html.twig', $data);

		// Configure dompdf
		$options = new Options();
		$options->set('isHtml5ParserEnabled', true);
		$options->set('isPhpEnabled', true);
		$options->set('defaultFont', 'Arial');

		$dompdf = new Dompdf($options);
		$dompdf->loadHtml($html);

		// (Optional) Set paper size and orientation
		$dompdf->setPaper('A4', 'portrait');

		// Render the HTML as PDF
		$dompdf->render();

		// Stream the generated PDF to the browser
		$response = new Response($dompdf->output());
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="rapport.pdf"');

		return $response;
	}



    #[Route('/{id}', name: 'app_rapport_delete', methods: ['POST'])]
    public function delete(Request $request, Rapport $rapport, EntityManagerInterface $entityManager): Response
    {
		$project = $rapport->getProject();
		$message = "rapport ".$rapport->getContent()." a bien été suprimer";
        if ($this->isCsrfTokenValid('delete'.$rapport->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rapport);
            $entityManager->flush();
        }

		return $this->redirectToRoute('project_show', ['id' => $project->getId(), 'message' => $message], Response::HTTP_SEE_OTHER);

	}
}
