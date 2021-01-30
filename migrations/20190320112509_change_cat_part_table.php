<?php


use Phinx\Migration\AbstractMigration;

class ChangeCatPartTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('cat_part');
        $table->addColumn('related_products', 'text')
              ->save();
    }
}
