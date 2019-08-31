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
        $this->quantity=1;
        $this->isReady=false;
        $this->status=false;
        $this->dateAdded = new \DateTime('now');
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
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean")
     */
    private $status;



    /**
     * @var ArrayCollection
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Product",inversedBy="order")
     */
    private $products;
    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\UserAddress",inversedBy="orders")
     */
    private $address;

    /**
     * @var integer
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;
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

    /**
     * @return bool
     */
    public function isStatus()
    {
        return $this->status;
    }

    /**
     * @param bool $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return bool
     */
    public function isReady()
    {
        return $this->isReady;
    }

    /**
     * @param bool $isReady
     */
    public function setIsReady($isReady)
    {
        $this->isReady = $isReady;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
}

