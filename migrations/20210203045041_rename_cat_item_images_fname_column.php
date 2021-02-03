<?php

use Phinx\Migration\AbstractMigration;

class RenameCatItemImagesFnameColumn extends AbstractMigration
{

    public function change()
    {
        $table = $this->table('cat_item_images');
        $table->renameColumn('fname', 'file_name')
              ->save();

    }
}
