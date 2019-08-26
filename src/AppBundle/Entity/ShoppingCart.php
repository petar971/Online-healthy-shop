<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * ShoppingCart
 *
 * @ORM\Table(name="shopping_cart")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ShoppingCartRepository")
 */
class ShoppingCart
{
    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User",inversedBy="orders")
     */
    private $user;

    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product",inversedBy="order")
     */
    private $products;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return ArrayCollection
     */
    public function getProduct()
    {
        return $this->products;
    }

    /**
     * @param ArrayCollection $products
     */
    public function setProduct($products)
    {
        $this->products = $products;
    }
}

