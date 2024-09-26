<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AssetsFetcher;
use App\Service\LangHelper;
use App\Service\NavHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\Finder\Finder;

class PayController extends FrontController
{
    #[Route('/pay/{page}', name: 'pay_page')]
    public function payPage(string $page, AssetsFetcher $assetsFetcher): Response
    {
        $view = sprintf('pay/%s.html.twig', $page);

        if (!$this->twig->getLoader()->exists($view)) {
            throw new NotFoundHttpException(sprintf('Template "%s" not found', $view));
        }

        $lang = 'ru';
        $parameters = [
            'lang' => $lang,
            'navHelper' => $this->navHelper,
            'payLogos' => $assetsFetcher->getPayLogoSrc(),
        ];

        return $this->render($view, $parameters);
    }
}
