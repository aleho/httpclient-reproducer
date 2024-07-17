<?php

namespace App\Command;

use App\FileDownloader\FileDownloader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;

#[AsCommand(
    name: 'app:demo',
)]
class DemoCommand extends Command
{
    public function __construct(
        private readonly FileDownloader $downloader,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'URL to request');
    }

    /**
     * @throws \Throwable
     */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $file = new File(tempnam(sys_get_temp_dir(), 'demo'));

        try {
            $result = $this->downloader->download($input->getArgument('url'), $file);
        } catch (\Throwable $t) {
            if (file_exists($file->getPathname())) {
                unlink($file->getPathname());
            }

            throw $t;
        }

        $output->writeln("Type: $result->contentType");

        return self::SUCCESS;
    }
}
