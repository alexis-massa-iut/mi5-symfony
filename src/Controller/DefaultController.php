<?php
//  Controller/DefaultController.php
namespace App\Controller;

use App\Service\ShopService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
    /**
     * Home page
     */
    public function index()
    {
        return $this->render('home.html.twig');
    }

    /**
     * Shoppping index
     */
    public function shopIndex(ShopService $shopService)
    {
        $categories = $shopService->findAllCategories();
        return $this->render('shop-index.html.twig', ["categories" => $categories]);
    }

    /**
     * Shoppping categorie
     */
    public function shopCateg(ShopService $shopService, int $categorie)
    {
        $categ = $shopService->findCategorieById($categorie);
        $products = $shopService->findProduitsByCategorie($categorie);
        return $this->render('shop-categ.html.twig', ["categorie" => $categ, "products" => $products]);
    }

    /**
     * Contact page
     */
    public function contact()
    {
        return $this->render('contact.html.twig');
    }

    /**
     * Search page
     */
    public function search(ShopService $shopService, String $search)
    {
        $products = $shopService->findProduitsBynameOrtext($search);
        return $this->render('search.html.twig', ["search" => $search, "products" => $products]);
    }
}
