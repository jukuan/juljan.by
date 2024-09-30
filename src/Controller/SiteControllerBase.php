<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteControllerBase extends BaseFrontController
{
    #[Route('/', name: 'site_index')]
    public function index(Request $request): Response
    {
        $lang = $request->getLocale();
        $lang = $this->prepareLang($lang);

        $response = $this->redirect('/' . $lang);
        $response->headers->setCookie($this->langHelper->createCookie($lang));

        return $response;
    }

    #[Route('/{lang}', name: 'site_home')]
    public function home(string $lang): Response
    {
        $lang = $this->prepareLang($lang);

        return $this->renderLangView($lang, 'home', [
            'isHome' => true,
        ]);
    }

    #[Route('/{lang}/prices', name: 'prices_page')]
    public function pagePrices(string $lang): Response
    {
        $lang = $this->prepareLang($lang);
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
        $lang = $this->prepareLang($lang);

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
        ]);

        return $this->render($view, $parameters);
    }

    private function getLangView(string $lang, string $file): string
    {
        $view = sprintf('%s/%s.html.twig', $lang, $file);

        if (!$this->twig->getLoader()->exists($view)) {
            $view = sprintf('%s/%s.twig', $lang, $file);

            if (!$this->twig->getLoader()->exists($view)) {
                throw new NotFoundHttpException(sprintf('Template "%s" not found', $view));
            }
        }

        return $view;
    }
}
