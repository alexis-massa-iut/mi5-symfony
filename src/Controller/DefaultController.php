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
     * Shoppping page
     */
    public function shop(ShopService $shopService)
    {
        $categories = $shopService->findAllCategories();
        return $this->render('shop.html.twig', ["categories" => $categories]);
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
    public function search(String $search)
    {
        return $this->render('search.html.twig', ["search" => $search]);
    }
}
