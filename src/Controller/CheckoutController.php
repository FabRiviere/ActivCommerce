<?php

namespace App\Controller;

use App\Form\CheckoutType;
use App\Services\CartServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(CartServices $cartServices, Request $request): Response
    {
        $user = $this->getUser();
        $cart = $cartServices->getFullCart();

        if(!$cart) {
            return $this->redirectToRoute('app_home');
        }

        if(!$user->getAddresses()->getValues()) {

            $this->addFlash('checkout_message', 'Please, add an address to your account before continuing !');
            return $this->redirectToRoute('app_address_new');
        }

        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);

        $form->handleRequest($request);

        // Traitement du formulaire


        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'checkout' => $form->createView()
        ]);
    }
}
