<?php

namespace App\Controller;

use App\Entity\EmailConfirmation;
use App\Entity\Email;
use App\Entity\EmailIndication;
use App\Entity\MotDePasse;
use App\Entity\User;
use App\Form\InscriptionFormType;
use App\Repository\EmailRepository;
use App\Repository\UserRepository;
use App\Security\VerificationEmail;
use App\Security\SimplonAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\IdGenerator\UuidGenerator;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class InscriptionController extends AbstractController
{

    private VerificationEmail $verificationEmail;

    public function __construct(VerificationEmail $verificationEmail)
    {
        $this->verificationEmail = $verificationEmail;
    }
   
   
    #[Route('/inscription', name: 'app_inscription')]
    public function inscription(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        SimplonAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        EmailRepository $emailRepository,
        \App\Services\User $userService
    ): Response
    {
        $user = new User();
        $form = $this->createForm(InscriptionFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            $email = $emailRepository->find($form->get('email')->getData());
            if($email === null) {
                $email = new Email();
                $email->setEmail($form->get('email')->getData());
            }
            $mailIndication = new EmailIndication();
            $email->addEmailIndication($mailIndication);
            $user->addEmailIndication($mailIndication);
            $entityManager->persist($email);
            $entityManager->persist($mailIndication);
            $entityManager->flush();

            $password = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $mot_de_passe = new MotDePasse();
            $mot_de_passe->setValeur($password);
            $user->addMotDePass(
                $mot_de_passe
            );

            $entityManager->persist($mot_de_passe);
            $entityManager->flush();

            // generate a signed url and email it to the user

            $userService::envoyerMailConfirmation(request: $request, verificationEmail: $this->verificationEmail, user: $user);

            $this->addFlash('success', 'Bienvenue sur Simplon Charleville !');

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
       

        return $this->render('inscription/inscription.html.twig', [
            'inscriptionForm' => $form->createView(),
        ]);
    }


    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_inscription');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            $this->addFlash('danger', 'Utilisateur non trouvé');
            return $this->redirectToRoute('app_inscription');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->verificationEmail->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('danger', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_inscription');
        }

        $this->addFlash('success', 'Votre email a bien été vérifié.');

        return $this->redirectToRoute('app_main');
    }






}
