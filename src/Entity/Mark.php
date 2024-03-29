<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\MarkRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: MarkRepository::class)]
#[UniqueEntity(
    fields: ['user', 'recipe'],
    errorPath: 'user',
    message: 'Cet utilisateur a déjà noté cette recette.'
)]
class Mark
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'marks')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'marks')]
    #[ORM\JoinColumn(nullable: false)]
    private Recipe $recipe;

    #[Assert\Positive()]
    #[Assert\LessThanOrEqual(5)]
    #[ORM\Column]
    private int $mark;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(){
        $this->createdAt = new DateTimeImmutable();
    }
    
    public function getId(): ?int
    {
        return $this->id;
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

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getMark(): ?int
    {
        return $this->mark;
    }

    public function setMark(int $mark): self
    {
        $this->mark = $mark;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
