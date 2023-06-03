<?php

declare(strict_types=1);

namespace App\View;

class NavItem
{
    public function __construct(
        private readonly string $href,
        private readonly string $text,
        private readonly string $requestUri = '/',
    )
    {

    }

    public function isActive(): bool
    {
        return $this->href === $this->requestUri;
    }

    public function getHref(): string
    {
        return $this->href;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
