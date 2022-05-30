<?php

namespace App\Controller\Account;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/address')]
class AddressController extends AbstractController
{

    private $requestStack;
   
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        
    }
    
    #[Route('/', name: 'app_address_index', methods: ['GET'])]
    public function index(AddressRepository $addressRepository): Response
    {
        return $this->render('address/index.html.twig', [
            'addresses' => $addressRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_address_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CartServices $cartServices, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $address->setUser($user);
           
            $entityManager->persist($address);
            $entityManager->flush();

            // $addressRepository->add($address, true);
            
            if($cartServices->getFullCart()){

                return $this->redirectToRoute('app_checkout');
            }

            $this->addFlash('address_message', 'Your address has been saved');

            return $this->redirectToRoute('account');
        }

        return $this->render('address/new.html.twig', [
            'address' => $address,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_address_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Address $address, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $session = $this->requestStack->getSession();
        
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            // $addressRepository->add($address, true);
            

           if($session->get('checkout_data')) {
                $data = $session->get('checkout_data');
                $data['address'] = $address;
                $session->set('checkout_data', $data);
                return $this->redirectToRoute('checkout_confirm');
           }
            $this->addFlash('address_message', 'Your address has been edited');

            return $this->redirectToRoute('account');
        }

        return $this->renderForm('address/edit.html.twig', [
            'address' => $address,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_address_delete', methods: ['POST'])]
    public function delete(Request $request, Address $address, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        
        if ($this->isCsrfTokenValid('delete'.$address->getId(), $request->request->get('_token'))) {
            
            $entityManager->remove($address);
            $entityManager->flush();
            
            // $addressRepository->remove($address, true);
            $this->addFlash('address_message', 'Your address has been deleted');
        }

       
        return $this->redirectToRoute('account');
    }
}
