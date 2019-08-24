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
