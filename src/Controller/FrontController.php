<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AssetsFetcher;
use App\Service\LangHelper;
use App\Service\NavHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as TwigEnvironment;

class FrontController extends AbstractController
{
    public function __construct(
        protected readonly AssetsFetcher $assetsFetcher,
        protected readonly EntityManagerInterface $em,
        protected readonly LangHelper $langHelper,
        protected readonly NavHelper $navHelper,
        protected readonly TwigEnvironment $twig,
    )
    {
    }
    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $parameters['payLogos'] = $this->assetsFetcher->getPayLogoSrc();

        return parent::render($view, $parameters, $response);
    }
}
