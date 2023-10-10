<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\Request;

class ActController extends AbstractController
{
    private const SLUGS = [
        '2023|09_tnn' => [
            'dateRange' => '2023-09-09 - 2023-10-11',
            'description' => 'Дапрацоўкі зробленыя для сайта <a href="https://tn.by">tn.by</a>',
            'rate' => 18,
            'currency' => 'р',
            'jobs' => [
                [
                    'name' => 'Выпраўленне фільтара тавараў у каталогу',
                    'hours' => 5,
                ],
                [
                    'name' => 'Выпраўленне выпадзення формы купонаў',
                    'hours' => 2,
                ],
                [
                    'name' => 'Выпраўленне вёрсткі шрыфтоў і кнопак на ст. афармлення замовы',
                    'hours' => 6,
                ],
                [
                    'name' => 'Праўка вёрсткі на старонцы тавару і кошыку',
                    'hours' => 6,
                ],
                [
                    'name' => 'Выпраўленне прагала і выгляду футара (ніз сайта)',
                    'hours' => 3,
                ],
                [
                    'name' => 'Выпраўленне зламанай вёрсткі на галоўнай старонцы',
                    'hours' => 4,
                ],
                [
                    'name' => 'Аптымізацыя адмінкі',
                    'hours' => 2,
                ],
                [
                    'name' => 'Выпраўленне опцый у варыятыўных тавараў',
                    'hours' => 2,
                ],
                [
                    'name' => 'Адаптацыя існуючага механізму перакладаў',
                    'hours' => 2,
                ],
                [
                    'name' => 'Асобны механізм аўта-перакладаў',
                    'hours' => 6,
                ],
            ],
        ],
    ];

    #[Route('/act/{slug}', name: 'act_view')]
    public function view(string $slug): Response
    {
        $details = self::SLUGS[$slug] ?? null;
        $jobs = $details['jobs'] ?? [];
        $details['slug'] = $slug;
        $slug = str_replace('|', '/', $slug);

        if ([] === $jobs || null === $details) {
            // 404
        }

        unset($details['jobs']);
        $rate = $details['rate'];
        $details['hours'] = $details['sum'] = 0;

        foreach ($jobs as &$jobFields) {
            $hours = $jobFields['hours'];
            $price = $hours * $rate;
            $jobFields['price'] = $price;
            $details['hours'] += $hours;
            $details['sum'] += $price;
        }

        return $this->render('act/'.$slug.'.html.twig', [
            'jobs' => $jobs,
            'details' => $details,
            'currency' => $details['currency'] ?? '',
        ]);
    }
}