<?php

namespace classes\commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DBDumpCommand extends Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('db:dump')
            ->setDescription('Database dump')
            ->addArgument('table', InputArgument::OPTIONAL, 'Table name')
        ;
    }

    /**
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        global $DBHOST, $DBUSER, $DBPASSWD, $DBNAME;
        $table = $input->getArgument('table');
        if (strlen($table)) {
            $output->writeln('<comment>Only table(s) </comment><info>'.$table.'</info>');
            if(strstr($table, ',')) {
                $table_add = str_replace(',', '_', $table);
                $table = str_replace(',', ' ', $table);
            }
        }
        $dump_name = 'dump-' . date('Y-m-d_His') . (strlen($table) ? '_' . $table_add : '') . '.sql';
        $output->writeln('<comment>Dumping db into </comment><info>'.$dump_name.'</info>');

        $command="mysqldump -h {$DBHOST} -u {$DBUSER} --password={$DBPASSWD} {$DBNAME} {$table} > {$dump_name}";
        $result = 0;
        system($command, $result);
        if ($result!==0) {
            $output->writeln('<error>ERROR!</error>');
            $output->writeln('<error>Command was: '.$command.'</error>');
            unlink($dump_name);
        } else {
            $output->writeln('<info>Done!</info>');
        }
        return 0;
    }
}
