<?php

namespace Classes;

/**
 * Sitemap generator.
 *
 * @property string $static_pages Data for static pages.
 * @property string $pages array of pages to generate
 *
 */

class Sitemap {
    private $static_pages = array(
        array(
            'url' => '',
            'changefreq' => 'weekly',
            'priority' => '1.00',
        ),
        array(
            'url' => 'sitemap.xml',
            'changefreq' => 'monthly',
            'priority' => '0.90',
        ),
    );
    public $pages;
    
    public function __construct(){
        if(isset(App::$settings['sitemap_static_pages']) && is_array(App::$settings['sitemap_static_pages'])){
            $this->static_pages = array_merge($this->static_pages, App::$settings['sitemap_static_pages']);
        }
        $this->pages=$this->static_pages;
    }
    
    /**
     * Add page to $pages
     *
     * @param string $url URL of the page
     * @param string $changefreq Frequency of changes
     * @param string $priority Priority to crawl
     *
     * @return string Output string
     */
    public function add_page($url,$changefreq,$priority){
        $this->pages[]=array(
            'url' => $url,
            'changefreq' => $changefreq,
            'priority' => $priority,
        );
    }

    /**
     * Add URL list from database to $pages 
     *
     * @param array $types Types of content
     *
     * @return string Output string
     */
    public function build_pages_array($types){
        if(in_array('article', $types)){
            $this->add_page('article/', 'monthly', '0.50');
            $query = 'SELECT * from article_list order by date_add asc';
            $result = App::$db->query($query);
            while ($row = $result->fetch_array()) {
                $this->add_page(get_article_list_href($row['id']), 'monthly', '0.80');
            }
            $query = 'SELECT * from article_item order by date_add asc';
            $result = App::$db->query($query);
            while ($row = $result->fetch_array()) {
                $this->add_page(get_article_href($row['id']), 'monthly', '0.80');
            }
        }    
        if(in_array('blog', $types)){
            $this->add_page('blog/', 'monthly', '0.50');
            $query = "SELECT * from blog_posts where active='Y' order by date_add asc";
            $result = App::$db->query($query);
            while ($row = $result->fetch_array()) {
                $this->add_page(get_post_href(NULL,$row), 'monthly', '0.80');
            }    
        }
        if(in_array('gallery', $types)){
            $this->add_page('gallery/', 'monthly', '0.50');
            $query = 'SELECT * from gallery_list order by title asc';
            $result = App::$db->query($query);
            while ($row = $result->fetch_array()) {
                $this->add_page(get_gallery_list_href($row['id']), 'monthly', '0.80');
            }
        }
        if(in_array('catalog', $types)){
            $this->add_page('catalog/', 'monthly', '0.80');
            $query = 'SELECT id from cat_part order by num,title asc';
            $result = App::$db->query($query);
            while ($row = $result->fetch_array()) {
                $this->add_page(get_cat_part_href($row['id']), 'monthly', '0.40');
            }
            $query = 'SELECT part_id,seo_alias from cat_item order by num,title asc';
            $result = App::$db->query($query);
            while ($row = $result->fetch_array()) {
                $url = get_cat_part_href($row['part_id']) . $row['seo_alias'];
                $this->add_page($url, 'monthly', '0.30');
            }
        }
    }
    
    /**
     * Write Sitemap.xml basing on $pages
     *
     * @return array ['output','count']
     */
    public function write($test_only = false){        
        $ServerUrl = App::$server['REQUEST_SCHEME'] . '://' . App::$server['HTTP_HOST'] . App::$SUBDIR;
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->formatOutput = true;

        $SITEMAP_NS = 'http://www.sitemaps.org/schemas/sitemap/0.9';
        $SITEMAP_NS_XSD = 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd';

        // ...and urlset (root) element
        $urlSet = $dom->createElementNS($SITEMAP_NS, 'urlset');
        $dom->appendChild($urlSet);
        $urlSet->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
        $urlSet->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', $SITEMAP_NS . " " . $SITEMAP_NS_XSD);

        $output='';
        foreach ($this->pages as $page) {

            $url = $ServerUrl . $page['url'];

            $output.= "Add {$url} <br>";

            // create url node for this page
            $urlNode = $dom->createElementNS($SITEMAP_NS, 'url');
            $urlSet->appendChild($urlNode);

            // put url in "loc" element
            $urlNode->appendChild($dom->createElementNS(
                            $SITEMAP_NS, "loc", $url));
            $urlNode->appendChild(
                    $dom->createElementNS(
                            $SITEMAP_NS, 'changefreq', $page['changefreq'])
            );

            $urlNode->appendChild(
                    $dom->createElementNS(
                            $SITEMAP_NS, 'priority', $page['priority'])
            );
        }

        $xml = $dom->saveXML();
        if(!$test_only) {
            file_put_contents(App::$DIR . 'sitemap.xml', $xml);
        }    
        return array('output'=>$output,'count'=>count($this->pages));
    }
}
