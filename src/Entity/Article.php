<?php

namespace App\Entity;

use App\Entity\Vote;
use Assert\NotBlank;
use Doctrine\DBAL\Types\Types;
use App\Entity\ArticleCategory;
use Doctrine\ORM\Mapping as ORM;
use App\Model\TimestampedInterface;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article 
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Please enter a title.")]
    #[Assert\Length(min:5,max:30,minMessage:"Le nom doit comporter au moins {{ limit }} caractères",    maxMessage:"Le nom ne peut pas dépasser {{ limit }} caractères")]
   
    private ?string $title = null;
    #[Assert\NotBlank(message: "Please enter a featured Text")]
    #[Assert\Length(min:5,max:30,minMessage:"Le nom doit comporter au moins {{ limit }} caractères",    maxMessage:"Le nom ne peut pas dépasser {{ limit }} caractères")]


    #[ORM\Column(length: 255)]
    private ?string $featuredText = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Please enter a Content")]
    #[Assert\Length(min:5,max:100,minMessage:"Le nom doit comporter au moins {{ limit }} caractères",    maxMessage:"Le nom ne peut pas dépasser {{ limit }} caractères")]


   
    private ?string $content = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeInterface $updatedAt;

    #[ManyToOne(targetEntity: ArticleCategory::class, inversedBy: 'articles')]
    #[JoinColumn(name: 'articleCategory_id', referencedColumnName: 'id', onDelete:"CASCADE")]
    #[Assert\NotBlank(message: "")]

    private ArticleCategory|null $articleCategory = null;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Comment::class, cascade:["persist","remove"])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Vote::class, cascade:["persist","remove"])]
    private Collection $votes;

    #[ORM\OneToMany(mappedBy: 'article', targetEntity: Rating::class)]
    private Collection $ratings;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $etat = null;

    public function __construct(){
        $this->createdAt=new \DateTimeImmutable();
        $this->updatedAt=new \DateTimeImmutable();
        $this->comments = new ArrayCollection();
        $this->votes = new ArrayCollection();
        $this->ratings = new ArrayCollection();
        
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFeaturedText(): ?string
    {
        return $this->featuredText;
    }

    public function setFeaturedText(string $featuredText): self
    {
        $this->featuredText = $featuredText;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = new \DateTimeImmutable();

        return $this;
    }

    public function getArticleCategory(): ?ArticleCategory
    {
        return $this->articleCategory;
    }

    public function setArticleCategory(?ArticleCategory $articleCategory): self
    {
        $this->articleCategory = $articleCategory;

        return $this;
    }
    public function __toString() {
        return $this->articleCategory;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

   /* public function addVote(Vote $vote): self
    {
        if (!$this->votes->contains($vote)) {
            $this->votes->add($vote);
            $vote->setArticle($this);
        }

        return $this;
    }

    public function removeVote(Vote $vote): self
    {
        if ($this->votes->removeElement($vote)) {
            // set the owning side to null (unless already changed)
            if ($vote->getArticle() === $this) {
                $vote->setArticle(null);
            }
        }

        return $this;
    }
    public function getPositiveVotesCount(): int
    {
        return $this->votes->filter(function (Vote $vote) {
            return $vote->getValue() > 0;
        })->count();
    }

    public function getNegativeVotesCount(): int
    {
        return $this->votes->filter(function (Vote $vote) {
            return $vote->getValue() < 0;
        })->count();
    }*/

    /**
     * @return Collection<int, Rating>
     */
    public function getRatings(): Collection
    {
        return $this->ratings;
    }

    public function addRating(Rating $rating): self
    {
        if (!$this->ratings->contains($rating)) {
            $this->ratings->add($rating);
            $rating->setArticle($this);
        }

        return $this;
    }

    public function removeRating(Rating $rating): self
    {
        if ($this->ratings->removeElement($rating)) {
            // set the owning side to null (unless already changed)
            if ($rating->getArticle() === $this) {
                $rating->setArticle(null);
            }
        }

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

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    
}