<?php


use Phinx\Migration\AbstractMigration;

class AddReviewsTable extends AbstractMigration
{

    function change()
    {
        $test = $this->table('reviews', ['engine' => 'MyISAM']);
        $test
              ->addColumn('date', 'datetime')
              ->addColumn('author', 'string', ['limit' => 255])
              ->addColumn('content', 'text')
              ->addColumn('file_name', 'string', ['limit' => 50])
              ->addColumn('file_type', 'string', ['limit' => 50])
              ->addIndex(['date'])
              ->create();
    }
}
