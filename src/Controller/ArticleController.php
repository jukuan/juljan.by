<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\NavHelper;
use App\Service\Transformer\ArticleToHtmlTransformer;
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    public function __construct(
        private readonly NavHelper $navHelper,
        private readonly ArticleToHtmlTransformer $articleToHtml,
    )
    {
    }

    #[Route('/{lang}/art/{slug}', name: 'article_post')]
    public function page(string $lang, string $slug): Response
    {
        $articleBody = $this->articleToHtml->getHtmlBody($lang, $slug);

        if (null === $articleBody) {
            $this->articleToHtml->process();
            $articleBody = $this->articleToHtml->getHtmlBody($lang, $slug);
        }

        if (null === $articleBody) {
            throw new NotFoundHttpException('Not Found');
        }

        $articleIndex = $this->articleToHtml->getIndex($lang);
        $articleList = $this->navHelper->createArticleItems($lang, $articleIndex);
        $articleTitle = $this->articleToHtml->getTitleFromHtml($articleBody) ?: $slug;

        return $this->render('art/post.html.twig', [
            'lang' => $lang,
            'navHelper' => $this->navHelper,
            'articleBody' => $articleBody,
            'articleList' => $articleList,
            'articleTitle' => $articleTitle,
        ]);
    }
}
