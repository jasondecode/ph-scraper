<?php
namespace App\Services\Scraper\Utilities;

use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

class Output
{
    /** @var Symfony\Component\Console\Output\ConsoleOutput */
    protected $consoleOutput;

    public function __construct(ConsoleOutput $consoleOutput)
    {
        $this->consoleOutput = $consoleOutput;
    }

    public function info(string $message)
    {
        $this->consoleOutput->writeln("<info>{$message}</info>");
    }

    public function warning(string $message) 
    {   
        if (! $this->consoleOutput->getFormatter()->hasStyle('warning')) {            
            $style = new OutputFormatterStyle('yellow');

            $this->consoleOutput->getFormatter()->setStyle('warning', $style);
        }
        
        $this->consoleOutput->writeln("<warning>{$message}</warning>");
    }

    public function error(string $message)
    {
        $this->consoleOutput->writeln("<error>{$message}</error>");
    }
}