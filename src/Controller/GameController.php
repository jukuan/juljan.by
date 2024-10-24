<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\NavHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as TwigEnvironment;

class GameController extends BaseFrontController
{
    #[Route('/{lang}/game/{slug}', name: 'site_game_view')]
    public function view(string $lang, string $slug): Response
    {
        return $this->render($lang.'/game/'.$slug.'.html.twig', [
            'lang' => $lang,
        ]);
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
        $view = sprintf('%s/game/%s.html.twig', $lang, $view);

        if (!$this->twig->getLoader()->exists($view)) {
            throw new NotFoundHttpException(sprintf('Template "%s" not found', $view));
        }

        return $view;
    }
}
