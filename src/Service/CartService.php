<?php
// src/Service/CartService.php
namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Controller\ShopController;
use App\Entity\Command;
use App\Entity\CommandLine;
use App\Entity\Product;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

// Service to manage cart in session
class CartService
{

    const CART_SESSION = 'cart'; // cart name in session
    private $session; // Session service
    private $shop; // Shop service
    private $cart; // Associative array idProduct => amount
    // so $this->cart[$i] = amount of product with id = $i
    // Constructor
public function __construct(SessionInterface $session, ShopController $shop, EntityManagerInterface $em)
    {
        // Get shop and session service
        $this->shop = $shop;
        $this->session = $session;
        // Get EntityManager
        $this->em = $em;
        // Get cart if exists, else initialize
        $this->cart = $this->getCart(); // initialize cart
    }


    /**
     * Update $this->cart with cart in $this->session
     */
    public function getCart()
    {
        $this->cart = $this->session->get(self::CART_SESSION, array());
    }

    /**
     * Set $this->cart in $this->session['cart']
     */
    public function setCart(): void
    {
        $this->session->set(self::CART_SESSION, $this->cart);
    }

    /**
     * Returns the cart content
     *      array of element [ "product" => product, "amount" => amount ]
     * @return Array cart content
     */
    public function getContent(): array
    {
        $this->getCart();
        $content = array();
        foreach ($this->cart as $id => $amount) {
            array_push($content, ["product" => $this->em->getRepository(Product::class)->find($id), "amount" => $amount]);
        }
        return $content;
    }

    /**
     * Return the total amount of the cart
     * @return float amount
     */
    public function getTotal()
    {
        $this->getCart();
        $total = 0.0;
        foreach ($this->cart as $id => $amount) {
            $total += $this->em->getRepository(Product::class)->find($id)->getPrice() * $amount;
        }
        return $total;
    }

    /**
     * Returns the number of elements in the cart
     * @return int number of elements
     */
    public function getNbProducts()
    {
        $this->getCart();
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
        $this->getCart();
        if (array_key_exists($idProduct, $this->cart)) $this->cart[$idProduct]++;
        else $this->cart[$idProduct] = $amount;
        $this->setCart();
    }

    /**
     * Remove amout $amout of the product with id $id in the cart
     * @param int $idProduct id of product to remove
     * @param int $amount amount to remove
     */
    public function removeProduct(int $idProduct, int $amount = 1)
    {
        $this->getCart();
        $this->cart[$idProduct] -= $amount;
        $this->setCart();
    }

    /**
     * Completely delete product of id $id from the cart
     * @param int $idProduct id of the product to delete
     */
    public function deleteProduct(int $idProduct)
    {
        $this->getCart();
        unset($this->cart[$idProduct]);
        $this->setCart();
    }

    /**
     * Empty cart
     */
    public function empty()
    {
        $this->getCart();
        $this->cart = array();
        $this->setCart();
    }


    /**
     * Convert cart to a new Command
     *
     * @param  int $idUser Cart owner's id
     * @return Command|null The newly created command or null is $this->cart is empty
     */
    public function cartToCommand($idUser): ?Command
    {
        $this->getCart(); //Update cart content
        
        if (empty($this->cart)) return null; // abort if cart is empty

        $command = new Command();
        $command->setDate(new DateTime('now', new DateTimeZone('Europe/Paris')))->setUser($this->em->getRepository(User::class)->find($idUser)); // Add datetime and user to command
        foreach ($this->cart as $id => $amount) { // add all cart items as commandlines
            $cl = new CommandLine();
            $cl->setProduct($this->em->getRepository(Product::class)->find($id))->setAmount($amount);
            $command->addCommandLine($cl);
        }
        return $command;
    }
}