<?php

namespace App\Services;

use App\Repository\UserRepository;
use App\Security\VerificationEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;

class User
{
    /**
     * Retourne le nombre d'octets occupé par l'utilisateur
     */
    public static function getFileUseSize(\App\Entity\User $user, UserRepository $userRepository) :int {

        return $userRepository->sumMedias(user: $user);
    }

    public static function envoyerMailConfirmation(Request $request, VerificationEmail $verificationEmail, \App\Entity\User $user): void
    {
        try {
            $verificationEmail->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('EmailConfirmation@simplon.fr', 'Simplon Charleville'))
                    ->to($user->getEmail())
                    ->subject('Veuillez confirmer votre mail')
                    ->htmlTemplate('inscription/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
            $request->getSession()->getFlashBag()->add('success', 'Nous avons envoyé un mail de confirmation.');
        } catch (TransportExceptionInterface $exception) {
            $request->getSession()->getFlashBag()->add('danger', $exception->getMessage());
        }

    }
}