<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;
use AppBundle\Service\Article\ArticleServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use AppBundle\Entity\User;

class ArticlesController extends Controller
{
    private $articleService;
    public function __construct(ArticleServiceInterface $articleService)
    {
$this->articleService=$articleService;
    }

    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @Route("/article/create",name="article_create", methods={"GET"})
     * @return Response
     */
    public function create()
    {
$currentUser = $this->getUser();
if($currentUser->isAdmin()) {
    return $this->render("admin/createArticle.html.twig",
        ['form' => $this->createForm(ArticleType::class)
            ->createView()
        ]);
}
return $this->render("articles/viewArticles.html.twig");
    }
    /**
     * @Route("/article/create",methods={"POST"})
     *@Security("is_granted('IS_AUTHENTICATED_FULLY')")
     * @param Request $request
     * @return Response
     * @throws \Exception
     */

    /*@Security("is_granted('IIS_AUTHENTICATED_FULLY')")*/
    public function createProcess(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        $this->uploadImage($form, $article);
        $this->articleService->create($article);

        return $this->redirectToRoute("blog_index");
    }

    /**
     * @Route("/article", name="article_view")
     * @return Response
     */
    public function indexAction()
    {
        $articles=$this->getDoctrine()
            ->getRepository(Article::class)
            ->findBy([],['dateAdded'=> 'DESC']);
        // replace this example code with whatever you need
        return $this->render('articles/viewArticles.html.twig',['articles'=>$articles]);
    }
    /**
     * @Route("/article/{id}",name="viewOne_article")
     *
     * @param $id
     * @return Response
     */
    public function view($id)
    {

        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        if (null == $article) {
            return $this->redirectToRoute("article_view");
        }

        return $this->render("articles/ArticleView.html.twig",
            [
                'article' => $article,

            ]);
    }

    /**
     * @Route("/delete", name="article_delete_view")
     * @return Response
     */
    public function deleteView()
    {
        $articles=$this->getDoctrine()
            ->getRepository(Article::class)
            ->findBy([],['dateAdded'=> 'DESC']);
        // replace this example code with whatever you need
        return $this->render('admin/deleteArticle.html.twig',['articles'=>$articles]);
    }


    /**
     * @Route("/delete/{id}",name="article_delete",methods={"GET"})
     *
     * @param $id
     * @return Response
     */

    /**/
    public function delete($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render("articles/delete.html.twig",
            [
                'form' => $this->createForm(ArticleType::class)->createView(),
                'article' => $article

            ]);

    }

    /**
     * @Route("/delete/{id}",methods={"POST"})
     *
     * @param Request $request
     * @param $id
     * @return Response
     */

    /**/
    public function deleteProcess(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);
        $form = $this->createForm(ArticleType::class);
        $form->handleRequest($request);

        $this->articleService->delete($article);
        return $this->redirectToRoute("blog_index");


    }

    /**
     * @Route("/edit/{id}",name="article_edit",methods={"GET"})
     *
     * @param $id
     * @return Response
     */


    public function edit($id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);

        /**
         * @var User $currentUser
         */
        $currentUser = $this->getUser();

        if (!$currentUser->isAdmin()) {
            return $this->redirectToRoute('blog_index');
        }

        return $this->render("articles/edit.html.twig",
            [
                'form' => $this->createForm(ArticleType::class)->createView(),
                'article' => $article

            ]);

    }

    /**
     * @Route("/edit/{id}",methods={"POST"})
     *
     * @param Request $request
     * @param $id
     * @return Response
     */


    public function editProcess(Request $request, $id)
    {
        $article = $this
            ->getDoctrine()
            ->getRepository(Article::class)
            ->find($id);
        $form = $this->createForm(ArticleType::class);
        $form->handleRequest($request);
        $this->uploadImage($form, $article);
        $this->articleService->edit($article);
        return $this->redirectToRoute("blog_index");


    }
    /**
     * @param FormInterface $form
     * @param Article $article
     */
    private function uploadImage(FormInterface $form, Article $article)
    {
        /**
         * @var UploadedFile $file
         */
        $file = $form['image']->getData();
        $filename = md5(uniqid()) . "." . $file->guessExtension();
        if ($file) {
            $file->move(
                $this->getParameter('article_directory'),
                $filename
            );
        }
        $article->setImage($filename);
        }


}
