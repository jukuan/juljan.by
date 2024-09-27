<?php

namespace __;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Translatable\Translatable as TranslatableTrait;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post implements TranslatableInterface
{
    use TranslatableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\OneToMany(mappedBy: 'post_id', targetEntity: PostTranslation::class)]
    private Collection $postTranslations;

    public function __construct()
    {
        $this->postTranslations = new ArrayCollection();
    }

    public function __get($name)
    {
        return PropertyAccess::createPropertyAccessor()->getValue($this->translate(), $name);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Post
    {
        $this->id = $id;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): Post
    {
        $this->type = $type;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(?\DateTimeImmutable $created_at): Post
    {
        $this->created_at = $created_at;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): Post
    {
        $this->updated_at = $updated_at;
        return $this;
    }

    /**
     * @return Collection<int, PostTranslation>
     */
    public function getPostTranslations(): Collection
    {
        return $this->postTranslations;
    }

    public function addPostTranslation(PostTranslation $postTranslation): static
    {
        if (!$this->postTranslations->contains($postTranslation)) {
            $this->postTranslations->add($postTranslation);
            $postTranslation->setPostId($this);
        }

        return $this;
    }

    public function removePostTranslation(PostTranslation $postTranslation): static
    {
        if ($this->postTranslations->removeElement($postTranslation)) {
            // set the owning side to null (unless already changed)
            if ($postTranslation->getPostId() === $this) {
                $postTranslation->setPostId(null);
            }
        }

        return $this;
    }

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        // TODO: Implement trans() method.
    }
}
