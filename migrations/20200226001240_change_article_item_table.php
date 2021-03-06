<?php

use Phinx\Migration\AbstractMigration;

class ChangeArticleItemTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('article_item');
        $table->addColumn('date_change', 'datetime')
              ->save();
    }
}
