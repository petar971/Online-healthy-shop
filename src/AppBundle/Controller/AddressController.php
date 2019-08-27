<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ShoppingCart;
use AppBundle\Entity\UserAddress;
use AppBundle\Form\UserAddressType;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends Controller
{
    /**
     * @Route("/address",name="delivery_address")
     * @return Response
     */
    public function indexAction()
    {

        return $this->render('shop/UserAddressForm.html.twig');
    }

    /**
     * @Route("/address/add",name="add_address")
     * @param Request $request
     * @return Response
     */
    public function AddAddress(Request $request)
    {
        $currentUser = $this->getUser();
        $address = new UserAddress();
        $address->setUser($currentUser);
        $form = $this->createForm(UserAddressType::class, $address);
        $form->handleRequest($request);
        $em = $this->getDoctrine()->getManager();
        $em->persist($address);

        $products=$this->getDoctrine()->getRepository(ShoppingCart::class)
            ->findBy(
                [
                    'status' => false,
                    'user' => $currentUser
                ]);
        for($i=0;$i<sizeof($products);$i++)
        {

            $products[$i]->setAddress($address);
            $em->persist($products[$i]);
        }
        $em->flush();



        return $this->redirectToRoute('buy_product',
            [

            ]
        );
    }
}
