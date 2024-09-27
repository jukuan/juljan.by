<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AssetsFetcher;
use App\Service\LangHelper;
use App\Service\NavHelper;
use App\Service\ProductsFetcher;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;

abstract class BaseFrontController extends AbstractController
{
    private const DEFAULT_LANG = 'ru';

    public function __construct(
        protected readonly AssetsFetcher $assetsFetcher,
        protected readonly EntityManagerInterface $em,
        protected readonly LangHelper $langHelper,
        protected readonly NavHelper $navHelper,
        protected readonly TwigEnvironment $twig,
        protected readonly ProductsFetcher $productsFetcher,
        protected readonly RequestStack $requestStack,
        protected readonly TranslatorInterface $translator,
    ) {
    }

    protected function getRequest(): ?\Symfony\Component\HttpFoundation\Request
    {
        return $this->requestStack->getCurrentRequest();
    }

    protected function getLang(array $parameters): string
    {
        $lang = $parameters['lang'] ?? null;

        if (null === $lang) {
            $lang = $this->getRequest()->get('lang');
        }

        if (null === $lang) {
            $lang = $this->getRequest()->getLocale();
        }

        if ($lang) {
            $lang = trim($lang);
            $lang = strtolower($lang);
        }

        return $lang ?: self::DEFAULT_LANG;
    }

    protected function render(string $view, array $parameters = [], ?Response $response = null): Response
    {
        $this->productsFetcher->setLang($this->getLang($parameters));

        $parameters['payLogos'] = $this->assetsFetcher->getPayLogoSrc();
        $parameters['products'] = $this->productsFetcher->getProducts();
        $parameters['services'] = $this->productsFetcher->getServices();

        return parent::render($view, $parameters, $response);
    }
}