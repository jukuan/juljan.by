<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;

class LangHelper
{
    private const LANGUAGES = [
        'be',
        'en',
        'ru',
    ];

    private const LANG_DEFAULT = 'be';

    public function getByRequest(Request $request): ?string
    {
        $locale = $request->get('lang');
        $locale = $locale ?? $request->cookies->get('lang');

        return $locale ? strtolower($locale) : null;
    }

    public function detectByRequest(Request $request): string
    {
        foreach ($request->getLanguages() as $requestLang) {
            $requestLang = strtolower($requestLang);

            foreach (self::LANGUAGES as $siteLang) {
                if (str_contains($requestLang, $siteLang)) {
                    return $siteLang;
                }
            }
        }

        return self::LANG_DEFAULT;
    }

    public function createCookie(string $locale): Cookie
    {
        return new Cookie('lang', $locale, time() + 3600 * 24 * 7);
    }
}
