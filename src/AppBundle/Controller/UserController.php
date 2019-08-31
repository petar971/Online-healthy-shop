<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{


    /**
     * @Route("register",name="user_register", methods={"GET"})
     *
     * @return Response
     */
    public function registerAction()
    {

        return $this->render("default/register.html.twig",
            [
                'form' => $this->createForm(UserType::class)->createView()
            ]
        );
    }


    /**
     * @Route("register",methods={"POST"})
     *
     * @param Request $request
     * @return Response
     */
    public function registerProcess(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $roleRepository = $this->getDoctrine()->getRepository(Role::class);
        $userRole = $roleRepository->findOneBy(['name' => 'ROLE_USER']);
        $user->addRole($userRole);


        if($form->isSubmitted()) {
$password=$this->get('security.password_encoder')
    ->encodePassword($user,$user->getPassword());
$user->setPassword($password);
            if(null !== $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' =>$form['email']->getData()]))
            {
                $this->addFlash("error", "Email e вече регистриран");
                return $this->render("default/register.html.twig",
                    [
                        'user' => $user
                    ]);
            }
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute("security_login");
        }


        return $this->redirectToRoute("user_register");
    }

    /**
     * @Route("/logout",name="security_logout")
     * @throws \Exception
     */
    public function logout()
    {
        throw new \Exception("Logout Failed");
    }
}
