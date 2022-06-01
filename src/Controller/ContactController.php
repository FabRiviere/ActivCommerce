<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/contact')]
class ContactController extends AbstractController
{
    
    #[Route('/', name: 'contact_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ContactRepository $contactRepository): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contactRepository->add($contact, true);

            // Envoi Email
            // Création de l'objet métier User
            // $user = (new User()) // Faire un use
            //         ->setEmail('activcommerce@gmail.com') //email administrateur
            //         ->setFirstname('ActivCommerce')
            //         ->setLastname('Shopping');
            // // Création de l'objet métier Email
            // $email = (new EmailModel()) // Faire un use
            //         ->setTitle("Hello ".$user->getFullName())
            //         ->setSubject("New Contact From your Website")
            //         ->setContent("<br>From : ".$contact->getEmail()
            //                     ."<br> Name : ".$contact->getName()
            //                     ."<br> Subject : ".$contact->getSubject()
            //                     ."<br><br>".$contact->getContent());
            // // Utilisation du service EmailSender après l'avoir injecter et fait un use
            // $emailsender->sendEmailNotificationByMailjet($user,$email);

            // Remise à zéro des champs du formulaire après envoi
            $contact = new Contact();
            $form = $this->createForm(ContactType::class, $contact);

            // Envoi message Flash
            $this->addFlash('contact_success', 'Your message has been sent. An advisor will answer you very quickly!');

            
            
            // return $this->redirectToRoute('contact_index', [], Response::HTTP_SEE_OTHER);
        }

        // Si formulaire pas valide
        if($form->isSubmitted() && !$form->isValid()) {
            // Envoi message Flash
            $this->addFlash('contact_error', 'The form contains error. Please correct and try again.');
        }

        return $this->renderForm('contact/new.html.twig', [
            'contact' => $contact,
            'form' => $form,
        ]);
    }

    
}
