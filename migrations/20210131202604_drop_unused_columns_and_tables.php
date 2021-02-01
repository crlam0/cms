<?php

use Phinx\Migration\AbstractMigration;

class DropUnusedColumnsAndTables extends AbstractMigration
{
    public function up()
    {
        if ($this->hasTable('blocks_list')) {
            $this->table('blocks_list')->drop()->save();
        }
        if ($this->hasTable('vote_list')) {
            $this->table('vote_list')->drop()->save();
        }
        if ($this->hasTable('vote_log')) {
            $this->table('vote_log')->drop()->save();
        }
        if ($this->hasTable('vote_variants')) {
            $this->table('vote_variants')->drop()->save();
        }

        $table = $this->table('messages');
        if ($table->hasColumn('tpl_id')) {
            $table->removeColumn('tpl_id')
                  ->save();
        }
    }
    public function down()
    {
    }
}
