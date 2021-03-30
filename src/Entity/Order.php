<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $total;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="boolean")
     */
    private $discountUsed;

    /**
     * @ORM\ManyToOne(targetEntity=Discount::class, inversedBy="orders")
     */
    private $discountCode;

    /**
     * @ORM\ManyToOne(targetEntity=Customer::class, inversedBy="orders")
     */
    private $customer;

    /**
     * @ORM\OneToMany(targetEntity=Row::class, mappedBy="theOrder")
     */
    private $rows2;

    public function __construct()
    {
        $this->rows2 = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDiscountUsed(): ?bool
    {
        return $this->discountUsed;
    }

    public function setDiscountUsed(bool $discountUsed): self
    {
        $this->discountUsed = $discountUsed;

        return $this;
    }

    public function getDiscountCode(): ?Discount
    {
        return $this->discountCode;
    }

    public function setDiscountCode(?Discount $discountCode): self
    {
        $this->discountCode = $discountCode;

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

    /**
     * @return Collection|Row[]
     */
    public function getRows2(): Collection
    {
        return $this->rows2;
    }

    public function addRows2(Row $rows2): self
    {
        if (!$this->rows2->contains($rows2)) {
            $this->rows2[] = $rows2;
            $rows2->setTheOrder($this);
        }

        return $this;
    }

    public function removeRows2(Row $rows2): self
    {
        if ($this->rows2->removeElement($rows2)) {
            // set the owning side to null (unless already changed)
            if ($rows2->getTheOrder() === $this) {
                $rows2->setTheOrder(null);
            }
        }

        return $this;
    }
}
