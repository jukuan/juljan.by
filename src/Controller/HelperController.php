<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LangHelper;
use App\Service\NavHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\Request;

class HelperController extends AbstractController
{
    public function __construct(
        private readonly LangHelper $langHelper,
        private readonly NavHelper $navHelper,
        private readonly TwigEnvironment $twig,
        private readonly EntityManagerInterface $em,
    )
    {
    }

    #[Route('/{lang}/helper/txt2tbl', name: 'helper_txt2tbl')]
    public function index(Request $request): Response
    {
        if ($text = $request->get('text')) {
            $lines = explode("\n", $text);

            $lines = array_map('trim', array_filter($lines));
            $tableRows = [];
            $row = [];

            // Iterate through the lines and group them into rows with 4 columns
            foreach ($lines as $line) {
                $line = trim($line);

                if (is_numeric($line)) {
                    $row = array_pad($row, 4, '');
                    $tableRows[] = $row;
                    $row = [];
                }

                $row[] = $line;;
            }

            if ($row) {
                $row = array_pad($row, 4, '');
                $tableRows[] = $row;
            }

            $table = '<table>';

            foreach ($tableRows as $row) {
                $table .= '<tr>';
                foreach ($row as $index => $cell) {
                    if ($index === 0) {
                        // Apply special formatting for the first cell (removing whitespace)
                        $cell = trim($cell);
                    }
                    $table .= '<td>' . $cell . '</td>';
                }
                $table .= '</tr>';
            }

            $table .= '</table>';
        } else {
            $table = null;
        }

        return $this->render('be/helper/txt2tbl.html.twig', [
            'table' => $table,
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
}