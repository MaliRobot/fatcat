<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("fatcat.order")
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 */
class Order
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderAmount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $shippingAmount;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $taxAmount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="orders")
     */
    private $customer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderAmount(): ?int
    {
        return $this->orderAmount;
    }

    public function setOrderAmount(int $orderAmount): self
    {
        $this->orderAmount = $orderAmount;

        return $this;
    }

    public function getShippingAmount(): ?int
    {
        return $this->shippingAmount;
    }

    public function setShippingAmount(?int $shippingAmount): self
    {
        $this->shippingAmount = $shippingAmount;

        return $this;
    }

    public function getTaxAmount(): ?int
    {
        return $this->taxAmount;
    }

    public function setTaxAmount(?int $taxAmount): self
    {
        $this->taxAmount = $taxAmount;

        return $this;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): self
    {
        $this->customer = $customer;

        return $this;
    }
}
