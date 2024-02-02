<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MonkeyController extends AbstractController
{
    #[Route('/monkey', name: 'app_monkey')]
    public function index(): Response
    {
        return $this->render('monkey/index.html.twig');
    }
}
