<?php
//  Controller/CartController.php
namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class CartController extends AbstractController
{
    /**
     * Cart page
     */
    public function cartIndex(EntityManagerInterface $em, CartService $cs)
    {
        $content = $cs->getContent($em);
        $total = $cs->getTotal($em);
        return $this->render('cart/index.html.twig', ["content" => $content, "total" => $total]);
    }

    /**
     * Add to cart
     */
    public function cartAdd(CartService $cs, Request $request, $idProduct, $amount)
    {
        if (!($idProduct == 0 && $amount == 0)) {
            $cs->addProduct($idProduct, $amount);
            $this->addFlash('success', 'Ajouté au panier');
            $referer = $request->headers->get('referer');
            return $this->redirect($referer);
        }
    }

    /**
     * Remove $amount of $idProduct
     */
    public function cartRemove(CartService $cs, Request $request, $idProduct, $amount)
    {
        $cs->removeProduct($idProduct, $amount);
        $this->addFlash('success', 'Retiré du panier');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Delete all $idProduct
     */
    public function cartDelete(CartService $cs, Request $request, $idProduct)
    {
        $cs->deleteProduct($idProduct);
        $this->addFlash('success', 'Supprimé du panier');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * Empty cart
     */
    public function cartEmpty(CartService $cs, Request $request)
    {
        $cs->empty();
        $this->addFlash('success', 'Panier vidé');
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
}
