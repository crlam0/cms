<?php

use Phinx\Migration\AbstractMigration;

class ChangeMediaTables extends AbstractMigration
{

    public function change()
    {
        $table = $this->table('media_list');
        $table->addColumn('date_change', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'after' => 'date_add'])
              ->addColumn('uid', 'integer', ['limit' => 11, 'default' => 0, 'after' => 'date_change'])
              ->addColumn('active', 'enum', ['values' => ['Y', 'N'], 'default' => 'Y', 'after' => 'num'])
              ->addColumn('image_name', 'string', ['limit' => 255, 'default' => ''])
              ->addColumn('image_type', 'string', ['limit' => 255, 'default' => ''])
              ->save();

        $table = $this->table('media_files');
        $table->addColumn('date_change', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'after' => 'date_add'])
              ->addColumn('uid', 'integer', ['limit' => 11, 'default' => 0])
              ->addColumn('active', 'enum', ['values' => ['Y', 'N'], 'default' => 'Y', 'after' => 'list_id'])
              ->save();
    }
}
