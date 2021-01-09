<?php

use Phinx\Migration\AbstractMigration;

class ChangeMenuItemTable extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('menu_item');
        $table->addColumn('image_name', 'string', ['limit' => 255, 'default' => ''])
              ->addColumn('image_type', 'string', ['limit' => 255, 'default' => ''])
              ->save();
    }
}
