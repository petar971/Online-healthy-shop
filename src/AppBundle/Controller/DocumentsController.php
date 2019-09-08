<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DocumentsController extends Controller
{
    /**
     * @Route("/documents/sitemap",name="sitemap")
     * @return Response
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
}
