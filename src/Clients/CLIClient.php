<?php namespace WordPressPluginFeed\Clients;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;

class CLIClient extends Command
{
    protected function configure()
    {
        $this->setName('generate');
        $this->setDescription('Generate a feed');

        $this->addOption
        (
            'plugin', null,
            InputOption::VALUE_REQUIRED,
            'Plugin name'
        );

        $this->addOption
        (
            'stability', null,
            InputOption::VALUE_OPTIONAL,
            'One o more stability options (any, stable, alpha, beta, rc) ' .
            'separated by commas'
        );

        $this->addOption
        (
            'format', null,
            InputOption::VALUE_OPTIONAL,
            'Output format (atom or rss)'
        );

        $this->addOption
        (
            'filter', null,
            InputOption::VALUE_OPTIONAL,
            'Terms to match against title and content'
        );

        $this->addOption
        (
            'categories', null,
            InputOption::VALUE_OPTIONAL,
            'User defined categories'
        );

        $this->addOption
        (
            'limit', null,
            InputOption::VALUE_OPTIONAL,
            'Number of releases on output'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $plugin     = $input->getOption('plugin');
        $stability  = $input->getOption('stability');
        $format     = $input->getOption('format');
        $filter     = $input->getOption('filter');
        $categories = $input->getOption('categories');
        $limit      = $input->getOption('limit');

        $parser = Parser::getInstance($plugin, $stability, $filter, $categories);
        $generator = Generator::getInstance($format);
        $output->writeln(trim($generator->generate($parser, $limit, false)));
    }
}