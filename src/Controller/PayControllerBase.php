<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\AssetsFetcher;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PayControllerBase extends BaseFrontController
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
        ];

        return $this->render($view, $parameters);
    }
}
