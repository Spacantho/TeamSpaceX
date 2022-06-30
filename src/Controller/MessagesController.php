<?php

namespace App\Controller;


use App\Form\MessageType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MessagesController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function new(Request $request): Response
    {
        $form = $this->createForm(MessageType::class);

        $emptyForm = clone $form;
            

            $form->handleRequest($request);

           

                    if ($form->isSubmitted() && $form->isValid()) {

                        $data = $form->getData();

                        dump(sprintf('Data email %s <%s>', $data['name'], $data['email']));

                        if (str_contains($request->headers->get('accept'), 'text/vnd.turbo-stream.html')) {

                            $form = $this->createForm(MessageType::class);
                                    
                            $response = new Response;
                            $response->setContent(
                                $this->renderView('messages/success.stream.html.twig', [
                                    'name' => $data ['name'],
                                    'form' => $emptyForm->createView()
                                
                                ])
                            );
                            $response->headers->set('Content-Type', 'text/vnd.turbo-stream.html');
                            return $response;
                        }
                        

                        $this->addFlash('Success', "Message Envoyé ! On vous contacte bientôt.");

                        return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
                    }

                   /*  if ($form->isSubmitted() && !$form->isValid()) {
                        $response = new Response;
                        $response->setStatusCode(422);
                        return $this->renderForm('messages/new.html.twig', [
                            'form' => $form->createView()
                        ], $response);

                    } */

                    return $this->renderForm('messages/new.html.twig', [
                        'form' => $form,
                    ]);
        
                    
            }
    }


