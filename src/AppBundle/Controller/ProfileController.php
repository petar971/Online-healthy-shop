<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends Controller
{
    /**
     * @Route("/profile",name="view_profile")
     * @return Response
     */
    public function indexAction()
    {
        $currentUser=$this->getUser();
        return $this->render('profile/viewProfile.html.twig', array('user' => $currentUser));
    }
}
