<?php
namespace Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrationCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'migration:migrate';

    protected function configure()
    {

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->writeln('success');
        return Command::SUCCESS;
    }
}