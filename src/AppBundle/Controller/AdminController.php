<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends Controller
{
    /**
     * @Route("/admin", name="admin_panel")
     *
     *
     * @return Response
     *
     */
/*@Security("has_role('ROLE_ADMIN')")
@Security("is_granted('IIS_AUTHENTICATED_FULLY')")*/
    public function indexAction()
    {

        $currentUser=$this->getUser();
        if($currentUser->isAdmin())
        {
        return $this->render('admin/adminView.html.twig');
        }
        return new Response("<html><body>Try again</body></html>");
    }
}
