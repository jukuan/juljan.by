<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
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

class SiteController extends FrontController
{
    #[Route('/', name: 'site_index')]
    public function index(Request $request): Response
    {
        $locale = $request->getLocale();
        $response = $this->redirect('/' . $locale);
        $response->headers->setCookie($this->langHelper->createCookie($locale));

        return $response;
    }

    #[Route('/{lang}', name: 'site_home')]
    public function home(string $lang): Response
    {
        return $this->renderLangView($lang, 'home', [
            'isHome' => true,
        ]);
    }

    #[Route('/{lang}/prices', name: 'prices_page')]
    public function pagePrices(string $lang): Response
    {
        $products = $this->em->getRepository(Product::class)->findBy([
            'lang' => $lang,
        ]);

        return $this->renderLangView($lang, 'prices', [
            'products' => $products,
        ]);
    }

    #[Route('/{lang}/{page}', name: 'site_page')]
    public function page(string $lang, string $page): Response
    {
        if (!$this->navHelper->isSitePage($page)) {
            throw new NotFoundHttpException(sprintf('Site page "%s" not found', $page));
        }

        return $this->renderLangView($lang, $page);
    }

    private function renderLangView(string $lang, string $view, array $parameters = []): Response
    {
        $view = $this->getLangView($lang, $view);
        $parameters = array_merge($parameters, [
            'lang' => $lang,
            'navHelper' => $this->navHelper,
            'payLogos' => $this->assetsFetcher->getPayLogoSrc(),
        ]);

        return $this->render($view, $parameters);
    }

    private function getLangView(string $lang, string $view): string
    {
        $view = sprintf('%s/%s.html.twig', $lang, $view);

        if (!$this->twig->getLoader()->exists($view)) {
            throw new NotFoundHttpException(sprintf('Template "%s" not found', $view));
        }

        return $view;
    }
}
