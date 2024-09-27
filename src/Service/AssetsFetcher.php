<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Finder\Finder;

class AssetsFetcher
{
    public function __construct(
        private readonly string $projectDir
    ) {

    }
    public function getPayLogoSrc(): array
    {
        $src = '/assets/payments/';
        $logosDir = $this->projectDir . '/public'.$src;
        $payLogos = [];

        foreach ((new Finder())->files()->in($logosDir) as $file) {
            $payLogos[] = $src . '/' . $file->getFilename();
        }

        return $payLogos;
    }
}
