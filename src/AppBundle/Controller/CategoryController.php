<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Entity\Species;
use AppBundle\Form\CategoryType;
use http\Client\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/category/create",name="category_create", methods={"GET"})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function create()
    {
        $species = $this->getDoctrine()->getRepository(Species::class)->findAll();
        $currentUser = $this->getUser();
        if ($currentUser->isAdmin()) {

            return $this->render("admin/createCategory.html.twig",
                ['form' => $this->createForm(CategoryType::class)
                    ->createView(),
                    'species' => $species
                ]);
        }
        return $this->render("default/index.html.twig");
    }
    /**
     * @Route("/category/create",methods={"POST"})
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */

    /*@Security("is_granted('IIS_AUTHENTICATED_FULLY')")*/
    public function createProcess(Request $request)
    {
        $category = new Category();

        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        $em=$this->getDoctrine()->getManager();
        $em->persist($category);
        $em->flush();


        return $this->render("admin/adminView.html.twig");
    }
}
