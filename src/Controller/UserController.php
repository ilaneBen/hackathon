<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', 'user_')]
class UserController extends AbstractController
{
    #[Route('/signup', name: 'signup', methods: [])]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $user = new User;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $em->persist($user);
            $em->flush();
        }

        return $this->render('user/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}