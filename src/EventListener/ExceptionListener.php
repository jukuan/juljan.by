<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Service\NavHelper;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class ExceptionListener
{
    public function __construct(
        private readonly Environment $twig,
        private readonly NavHelper $navHelper,
    )
    {
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof NotFoundHttpException) {
            $lang = $event->getRequest()->getLocale();
            $response = new Response(
                $this->twig->render('errors/404.html.twig', [
                    'lang' => $lang,
                    'navHelper' => $this->navHelper,
                ]),
                Response::HTTP_NOT_FOUND
            );

            $event->setResponse($response);
        }
    }
}
