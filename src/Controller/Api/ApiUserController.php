<?php

namespace App\Controller\Api;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/', 'user_')]
class ApiUserController extends AbstractController
{
    #[Route('/signin', name: 'signin', methods: ['POST'])]
    public function signIn(): Response
    {
        return $this->json(['code' => 200, 'message' => 'Connected']);
    }

    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signUp(): Response 
    {
        return $this->json(['code' => 200, 'message' => "User created and connected"]);
    }
    
    #[Route('/user/edit', name: 'edit', methods: ['GET', 'POST'])]
	public function edit (Request $request, EntityManagerInterface $entityManager): Response
	{
		// Vérifier si un utilisateur est connecté
		if (!$user = $this->getUser()) {
			return $this->json(['code' => 403, 'message' => "Il faut être connecté pour accéder à cette ressource"]);
		}

        $inputBag = $request->request;

		if (!$inputBag->has('email') || !$inputBag->has('name') || !$inputBag->has('firstName')) {
            return $this->json(['code' => 403, 'message' => "Champs manquants"]);
        }

        $user->setName($inputBag->get('name'));
        $user->setFirstName($inputBag->get('firstName'));
        $user->setEmail($inputBag->get('email'));

        $entityManager->flush();

		return $$this->json(['code' => 200, 'message' => 'Le project a été modifié.']);
	}
}
