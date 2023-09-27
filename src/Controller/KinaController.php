<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment as TwigEnvironment;
use Symfony\Component\HttpFoundation\Request;

class KinaController extends AbstractController
{
    #[Route('/kina/kong', name: 'kina_kong')]
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

            // Generate the HTML table
            $html = '<table>';

            foreach ($tableRows as $row) {
                $html .= '<tr>';
                foreach ($row as $index => $cell) {
                    if ($index === 0) {
                        // Apply special formatting for the first cell (removing whitespace)
                        $cell = trim($cell);
                    }
                    $html .= '<td>' . $cell . '</td>';
                }
                $html .= '</tr>';
            }

            $html .= '</table>';

            return $this->render('be/kina/kong.html.twig', [
                'tableHtml' => $html,
            ]);
        }

        return $this->render('be/kina/kong.html.twig');
    }
}