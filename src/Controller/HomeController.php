<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\SearchProduct;
use App\Form\SearchProductType;
use App\Repository\HomeSliderRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $repoProduct, HomeSliderRepository $repoHomeSlider): Response
    {
        $products = $repoProduct->findAll();

        $homeSlider = $repoHomeSlider->findBy(['isDisplayed' => true]);

        $productBestSeller      = $repoProduct->findByIsBestSeller(1);
        $productSpecialOffer    = $repoProduct->findByIsSpecialOffer(1);
        $productNewArrival      = $repoProduct->findByIsNewArival(1);
        $productFeatured        = $repoProduct->findByIsFeatured(1);

        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'products' => $products,
            'productBestSeller' => $productBestSeller,
            'productSpecialOffer' => $productSpecialOffer,
            'productNewArrival' => $productNewArrival,
            'productFeatured' => $productFeatured,
            'homeSlider' => $homeSlider,
        ]);
    }

    #[Route('/product/{slug}', name:'product_details')]

    public function show(?Product $product): Response
    {
        if(!$product) {
            return $this->redirectToRoute('home');
        }

        return $this->render("home/single_product.html.twig", [
            'product' => $product
        ]);
    }

    #[Route('/shop', name: 'shop')]
    public function shop(ProductRepository $repoProduct, Request $request): Response
    {
        $products = $repoProduct->findAll();

        // Formulaire de recherche
        $search = new SearchProduct();
        $form = $this->createForm(SearchProductType::class, $search);

        // Analyse de la requ??te
        $form->handleRequest($request);

        // Le formulaire est il soumis et est il valide
        if($form->isSubmitted() && $form->isValid()){
            
            $products = $repoProduct->findWithSearch($search);

        }
        
        return $this->render('home/shop.html.twig', [
            
            'products' => $products,
            'search' => $form->createView(),
            
        ]);
    }
}
