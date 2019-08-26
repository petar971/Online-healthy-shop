<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\ShoppingCart;
use http\Client\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingCartController extends Controller
{

    /**
     * @Route("/cart",name="view_cart")
     * @param $id
     * @return Response
     */
    public function ViewCart()
    {
        $currentUser=$this->getUser();
        $products=$this->getDoctrine()->getRepository(ShoppingCart::class)->findBy(
            [
                'user' => $currentUser
            ]);
        /*   $category=$products->getCategory();*/



        return $this->render('shop/shoppingCart.html.twig',
            [
                'carts' =>$products
            ]);


    }
    /**
     * @Route("cart/add/{id}",name="add_cart")
     * @param $id
     * @return Response
     */
    public function AddToCart($id)
    {
        $currentUser=$this->getUser();
        $cart=$this->getDoctrine()->getRepository(ShoppingCart::class)->findOneBy(
            [
                'id' =>$id,
                'user' =>$currentUser
            ]
        );
       $product=$this->getDoctrine()->getRepository(Product::class)->find($id);
        $category=$product->getCategory()->getId();
        $products=$this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(
                [
                    'category' => $category
                ]
            );
        if($cart==null)
        {
            $cart=new ShoppingCart();
            $cart->setProduct($product);
            $cart->setUser($currentUser);
        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();


        return $this->redirectToRoute('group_food_view',
            [
                'products' =>$products,
                'id' =>$category
            ]);


    }
    /**
     * @Route("/remove/id",name="remove_from_cart")
     * @param $id
     * @return Response
     */
    public function removeFromCart($id)
    {
        $currentUser=$this->getUser();
        $product=$this->getDoctrine()->getRepository(ShoppingCart::class)->findOneBy(
            [
                'id' =>$id,
                'user' =>$currentUser
            ]
        );
        $products=$this->getDoctrine()->getRepository(ShoppingCart::class)->findAll();

        $em = $this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();

        return $this->render('shop/shoppingCart.html.twig',
            [
                'carts' => $products
            ]);


    }

}
