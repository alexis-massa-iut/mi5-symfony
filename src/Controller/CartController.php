<?php
//  Controller/CartController.php
namespace App\Controller;

use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

use function PHPUnit\Framework\isNull;

class CartController extends AbstractController
{
    /**
     * Cart page
     */
    public function cartIndex(CartService $cs)
    {
        $content = $cs->getContent();
        $total = $cs->getTotal();
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
            return $this->redirect($request->headers->get('referer'));
        }
    }

    /**
     * Remove $amount of $idProduct
     */
    public function cartRemove(CartService $cs, Request $request, $idProduct, $amount)
    {
        $cs->removeProduct($idProduct, $amount);
        $this->addFlash('success', 'Retiré du panier');
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Delete all $idProduct
     */
    public function cartDelete(CartService $cs, Request $request, $idProduct)
    {
        $cs->deleteProduct($idProduct);
        $this->addFlash('success', 'Supprimé du panier');
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Empty cart
     */
    public function cartEmpty(CartService $cs, Request $request)
    {
        $cs->empty();
        $this->addFlash('success', 'Panier vidé');
        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * Checkout cart
     */
    public function cartCheckout(CartService $cs, Request $request, $idUser)
    {
        $res = $cs->cartToCommand($idUser); // convert to command
        if ($res === null) { // Panier vide
            $this->addFlash('warning', 'Panier vide, aucune commande créée');
            return $this->redirect($request->headers->get('referer'));
        } else {
            $this->addFlash('success', 'Commande créée');
            $cs->empty();
            return $this->redirectToRoute('user_commands');
        }
    }
}
