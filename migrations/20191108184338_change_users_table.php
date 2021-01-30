<?php

use Phinx\Migration\AbstractMigration;

class ChangeUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users');
        $table->addColumn('token_expire', 'integer', ['limit' => 11])
              ->addColumn('avatar', 'string', ['limit' => 64])
              ->save();
    }
}
