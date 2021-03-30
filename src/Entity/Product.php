<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
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
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=DiscountProduct::class, mappedBy="product")
     */
    private $discountProducts;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Row::class, mappedBy="product")
     */
    private $rows2;

    public function __construct()
    {
        $this->discountProducts = new ArrayCollection();
        $this->rows2 = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
            $discountProduct->setProduct($this);
        }

        return $this;
    }

    public function removeDiscountProduct(DiscountProduct $discountProduct): self
    {
        if ($this->discountProducts->removeElement($discountProduct)) {
            // set the owning side to null (unless already changed)
            if ($discountProduct->getProduct() === $this) {
                $discountProduct->setProduct(null);
            }
        }

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
            $rows2->setProduct($this);
        }

        return $this;
    }

    public function removeRows2(Row $rows2): self
    {
        if ($this->rows2->removeElement($rows2)) {
            // set the owning side to null (unless already changed)
            if ($rows2->getProduct() === $this) {
                $rows2->setProduct(null);
            }
        }

        return $this;
    }
}
