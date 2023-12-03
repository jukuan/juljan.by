<?php

declare(strict_types=1);

namespace App\Service;

use App\View\NavItem;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class NavHelper
{
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly RequestStack $requestStack,
    )
    {
    }

    private const SITE_PAGES = [
        'services',
        'prices',
        'faq',
        'examples',
        'samples',
        'about',
        'contacts',
        'think-tech',
    ];

    private const NAV_HEADER = [
        'services',
        'prices',
        'about',
        'contacts',
    ];

    public function isSitePage(string $page): bool
    {
        return in_array($page, self::SITE_PAGES, true);
    }

    public function getHref(string $href): string
    {
        if (!$this->isSitePage($href)) {
            throw new NotFoundHttpException(sprintf('Site page "%s" not found', $href));
        }

        return sprintf('/%s/%s', $this->translator->getLocale(), $href);
    }

    public function getLang(): string
    {
        return $this->translator->getLocale();
    }

    public function isCurrentLang(string $lang): bool
    {
        return $lang === $this->translator->getLocale();
    }

    public function toLang(string $lang): string
    {
        return str_replace($this->translator->getLocale(), $lang, $this->requestStack->getMainRequest()->getRequestUri());
    }

    /**
     * @return NavItem[]
     */
    public function getHeaderItems(): array
    {
        $requestUri = $this->requestStack->getCurrentRequest()->getRequestUri();
        $requestUri = rtrim($requestUri, '/') . '/';

        $lang = sprintf('/%s/', $this->translator->getLocale());
        $requestUri = str_replace($lang, '/', $requestUri);

        $items = array_map(function (string $page) use ($requestUri) {
            $href = $this->getHref($page);
            $text = $this->translator->trans('nav.' . $page);

            return new NavItem($href, $text, $requestUri);
        }, self::NAV_HEADER);

        return [new NavItem('/', $this->translator->trans('nav.home'), $requestUri)] + $items;
    }

    public function createArticleItems(string $lang, array $articleIndex): array
    {
        $requestUri = $this->requestStack->getCurrentRequest()->getRequestUri();
        $requestUri = rtrim($requestUri, '/') . '/';
        $requestUri = str_replace($lang, '/', $requestUri);

        $items = [];

        foreach ($articleIndex as $slug => $fields) {
            $date = $fields['date'];
            $title = $fields['title'];
            $href = sprintf('/%s/art/%s', $lang, $slug);

            $items[] = (new NavItem($href, $title, $requestUri))->setDate($date);
        }

        return $items;
    }
}
