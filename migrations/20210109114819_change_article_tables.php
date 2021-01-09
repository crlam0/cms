<?php

use Phinx\Migration\AbstractMigration;

class ChangeArticleTables extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('article_list');
        $table->addColumn('date_change', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('uid', 'integer', ['limit' => 11, 'default' => 0])
              ->addColumn('image_name', 'string', ['limit' => 255, 'default' => ''])
              ->addColumn('image_type', 'string', ['limit' => 255, 'default' => ''])
              ->save();

        $table = $this->table('article_item');
        $table->addColumn('active', 'enum', ['values' => ['Y', 'N'], 'default' => 'Y'])
              ->addColumn('uid', 'integer', ['limit' => 11, 'default' => 0])
              ->addColumn('image_name', 'string', ['limit' => 255, 'default' => ''])
              ->addColumn('image_type', 'string', ['limit' => 255, 'default' => ''])
              ->save();
    }
}
