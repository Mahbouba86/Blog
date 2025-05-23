<?php

namespace App\Entity;

use App\Repository\ArticleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\HasLifecycleCallbacks] // Gestion auto des évènements par Doctrine
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Assert\Length(min: 2, max: 100, message: 'Le titre contient au minimum {{ min }} caractères et au maximum {{ max }} caractères')]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, message: '{{ max }} caractères maximum')]
    #[Assert\Regex(pattern: '/^[a-z0-9-]+$/')]
    private ?string $slug = null;

    #[ORM\Column(length: 255)]
    #[Assert\Length(max: 255, message: '{{ max }} caractères maximum')]
    #[Assert\Regex(pattern: '/\.(jpg|jpeg|png|webp)$/')]
    private ?string $image = 'default.png';

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, message: '{{ max }} caractères maximum')]
    private ?string $keywords = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(max: 255, message: '{{ max }} caractères maximum')]
    private ?string $description = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    #[ORM\Column]
    private ?bool $is_published = false;

    #[ORM\Column]
    private ?bool $is_archived = false;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    /**
     * @var Collection<int, Block>
     */
    #[ORM\ManyToMany(targetEntity: Block::class, mappedBy: 'articles')]
    private Collection $blocks;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, Comment>
     */
    #[ORM\OneToMany(targetEntity: Comment::class, mappedBy: 'article', orphanRemoval: true)]
    private Collection $comments;

    public function __construct()
    {
        $this->blocks = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    /**
     * Les évènements du cycle de vie de l'entité
     * La mise à jour des dates de création et de modification de l'entité
     */
    #[ORM\PrePersist] // Premier enregistrement d'un objet de l'entité
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate] // Modification d'un objet de l'entité
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getKeywords(): ?string
    {
        return $this->keywords;
    }

    public function setKeywords(?string $keywords): static
    {
        $this->keywords = $keywords;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function isPublished(): ?bool
    {
        return $this->is_published;
    }

    public function setIsPublished(bool $is_published): static
    {
        $this->is_published = $is_published;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->is_archived;
    }

    public function setIsArchived(bool $is_archived): static
    {
        $this->is_archived = $is_archived;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, Block>
     */
    public function getBlocks(): Collection
    {
        return $this->blocks;
    }

    public function addBlock(Block $block): static
    {
        if (!$this->blocks->contains($block)) {
            $this->blocks->add($block);
            $block->addArticle($this);
        }

        return $this;
    }

    public function removeBlock(Block $block): static
    {
        if ($this->blocks->removeElement($block)) {
            $block->removeArticle($this);
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setArticle($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getArticle() === $this) {
                $comment->setArticle(null);
            }
        }

        return $this;
    }
}