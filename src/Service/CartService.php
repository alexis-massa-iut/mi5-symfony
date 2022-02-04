<?php
// src/Service/CartService.php
namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Controller\ShopController;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;

// Service to manage cart in session
class CartService
{

    const CART_SESSION = 'cart'; // cart name in session
    private $session; // Session service
    private $shop; // Shop service
    private $cart; // Associative array idProduct => amount
    // so $this->cart[$i] = amount of product with id = $i
    // Constructor
    public function __construct(SessionInterface $session, ShopController $shop)
    {
        // Get shop and session service
        $this->shop = $shop;
        $this->session = $session;
        // Get cart if exists, else initialize
        $this->cart = $session->get(self::CART_SESSION, array()); // initialize cart
    }

    /**
     * Returns the cart content
     *      array of element [ "product" => product, "amount" => amount ]
     * @return Array cart content
     */
    public function getContent($em): array
    {
        $this->cart = $this->session->get(self::CART_SESSION, array());
        $content = array();
        foreach ($this->cart as $id => $amount) {
            array_push($content, ["product" => $em->getRepository(Product::class)->find($id), "amount" => $amount]);
        }
        return $content;
    }

    /**
     * Return the total amount of the cart
     * @return float amount
     */
    public function getTotal($em)
    {
        $this->cart = $this->session->get(self::CART_SESSION, array());
        $total = 0.0;
        foreach ($this->cart as $id => $amount) {
            $total += $em->getRepository(Product::class)->find($id)->getPrice() * $amount;
        }
        return $total;
    }

    /**
     * Returns the number of elements in the cart
     * @return int number of elements
     */
    public function getNbProducts()
    {
        $this->cart = $this->session->get(self::CART_SESSION, array());
        $nb = 0;
        foreach ($this->cart as $id => $amount) {
            $nb += $amount;
        }
        return $nb;
    }

    /**
     * Add amout $amout of the product with id $id in the cart
     * @param int $idProduct id of product to add
     * @param int $amount amount to add
     */
    public function addProduct(int $idProduct, int $amount = 1)
    {
        $this->cart = $this->session->get(self::CART_SESSION, array());
        if (array_key_exists($idProduct, $this->cart)) $this->cart[$idProduct]++;
        else $this->cart[$idProduct] = $amount;
        $this->session->set(self::CART_SESSION, $this->cart);
    }

    /**
     * Remove amout $amout of the product with id $id in the cart
     * @param int $idProduct id of product to remove
     * @param int $amount amount to remove
     */
    public function removeProduct(int $idProduct, int $amount = 1)
    {
        $this->cart = $this->session->get(self::CART_SESSION, array());
        $this->cart[$idProduct] -= $amount;
        $this->session->set(self::CART_SESSION, $this->cart);
    }

    /**
     * Completely delete product of id $id from the cart
     * @param int $idProduct id of the product to delete
     */
    public function deleteProduct(int $idProduct)
    {
        $this->cart = $this->session->get(self::CART_SESSION, array());
        unset($this->cart[$idProduct]);
        $this->session->set(self::CART_SESSION, $this->cart);
    }

    /**
     * Empty cart
     */
    public function empty()
    {
        $this->cart = $this->session->get(self::CART_SESSION, array());
        $this->cart = array();
        $this->session->set(self::CART_SESSION, $this->cart);
    }
}
