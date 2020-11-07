<?php

use Phinx\Migration\AbstractMigration;

class CreateTableBlogTags extends AbstractMigration
{
    public function change()
    {
        $blog_tags = $this->table('blog_tags');
        $blog_tags
              ->addColumn('name', 'string', ['limit' => 64])
              ->addColumn('seo_alias', 'string', ['limit' => 64])
              ->create();
        
        $blog_posts_tags = $this->table('blog_posts_tags');
        $blog_posts_tags
              ->addColumn('post_id', 'integer', ['limit' => 11])
              ->addColumn('tag_id', 'integer', ['limit' => 11])
              ->create();

        $blog_posts = $this->table('blog_posts');
        $blog_posts->addColumn('image_type', 'string', ['limit' => 255])
              ->save();
    }
}
