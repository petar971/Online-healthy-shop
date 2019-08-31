<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Product;
use AppBundle\Entity\Species;
use AppBundle\Form\ProductType;
use AppBundle\Service\Product\ProductServiceInterface;
use Doctrine\Common\Collections\ArrayCollection;
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
     * @return Response
     */
    public function FoodView($id)
    {
        $species = $this->getDoctrine()->getRepository(Species::class)->find($id);
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
        // replace this example code with whatever you need
        return $this->render('shop/healthyfood.html.twig', [
            'products' => $products,
            'category' => $category
        ]);

    }


    /**
     * @Route("/shop/category/{id}",name="group_food_view")
     * @param $id
     * @return Response
     */
    public function FoodGroupView($id)
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

        return $this->render('shop/healthyfood.html.twig',
            [
                'products' => $products,
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

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        $this->uploadImage($form, $product);
        $this->productService->create($product);

        return $this->redirectToRoute("blog_index");
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
     * @return Response
     */
    public function viewOneProduct($id)
    {

        $product = $this
            ->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);


        return $this->render("shop/oneProductDetails.html.twig",
            [
                'product' => $product,

            ]);
    }


}

