<?php

use Phinx\Migration\AbstractMigration;

class ChangeSettingsTemplatesColumns extends AbstractMigration
{
    public function change(): void
    {
        $settings = $this->table('settings');
        $settings->renameColumn('title', 'name')
              ->addIndex(['name'], ['unique' => true])
              ->save();

        $templates = $this->table('templates');
        $templates->renameColumn('title', 'name')
              ->addIndex(['name'], ['unique' => true])
              ->save();
    }
}
