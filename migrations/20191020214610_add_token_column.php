<?php

use Phinx\Migration\AbstractMigration;

class AddTokenColumn extends AbstractMigration
{
    public function change(): void
    {        
        $table = $this->table('users');
        $table->addColumn('token', 'string', ['limit' => 255])
              ->save();
    }
}
