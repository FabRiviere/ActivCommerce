<?php

namespace App\Controller;

use App\Entity\Categories;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DataLoaderController extends AbstractController
{
    #[Route('/data', name: 'app_data_loader')]
    public function index(EntityManagerInterface $manager): Response
    {
        // Chemin ou se trouve nos fichiers
        $file_products = dirname(dirname(__DIR__))."\Sauvegardes_Db\products.json";
        $file_categories = dirname(dirname(__DIR__))."\Sauvegardes_Db\categories.json";
        // Lire le fichier et décodage pour lecture au format php
        $data_products = json_decode(file_get_contents($file_products))[0]->rows;
        $data_categories = json_decode(file_get_contents($file_categories))[0]->rows;
        

        $categories = [];

        foreach ($data_categories as $data_category) {
            $category = new Categories();
            $category   ->setName($data_category[1])
                        ->setImage($data_category[3]);
            $manager->persist($category);
            $categories[] = $category;
        }

        $products = [];

        foreach ($data_products as $data_Product) {
            $product = new Product();
            $product->setName($data_Product[1])
                    ->setDescription($data_Product[2])
                    ->setPrice($data_Product[4])
                    ->setIsBestSeller($data_Product[5])
                    ->setIsNewArival($data_Product[6])
                    ->setIsFeatured($data_Product[7])
                    ->setIsSpecialOffer($data_Product[8])
                    ->setImage($data_Product[9])
                    ->setQuantity($data_Product[10])
                    ->setTags($data_Product[12])
                    ->setSlug($data_Product[13])
                    ->setCreatedAt(new \DateTime());
            $manager->persist($product);
            $products[] = $product;
        }

        // $manager->flush();


        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/DataLoaderController.php',
        ]);
    }
}
