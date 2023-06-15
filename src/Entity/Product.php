<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Please enter your name')]
    #[Assert\Length(
        max: 60, 
        maxMessage: "The name is way too long")]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Please enter a description for the product')]
    #[Assert\Length(
        min: 10, 
        max: 255, 
        minMessage: "The descriptionn is way too short",
        maxMessage: "The description is way too long")]
    private ?string $description = null;

    #[ORM\Column(length: 255)]

    private ?string $image = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'Please enter a pricing tag')]
    #[Assert\Range(
        min: 1,
        max: 100000,
        minMessage: "The price must be > than {{ limit }}",
        maxMessage: "The price must be < than {{ limit }}"
    )]
    private ?float $price = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'Please enter a rating')]
    #[Assert\Range(
        min: 0,
        minMessage: 'The Stock must be a positive value'
    )]
    private ?int $stock = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:'Please enter a rating')]
    #[Assert\Range(
        min: 1,
        max: 5,
        minMessage: "The price must be > than {{ limit }}",
        maxMessage: "The price must be < than {{ limit }}"
    )]
    private ?float $rating = null;

    #[ManyToOne(targetEntity: ProductCategory::class, inversedBy: 'products')]
    #[JoinColumn(name: 'productCategory_id', referencedColumnName: 'id', onDelete:"CASCADE")] 
    private ProductCategory|null $productCategory = null;

    #[ORM\OneToMany(mappedBy: 'product', targetEntity: CartProduct::class)]
    private Collection $cartProducts;

    public function __construct()
    {
        $this->cartProducts = new ArrayCollection();
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

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getProductCategory(): ?ProductCategory
    {
        return $this->productCategory;
    }

    public function setProductCategory(?ProductCategory $productCategory): self
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    /**
     * @return Collection<int, CartProduct>
     */
    public function getCartProducts(): Collection
    {
        return $this->cartProducts;
    }

    public function addCartProduct(CartProduct $cartProduct): self
    {
        if (!$this->cartProducts->contains($cartProduct)) {
            $this->cartProducts->add($cartProduct);
            $cartProduct->setProduct($this);
        }

        return $this;
    }

    public function removeCartProduct(CartProduct $cartProduct): self
    {
        if ($this->cartProducts->removeElement($cartProduct)) {
            // set the owning side to null (unless already changed)
            if ($cartProduct->getProduct() === $this) {
                $cartProduct->setProduct(null);
            }
        }

        return $this;
    }
    
}
