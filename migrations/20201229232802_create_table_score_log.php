<?php

use Phinx\Migration\AbstractMigration;

class CreateTableScoreLog extends AbstractMigration
{

    public function change(): void
    {
        $table = $this->table('score_log', ['engine' => 'MyISAM']);
        $table
              ->addColumn('date', 'datetime')
              ->addColumn('target_type', 'string', ['limit' => 16])
              ->addColumn('target_id', 'integer', ['limit' => 11])
              ->addColumn('uid', 'integer', ['limit' => 11])
              ->addColumn('score', 'integer', ['limit' => 2])
              ->addColumn('ip', 'string', ['limit' => 32])
              ->addIndex(['target_type'])
              ->addIndex(['target_id'])
              ->addIndex(['uid'])
              ->create();
    }
}
