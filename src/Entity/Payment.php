<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PaymentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PaymentRepository::class)]
class Payment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id = 0;

    #[ORM\Column(length: 7, nullable: false, options: ['default' => '1.00',])]
    private string $sum = '1.00';

    #[ORM\Column(length: 127, nullable: false, options: ['default' => '',])]
    private string $service = '';

    #[ORM\Column(length: 63, nullable: false, options: ['default' => '', ])]
    private string $email = '';

    #[ORM\Column(length: 31, nullable: false, options: ['default' => '', ])]
    private string $phone = '';

    #[ORM\Column(type: Types::TEXT, nullable: false, options: ['default' => '', ])]
    private string $text = '';

    #[ORM\ManyToOne(inversedBy: 'payments')]
    private ?Product $product = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt ;

    #[ORM\Column(length: 127)]
    private ?string $trackingId = null;

    #[ORM\Column(nullable: true)]
    private ?array $params = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getSum(): string
    {
        return $this->sum;
    }

    public function setSum(null|string|int|float $sum): static
    {
        $this->sum = (string)$sum;

        return $this;
    }

    public function getService(): string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;

        return $this;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        if (null !== $product) {
            if (!$this->sum) {
                $this->setSum($product->getPrice());
            }

            if (!$this->service) {
                $this->setService($product->getName());
            }
        }

        return $this;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(?string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getTrackingId(): ?string
    {
        return $this->trackingId;
    }

    public function setTrackingId(string $trackingId): static
    {
        $this->trackingId = $trackingId;

        return $this;
    }

    public function getParams(): ?array
    {
        return $this->params;
    }

    public function setParams(array $params): static
    {
        $this->params = $params;

        return $this;
    }

    public function addKeyParams(string $key, array $params): static
    {
        $this->params[$key] = $params;

        return $this;
    }
}
