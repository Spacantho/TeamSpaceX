<?php

namespace App\Controller;

use App\Services\User;
use App\Security\VerificationEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(): Response
    {
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
        ]);
    }

    #[Route('/profil/mail_confirmationb', name: 'app_profil_mail_confirmation')]
    public function mail_confirmation(User $userService, VerificationEmail $verificationEmail, Request $request): Response
    {
        $userService::envoyerMailConfirmation(request: $request, verificationEmail: $verificationEmail, user: $this->getUser());

        $this->addFlash('info', 'Mail de confirmation envoyé');
        return $this->redirectToRoute('app_profil', [], Response::HTTP_SEE_OTHER);
    }



}
