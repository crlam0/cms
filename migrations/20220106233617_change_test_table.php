<?php

use Phinx\Migration\AbstractMigration;

class ChangeTestTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('test');
        $table->renameColumn('username', 'name')
              ->renameColumn('password', 'value')
              ->addColumn('comment', 'string', ['limit' => 255, 'null' => false, 'default' => ''])
              ->save();
    }
}
