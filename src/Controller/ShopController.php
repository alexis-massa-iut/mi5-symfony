<?php
//  Controller/ShopController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShopController extends AbstractController
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
    public function shopIndex(EntityManagerInterface $em)
    {
        $categories = $em->getRepository(Category::class)->findAll();
        return $this->render('shop-index.html.twig', ["categories" => $categories]);
    }

    /**
     * Shoppping category
     */
    public function shopCateg(EntityManagerInterface $em, int $category)
    {
        $categ = $em->getRepository(Category::class)->find($category);
        $products = $categ->getProducts();
        return $this->render('shop-categ.html.twig', ["category" => $categ, "products" => $products]);
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
    public function search(EntityManagerInterface $em, String $search)
    {
        $products = $em->getRepository(Product::class)->findBySearch($search);
        return $this->render('search.html.twig', ["search" => $search, "products" => $products]);
    }
}
