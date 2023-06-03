<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LangHelper;
use App\Service\NavHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment as TwigEnvironment;

class SiteController extends AbstractController
{
    public function __construct(
        private readonly LangHelper $langHelper,
        private readonly NavHelper $navHelper,
        private readonly TwigEnvironment $twig,
    )
    {
    }

    #[Route('/', name: 'site_index')]
    public function index(Request $request, TranslatorInterface $translator): Response
    {
        $locale = $request->getLocale();
        $response = $this->redirect('/' . $locale);
        $response->headers->setCookie($this->langHelper->createCookie($locale));

        return $response;
    }

    #[Route('/{lang}', name: 'site_home')]
    public function home(string $lang): Response
    {
        return $this->renderLangView($lang, 'home');
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
