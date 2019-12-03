<?php
namespace App\Services\Scraper\Profiles;

use Symfony\Component\Console\Output\ConsoleOutput;

class Profiles
{
    /** @var Symfony\Component\Console\Output\ConsoleOutput */
    protected $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput;
    }

    public function info(string $message)
    {
        return $this->output->writeln("<info>{$message}</info>");
    }
}