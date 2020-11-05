<?php

namespace classes\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DBDumpClearCommand extends Command
{
    private $paths = [];
    
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('db:dump-clear')
            ->setDescription('Remove database dumps')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->paths = glob('dump-*.sql');
        
        $output->writeln('<comment>Remove database dumps</comment>');
        foreach($this->paths as $dump_name){
            if(unlink($dump_name)) {
                $output->writeln('<info>Delete: ' . $dump_name . '</info>');
            } else {
                $output->writeln('<error>Cant delete: ' . $dump_name . '</error>');
            }
            
        }
    }
}
