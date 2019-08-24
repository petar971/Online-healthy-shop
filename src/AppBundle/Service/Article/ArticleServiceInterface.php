<?php


namespace AppBundle\Service\Article;


use AppBundle\Entity\Article;
use Doctrine\Common\Collections\ArrayCollection;

interface ArticleServiceInterface
{
    public function edit(Article $article);
    public function create(Article $article);
    public function delete(Article $article);
    public function getOne($id);
    /**
     * @return ArrayCollection|Article[]
     */
    public function getAll();

}