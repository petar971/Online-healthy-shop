<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ArticlesController extends Controller
{

    /**
     * @Route("/blog",name="article_view")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        return $this->render('articles/viewArticles.html.twig');
    }
}
