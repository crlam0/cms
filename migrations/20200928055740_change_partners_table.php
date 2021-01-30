<?php

use Phinx\Migration\AbstractMigration;

class ChangePartnersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('partners');
        $table->addColumn('active', 'enum', ['values' => ['Y', 'N'], 'default' => 'Y'])
              ->save();
    }
}
