<?php

namespace App\Entity;

use App\Repository\DiscountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DiscountRepository::class)
 */
class Discount
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    /**
     * @ORM\Column(type="float")
     */
    private $percentage;

    /**
     * @ORM\OneToMany(targetEntity=DiscountProduct::class, mappedBy="discount")
     */
    private $discountProducts;

    /**
     * @ORM\OneToMany(targetEntity=Order::class, mappedBy="discountCode")
     */
    private $orders;

    public function __construct()
    {
        $this->discountProducts = new ArrayCollection();
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getPercentage(): ?float
    {
        return $this->percentage;
    }

    public function setPercentage(float $percentage): self
    {
        $this->percentage = $percentage;

        return $this;
    }

    /**
     * @return Collection|DiscountProduct[]
     */
    public function getDiscountProducts(): Collection
    {
        return $this->discountProducts;
    }

    public function addDiscountProduct(DiscountProduct $discountProduct): self
    {
        if (!$this->discountProducts->contains($discountProduct)) {
            $this->discountProducts[] = $discountProduct;
            $discountProduct->setDiscount($this);
        }

        return $this;
    }

    public function removeDiscountProduct(DiscountProduct $discountProduct): self
    {
        if ($this->discountProducts->removeElement($discountProduct)) {
            // set the owning side to null (unless already changed)
            if ($discountProduct->getDiscount() === $this) {
                $discountProduct->setDiscount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Order[]
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
            $order->setDiscountCode($this);
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getDiscountCode() === $this) {
                $order->setDiscountCode(null);
            }
        }

        return $this;
    }
}
