<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\Collection;
use App\Repository\ProductCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Product;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductCategoryRepository::class)]
class ProductCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Please enter the category name')]
    #[Assert\Length(
        max: 60, 
        maxMessage: "The name is way too long")]
    private ?string $name = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'productCategories')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    private User|null $user = null;

    #[ORM\OneToMany(mappedBy: 'productCategory', targetEntity: Product::class)]
    private Collection $products;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message:'Please enter a description for the product category')]
    #[Assert\Length(
        min: 10, 
        max: 255, 
        minMessage: "The descriptionn is way too short",
        maxMessage: "The description is way too long")]
    private ?string $description = null;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setProductCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getProductCategory() === $this) {
                $product->setProductCategory(null);
            }
        }

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
}
