<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;

class ProductsFetcher
{
    private string $lang = '';

    public function __construct(
        protected readonly EntityManagerInterface $em,
    ) {

    }

    public function setLang(string $lang): self
    {
        $this->lang = $lang;

        return $this;
    }

    public function getProducts(): iterable
    {
        return $this->em->getRepository(Product::class)->findBy(['lang' => $this->lang]);
    }

    public function getServices(): iterable
    {
        return $this->em->getRepository(Product::class)->findBy(['lang' => $this->lang]);
    }
}
