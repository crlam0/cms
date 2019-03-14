<?php


use Phinx\Migration\AbstractMigration;

class AddReviewsTable extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    addCustomColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Any other destructive changes will result in an error when trying to
     * rollback the migration.
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     * 
     * `date` DATETIME NULL DEFAULT NULL,
	`author` VARCHAR(255) NULL DEFAULT NULL,
	`title` VARCHAR(255) NULL DEFAULT NULL,
	`content` TEXT NULL,
	`url` VARCHAR(50) NULL DEFAULT NULL,
	`file_name` VARCHAR(50) NULL DEFAULT NULL,
	`file_type` VARCHAR(50) NULL DEFAULT NULL,
	`seo_alias` VARCHAR(255) NULL DEFAULT NULL,
	`css_class` VARCHAR(50) NULL DEFAULT NULL,
     * 
     */
    public function change()
    {
        $test = $this->table('reviews');
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
