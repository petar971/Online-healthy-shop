<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Orders;
use AppBundle\Entity\Product;
use AppBundle\Entity\ShoppingCart;
use AppBundle\Form\ShoppingCartType;
use AppBundle\Repository\ShoppingCartRepository;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingCartController extends Controller
{
    private $cartRepository;
    public function __construct(ShoppingCartRepository $cartRepository)
    {
        $this->cartRepository=$cartRepository;
    }

    /**
     * @Route("/cart",name="view_cart")
     * @return Response
     */
    public function ViewCart()
    {
        $currentUser=$this->getUser();
        $products=$this->getDoctrine()->getRepository(ShoppingCart::class)
            ->findBy(
            [
                'status' => false,
                'user' => $currentUser
            ]);
        /*   $category=$products->getCategory();*/
        $totalPrice=0;

        for($i=0;$i<sizeof($products);$i++)
        {
        $totalPrice=$totalPrice + $products[$i]->getProduct()->getPrice()*$products[$i]->getQuantity();
        }

        return $this->render('shop/shoppingCart.html.twig',
            [
                'total' =>$totalPrice,
                'carts' =>$products
            ]);


    }

    /**
     * @Route("cart/add/{id}",name="add_cart")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function AddToCart($id,Request $request)
    {
        $currentUser=$this->getUser();
        $cart=$this->getDoctrine()->getRepository(ShoppingCart::class)->findOneBy(
            [
                'id' =>$id,
                'user' =>$currentUser
            ]

        );


        $form=$this->createForm(ShoppingCartType::class);
        $form->handleRequest($request);
        $quantity=1;
        if ($form->isSubmitted()) {
            $quantity = $form->getData()->getQuantity();


        }

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
            $cart->setQuantity($quantity);

        }
        $em = $this->getDoctrine()->getManager();
        $em->persist($cart);
        $em->flush();

        return $this->redirectToRoute('group_food_view',
            ['form' =>$this->createForm(ShoppingCartType::class)->createView(),
                'products' =>$products,
                'id' =>$category
            ]);


    }

    /**
     * @Route("/remove/cart/{id}",name="remove_from_cart")
     */
    public function RemoveFromCart($id)
    {
        $product=$this->getDoctrine()->getRepository(ShoppingCart::class)->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($product);
        $em->flush();
        return $this->redirectToRoute('view_cart');
    }

    /**
     * @Route("/buy",name="buy_product")
     * @return Response
     */
public function BuyProduct()
{

    $currentUser=$this->getUser();
    $products=$this->getDoctrine()->getRepository(ShoppingCart::class)
        ->findBy(
            [
                'status' => false,
                'user' => $currentUser
            ]);
    $em = $this->getDoctrine()->getManager();
    for($i=0;$i<sizeof($products);$i++)
    {

        $products[$i]->setStatus(1);
        $em->persist($products[$i]);
    }
    $em->flush();
return $this->render('default/index.html.twig');
}
    /**
     *@Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/admin/orders",name="view_orders")
     */
    public function ViewOrders()
    {
        $currentUser=$this->getUser();

        if($currentUser->isAdmin())
        {
        /** @var ShoppingCart $products */
            $products=$this->getDoctrine()->getRepository(ShoppingCart::class)
                ->findBy(
                    [

                        'status' => true,
                    ],
                    [
                        'user' => 'DESC'
                    ]);

        $users=$this->cartRepository->FindByAllIdUsersWithOrder();

        return $this->render('admin/orders.html.twig',
            [
                'users'=>$users,
                'products' => $products
            ]);

    }
        return $this->render('default/index.html.twig');
    }

/**
 *@Security("is_granted('IS_AUTHENTICATED_FULLY')")
 * @Route("/admin/order/{id}",name="one_order")
 */
public function ViewOneOrder($id)
{
    $currentUser=$this->getUser();

    if($currentUser->isAdmin())
    {
        /** @var ShoppingCart $products */
        $products=$this->getDoctrine()->getRepository(ShoppingCart::class)
            ->findBy(
                [
                    'id'=>$id,
                    'status' => true,
                ],
                [
                    'user' => 'DESC'
                ]);

        $products1=$this->cartRepository->FindByAllProductFromUser($id);


        return $this->render('admin/OneOrderView.html.twig',
            [
                'products' => $products1
            ]);

    }
    return $this->render('default/index.html.twig');
}

    /**
     * @Route("/sent/order/{id}",name="sent_order")
     * @param $id
     * @return Response
     */
public function send($id)
{
    $em=$this->getDoctrine()->getManager();
    $products1=$this->cartRepository->FindByAllProductFromUser($id);
    foreach($products1 as $item)
    {
        $order=new Orders();
        $order->setProduct($item->getProduct());
        $order->setUser($item->getUser());
        $em->persist($order);
        $em->remove($item);
    }
    $em->flush();

    return $this->render('default/index.html.twig');
}

}
