<?php

namespace classes\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;

class DBRestoreCommand extends Command
{
    private $paths = [];
    
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('db:restore')
            ->setDescription('Database restore')
            ->addArgument('last', InputArgument::OPTIONAL, 'Restore from last dump')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        global $DBHOST, $DBUSER, $DBPASSWD, $DBNAME;        
        $last = $input->getArgument('last');
        $this->paths = glob('dump-*.sql');
        if (empty($last)) {
            $helper = $this->getHelper('question');
            $question = new ChoiceQuestion('Choose file', $this->paths, 0);
            $dump_name = $helper->ask($input, $output, $question);
        } else {
            $dump_name = end($this->paths);
        }
        
        $output->writeln('<comment>Restore DB from </comment><info>'.$dump_name.'</info>');

        $command="mysql -h {$DBHOST} -u {$DBUSER} --password={$DBPASSWD} {$DBNAME} {$table} < {$dump_name}";
        $result = 0;
        system($command,$result);
        if($result!==0){
            $output->writeln('<error>ERROR!</error>');
            $output->writeln('<error>Command was: '.$command.'</error>');
            unlink($dump_name);
        } else {
            $output->writeln('<info>Done!</info>');
        }

    }
}
