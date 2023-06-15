<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\Common\Collections\Collection;
use App\Repository\ArticleCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use app\Model\TimestampedInterface;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ArticleCategoryRepository::class)]
class ArticleCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
  
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Please enter a name.")]
    #[Assert\Length(min: 5, max: 15, minMessage: "Name have to contain at least {{ limit }} caracters",    maxMessage: "Le nom ne peut pas dépasser {{ limit }} caractères")]
    private ?string $name = null;

    #[ManyToOne(targetEntity: User::class, inversedBy: 'articleCategories')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id')]
    #[Assert\NotBlank(message: "")]
    private User|null $user = null;

    #[ORM\OneToMany(mappedBy: 'articleCategory', targetEntity: Article::class, cascade:["persist","remove"])]
    private Collection $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
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
     * @return Collection<int, Article>
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles->add($article);
            $article->setArticleCategory($this);
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        if ($this->articles->removeElement($article)) {
            // set the owning side to null (unless already changed)
            if ($article->getArticleCategory() === $this) {
                $article->setArticleCategory(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return $this->name;
    }
}