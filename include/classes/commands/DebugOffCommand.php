<?php

namespace classes\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class DebugOffCommand extends Command
{
    
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('debug:off')
            ->setDescription('Switch debug mode off')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<comment>Switch debug mode off</comment>');
        
        $query = "update settings set value='0' where name='debug'";
        if($this->db->query($query)) {
            $output->writeln('<info>Done!</info>');
        } else {
            $output->writeln('<error>ERROR!</error>');
            $output->writeln('<error>Error was: ' . $this->db->error() . '</error>');
        }        
    }
}
