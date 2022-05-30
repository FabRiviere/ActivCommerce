<?php

namespace App\Controller\Stripe;

use App\Entity\Cart;
use App\Services\OrderServices;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class StripeCheckoutSessionController extends AbstractController
{
    #[Route('/create-checkout-session/{reference}', name: 'create-checkout-session')]
    public function index(?Cart $cart, OrderServices $orderServices,
                           EntityManagerInterface $manager ): JsonResponse
    {
        $user = $this->getUser();
        
        if(!$cart) {
            // Si ns ne récupérons pas le panier retour à la page accueil
            return $this->redirectToRoute('home');
        }

        // $cart = $cartServices->getFullCart();

        $order = $orderServices->createOrder($cart);
        Stripe::setApiKey('sk_test_51L4TuZC9wnTTIss4vHiYJOfWBzLsymj1hNnvr1ZyFLEAaWVxSe68LoyaVAf0dUn6QMgWsShoq7bUBAu5TPUJzW9b00CyZIlb5P');
       
        $checkout_session = Session::create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => $orderServices->getLineItems($cart),
            'mode' => 'payment',
            'success_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-success/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $_ENV['YOUR_DOMAIN'] . '/stripe-payment-cancel/{CHECKOUT_SESSION_ID}',
        ]);
        
        $order->setStripeCheckoutSessionId($checkout_session->id);  
        $manager->flush();

          header("HTTP/1.1 303 See Other");
          header("https://localhost:8000" . $checkout_session->url);
       
       
        return $this->json([
            'id' => $checkout_session->id
        ]);
    }
}
