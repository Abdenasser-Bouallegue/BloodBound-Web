<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Article;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VoteRepository;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\JoinColumn;

#[ORM\Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column] 
    private ?int $id = null;

    #[ManyToOne(targetEntity: Article::class, inversedBy: 'votes')]
    #[JoinColumn(name: 'article_id', referencedColumnName: 'id', onDelete:"CASCADE")]
    private ?Article $article = null;

    #[ORM\Column]
    private ?int $value = null;

    #[OneToOne(targetEntity: User::class, inversedBy: 'vote')]
    #[JoinColumn(name: 'user_id', referencedColumnName: 'id', onDelete:"CASCADE")]
    private ?User $user = null;

    #[ORM\ManyToOne(targetEntity: Comment::class, inversedBy: 'vote')]
    private ?Comment $comment = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

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

    public function getComment(): ?Comment
    {
        return $this->comment;
    }

    public function setComment(?Comment $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}