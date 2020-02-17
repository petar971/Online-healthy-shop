<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Form\ProductType;
use AppBundle\Service\Product\ProductServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class ProductController extends Controller
{
    private $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService = $productService;
    }

    /**
     * @Route("/shop/{id}",name="healthy_food_view")
     * @param $id
     * @return Response
     */
    public function FoodView($id,Request $request)
    {

        $category = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->findBy([
                'species' => $id
            ]);

        $products = $this->getDoctrine()
            ->getRepository(Product::class)
            ->findBy([
                'category' => $category
            ], ['price' => 'DESC']);


        $paginator=$this->get('knp_paginator');
        $result = $paginator->paginate(
            $products,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',20)


        );

        return $this->render('shop/healthyfood.html.twig', [
            'products' => $result,
            'category' => $category
        ]);

    }


    /**
     * @Route("/shop/category/{id}",name="group_food_view")
     * @param $id
     * @return Response
     */
    public function FoodGroupView(Request $request,$id)
    {


        $species=$this->getDoctrine()->getRepository(Category::class)->find($id)->getSpecies();
        $category = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->findBy([
                'species' => $species
            ]);

        $products = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(
                [
                    'category' => $id
                ]
            );



        $paginator=$this->get('knp_paginator');
        $result = $paginator->paginate(
            $products,
            $request->query->getInt('page',1),
            $request->query->getInt('limit',20)


        );

        return $this->render('shop/healthyfood.html.twig',
            [

                'products' => $result,
                'category' => $category
            ]);

    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/product/create",name="product_create", methods={"GET"})
     * @return Response
     */
    public function create()
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $currentUser = $this->getUser();
        if ($currentUser->isAdmin()) {
            return $this->render("admin/createProduct.html.twig",
                ['form' => $this->createForm(ProductType::class)
                    ->createView(),
                    'category' => $category
                ]);
        }
        return $this->render("articles/viewArticles.html.twig");
    }
    /**
     * @Route("/product/create",methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */

    /*@Security("is_granted('IIS_AUTHENTICATED_FULLY')")*/
    public function createProcess(Request $request)
    {
        $product = new Product();
        $category = $this->getDoctrine()->getRepository(Category::class)->findAll();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $this->uploadImage($form, $product);
        $this->productService->create($product);

       /* return $this->redirectToRoute("blog_index");*/
        return $this->render('admin/createProduct.html.twig',['category'=>$category,
            'form' => $this->createForm(ProductType::class)
                ->createView()
            ]);
    }

    /**
     * @param FormInterface $form
     * @param Product $product
     */
    private function uploadImage(FormInterface $form, Product $product)
    {
        /**
         * @var UploadedFile $file
         */
        $file = $form['image']->getData();

        if ($file) {
            $filename = md5(uniqid()) . "." . $file->guessExtension();
            $file->move(
                $this->getParameter('product_directory'),
                $filename
            );
            $product->setImage($filename);

        }
    }

    /**
     * @Route("/details/{id}",name="view_oneProduct")
     *
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function viewOneProduct($id,Request $request)
    {



        $product = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);



        return $this->render("shop/oneProductDetails.html.twig",
            [
                'url1'=> $request->headers->get('referer'),
                'product' => $product,

            ]);
    }



    /**
     * @Route("/shop/category/{id}/{page}",name="group_food_view_page")
     * @param $id
     * @return Response
     */
    public function FoodGroupViewPage($id)
    {


        $species=$this->getDoctrine()->getRepository(Category::class)->find($id)->getSpecies();
        $category = $this
            ->getDoctrine()
            ->getRepository(Category::class)
            ->findBy([
                'species' => $species
            ]);

        $products = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->findBy(
                [
                    'category' => $id
                ],[],2
            );




        return $this->render('shop/healthyfood.html.twig',
            [

                'products' => $products,
                'category' => $category
            ]);

    }
}

