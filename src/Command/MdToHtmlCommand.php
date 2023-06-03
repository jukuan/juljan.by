<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\Transformer\ArticleToHtmlTransformer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'md2html',
    description: 'Converts MD articles to html file.',
)]
class MdToHtmlCommand extends Command
{
    public function __construct(
        private readonly ArticleToHtmlTransformer $articleToHtmlTransformer,
        string $name = null
    )
    {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this
            ->addOption('lang', 'l', InputOption::VALUE_OPTIONAL, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($input->getOption('lang')) {
            // ... TODO
        }

        $this->articleToHtmlTransformer->process(
            fn(string $msg) => $io->info($msg)
        );

        return Command::SUCCESS;
    }
}
