<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\LangHelper;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Translation\TranslatorInterface;

class LocaleRequestListener
{
    public function __construct(
        private readonly LangHelper $langHelper,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $locale = $this->langHelper->getByRequest($request) ?? $this->langHelper->detectByRequest($request);
        $request->setLocale($locale);
        $this->translator->setLocale($locale);
    }
}
