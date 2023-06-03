<?php

declare(strict_types=1);

namespace App\Service\Transformer;

use League\CommonMark\CommonMarkConverter;
use League\CommonMark\Exception\CommonMarkException;
use League\CommonMark\Exception\IOException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ArticleToHtmlTransformer
{
    private string $sourceDir;
    private string $targetDir;

    public function __construct(ParameterBagInterface $parameters)
    {
        $this->sourceDir = $parameters->get('kernel.project_dir') . '/articles';
        $this->targetDir = $parameters->get('kernel.cache_dir') . '/articles';
    }

    /**
     * @throws CommonMarkException
     */
    public function convertFile(string $filePath): string
    {
        $content = file_exists($filePath) ? file_get_contents($filePath) : null;

        if (null === $content) {
            throw new IOException('Not Found');
        }

        return (new CommonMarkConverter())->convert($content)->getContent();
    }

    public function process(?callable $fn = null): void
    {
        $logInfo = function (string $text) use ($fn): void {
            if (null !== $fn) {
                $fn($text);
            }
        };

        $directories = $this->getFileList($this->sourceDir);

        foreach ($directories as $lang) {
            $srcLangDir = $this->sourceDir . '/' . $lang;
            $articles = $this->getFileList($srcLangDir);
            $fileIndex = [];

            foreach ($articles as $mdFile) {
                $mdFilePath = $srcLangDir . '/' . $mdFile;

                try {
                    $output = $this->convertFile($mdFilePath);
                } catch (CommonMarkException $e) {
                    $output = null;
                    $logInfo('Exception: ' . $e->getMessage());
                }

                if ($output) {
                    $fileName = str_replace('.md', '', $mdFile);
                    $htmlFile = $fileName . '.html';
                    $targetFilePath = sprintf('%s/%s/%s', $this->targetDir, $lang, $htmlFile);
                    $this->prepareFilePath($targetFilePath);

                    $fileIndex[$fileName] = [
                        'title' => $this->getTitleFromHtml($output) ?: $fileName,
                        'date' => date('Y-m-d', filemtime($mdFilePath)),
                    ];
                    file_put_contents($targetFilePath, $output);
                    $logInfo(sprintf('Success: file `%s` for lang \'%s\'', $mdFile, $lang));
                }
            }

            file_put_contents(sprintf('%s/%s/%s', $this->targetDir, $lang, '_index.json'), json_encode($fileIndex));
        }
    }

    public function getHtmlBody(string $lang, string $slug): ?string
    {
        $path = sprintf('%s/%s/%s', $this->targetDir, $lang, $slug . '.html');

        return file_exists($path) ? file_get_contents($path) : null;
    }

    public function getIndex(string $lang): array
    {
        $path = sprintf('%s/%s/%s', $this->targetDir, $lang, '_index.json');

        $json = file_exists($path) ? file_get_contents($path) : null;

        return $json ? (array)json_decode($json, true) : [];
    }

    public function getTitleFromHtml(string $hml): ?string
    {
        $hml = str_replace("\r\n", PHP_EOL, $hml);
        $hml = str_replace("\n", PHP_EOL, $hml);
        $hml = ltrim($hml);
        $parts = explode(PHP_EOL, $hml);

        if (isset($parts[0])) {
            return trim(strip_tags($parts[0]));
        }

        return null;
    }

    /**
     * @param string $path
     *
     * @return string[]
     */
    private function getFileList(string $path): array
    {
        return array_diff(scandir($path), ['.', '..']);
    }

    private function prepareFilePath(string $filePath): void
    {
        $dir = dirname($filePath);

        if (!file_exists($dir)) {
            mkdir($dir, 0755);
        }

        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }
}
