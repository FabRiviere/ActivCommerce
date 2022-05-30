<?php

namespace App\Controller\Cart;

use App\Form\CheckoutType;
use App\Services\CartServices;
use App\Services\OrderServices;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;


class CheckoutController extends AbstractController
{
    private $cartServices;
    private $requestStack;

    public function __construct(CartServices $cartServices, RequestStack $requestStack)
    {
        $this->cartServices = $cartServices;
        $this->requestStack = $requestStack;
    }
    #[Route('/checkout', name: 'app_checkout')]
    public function index(): Response
    {
        
        $user = $this->getUser();
        $cart = $this->cartServices->getFullCart();
        $session = $this->requestStack->getSession();
        

        if(!isset($cart['products'])) {
            return $this->redirectToRoute('app_home');
        }

        if(!$user->getAddresses()->getValues()) {

            $this->addFlash('checkout_message', 'Please, add an address to your account before continuing !');
            return $this->redirectToRoute('app_address_new');
        }

        if($session->get('checkout_data')) {
            return $this->redirectToRoute('checkout_confirm');
        }

        $form = $this->createForm(CheckoutType::class,null,['user'=>$user]);

        
        return $this->render('checkout/index.html.twig', [
            'cart' => $cart,
            'checkout' => $form->createView()
        ]);
    }

    
    #[Route('/checkout/confirm', name:'checkout_confirm')]

    public function confirm(Request $request, OrderServices $orderServices): Response
    {
        $user = $this->getUser();
        $cart = $this->cartServices->getFullCart();
        $session = $this->requestStack->getSession();

        if(!isset($cart['products'])) {
            return $this->redirectToRoute('app_home');
        }

        if(!$user->getAddresses()->getValues()) {

            $this->addFlash('checkout_message', 'Please, add an address to your account before continuing !');
            return $this->redirectToRoute('app_address_new');
        }

        $form = $this->createForm(CheckoutType::class, null, ['user' => $user]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() || $session->get('checkout_data')) {

            if($session->get('checkout_data')){
                $data = $session->get('checkout_data');
            } else {
                $data = $form->getData();
                $session->set('checkout_data', $data);
            }
           
            $address = $data['address'];
            $carrier = $data['carrier'];
            $information = $data['informations'];

            // Save Cart
            $cart['checkout'] = $data;
            $reference = $orderServices->saveCart($cart,$user);
            // dd($reference);

            return $this->render('checkout/confirm.html.twig', [
                'cart' => $cart,
                'address' => $address,
                'carrier' => $carrier,
                'informations' => $information,
                'reference' => $reference,
                'checkout' => $form->createView()
            ]);
        }

        return $this->redirectToRoute('app_checkout');
    }

    #[Route("/checkout/edit", name:"checkout_edit")]
    public function checkoutEdit():Response {
        $session = $this->requestStack->getSession();

        $session->set('checkout_data',[]);
        return $this->redirectToRoute('app_checkout');
    }
}
