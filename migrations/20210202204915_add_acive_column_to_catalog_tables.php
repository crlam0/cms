<?php

use Phinx\Migration\AbstractMigration;

class AddAciveColumnToCatalogTables extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('cat_part');
        $table->addColumn('active', 'enum', ['values' => ['Y', 'N'], 'default' => 'Y', 'after' => 'num'])
              ->addColumn('uid', 'integer', ['limit' => 11, 'default' => 0])
              ->save();

        $table = $this->table('cat_item');
        $table->addColumn('active', 'enum', ['values' => ['Y', 'N'], 'default' => 'Y', 'after' => 'num'])
              ->addColumn('uid', 'integer', ['limit' => 11, 'default' => 0])
              ->save();
    }
}
