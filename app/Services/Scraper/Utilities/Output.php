<?php
namespace App\Services\Scraper\Utilities;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Output
{
    /** @var Symfony\Component\Console\Output\ConsoleOutput */
    protected $output;

    public function __construct()
    {
        $this->output = new ConsoleOutput;
    }

    public function info(string $message)
    {
        $this->output->writeln("<info>{$message}</info>");
    }

    public function warning(string $message) 
    {   
        if (! $this->output->getFormatter()->hasStyle('warning')) {            
            $style = new OutputFormatterStyle('yellow');

            $this->output->getFormatter()->setStyle('warning', $style);
        }
        
        $this->output->writeln("<warning>{$message}</warning>");
    }

    public function error(string $message)
    {
        $this->output->writeln("<error>{$message}</error>");
    }
}