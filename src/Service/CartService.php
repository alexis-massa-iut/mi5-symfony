<?php
// src/Service/CartService.php
namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\ShopService;

// Service to manage cart in session
class CartService
{

    const CART_SESSION = 'cart'; // cart name in session
    private $session; // Session service
    private $shop; // Shop service
    private $cart; // Associative array idProduct => amount
    // so $this->cart[$i] = amount of product with id = $i
    // Constructor
    public function __construct(SessionInterface $session, ShopService $shop)
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
    public function getContent(): array
    {
        $content = array();
        foreach ($this->cart as $id => $amount) {
            array_push($content, ["product" => $this->shop->findProductById($id), "amount" => $amount]);
        }
        return $content;
    }

    /**
     * Return the total amount of the cart
     * @return float amount
     */
    public function getTotal()
    {
        $total = 0.0;
        foreach ($this->cart as $id => $amount) {
            $total += $this->shop->findProductById($id)['price'] * $amount;
        }
        return $total;
    }

    /**
     * Returns the number of elements in the cart
     * @return int number of elements
     */
    public function getNbProducts()
    {
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
        $this->cart[$idProduct] += $amount;
    }

    /**
     * Remove amout $amout of the product with id $id in the cart
     * @param int $idProduct id of product to remove
     * @param int $amount amount to remove
     */
    public function removeProduct(int $idProduct, int $amount = 1)
    {
        $this->cart[$idProduct] -= $amount;
    }

    /**
     * Completely delete product of id $id from the cart
     * @param int $idProduct id of the product to delete
     */
    public function deleteProduct(int $idProduct)
    {
        unset($this->cart[$idProduct]);
    }

    /**
     * Empty cart
     */
    public function empty()
    {
        $this->cart = array();
    }
}
