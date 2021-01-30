<?php

use Phinx\Migration\AbstractMigration;

class ChangeMessagesTable extends AbstractMigration
{
    public function change(): void
    {
        $templates = $this->table('messages');
        $templates->renameColumn('title', 'name')
              ->addIndex(['name'], ['unique' => true])
              ->save();
    }
}
