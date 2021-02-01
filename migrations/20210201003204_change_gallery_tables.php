<?php

use Phinx\Migration\AbstractMigration;

class ChangeGalleryTables extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('gallery_list');
        $table->addColumn('date_change', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'after' => 'date_add'])
              ->addColumn('uid', 'integer', ['limit' => 11, 'default' => 0, 'after' => 'date_change'])
              ->save();

        $table = $this->table('gallery_images');
        $table->addColumn('date_change', 'datetime', ['default' => 'CURRENT_TIMESTAMP', 'after' => 'date_add'])
              ->addColumn('uid', 'integer', ['limit' => 11, 'default' => 0, 'after' => 'date_change'])
              ->save();
    }
}
