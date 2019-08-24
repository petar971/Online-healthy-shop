<?php


namespace AppBundle\Service\Article;


use AppBundle\Entity\Article;
use AppBundle\Entity\User;
use AppBundle\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Security;

class ArticleService implements ArticleServiceInterface
{

    private $articleRepository;
    private $security;

    public function __construct(ArticleRepository $articleRepository,Security $security)
    {
$this->articleRepository=$articleRepository;
$this->security=$security;
    }

    public function edit(Article $article)
    {
       $this->articleRepository->update($article);
    }

    public function create(Article $article)

    {
        $currentUser=$this->security->getUser();
        /** @var User $currentUser */
        $article->setAuthor($currentUser);
        $this->articleRepository->insert($article);
    }

    public function delete(Article $article)
    {
       $this->articleRepository->remove($article);
    }

    public function getOne($id)
    {
      $this->articleRepository->find($id);
    }


    /**
     * @return ArrayCollection|Article[]
     */
    public function getAll()
    {
       return $this->articleRepository->findAll();
    }
}