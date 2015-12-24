<?php

$tags[Header] = 'Генератор sitemap.xml';
include '../include/common.php';

//адрес вашего сайта
$ServerUrl = 'http://' . $_SERVER["HTTP_HOST"] . $SUBDIR;
// создаем новый xml документ
$dom = new DOMDocument('1.0', 'UTF-8');
$dom->formatOutput = true;
// массив страницы для sitemap
$pages = array(
    array(
        'url' => "",
        'changefreq' => "daily",
        'priority' => '1.00',
    ),
    array(
        'url' => "sitemap.xml",
        'changefreq' => "weekly",
        'priority' => '0.90',
    ),
    array(
        'url' => "faq/",
        'changefreq' => 'monthly',
        'priority' => '0.50',
    ),
    array(
        'url' => "gallery/",
        'changefreq' => 'monthly',
        'priority' => '0.50',
    ),
    array(
        'url' => "article/",
        'changefreq' => 'monthly',
        'priority' => '0.50',
    )
);

$query = 'SELECT * from article_list order by date_add asc';
$result = my_query($query, $conn, true);
while ($row = $result->fetch_array()) {
    $pages[] = array(
        'url' => get_article_list_href($row["id"]),
        'changefreq' => 'monthly',
        'priority' => '0.80',
    );
}
$query = 'SELECT * from article_item order by date_add asc';
$result = my_query($query, $conn, true);
while ($row = $result->fetch_array()) {
    $pages[] = array(
        'url' => get_article_href($row["id"]),
        'changefreq' => 'monthly',
        'priority' => '0.80',
    );
}
$query = "SELECT * from blog_posts where active='Y' order by date_add asc";
$result = my_query($query, $conn, true);
while ($row = $result->fetch_array()) {
    $url = 'blog/' . ( strlen($row["seo_alias"]) ? $row["seo_alias"] . '/' : '?view_post=' . strlen($row['id']) );
    $pages[] = array(
        'url' => $url,
        'changefreq' => 'monthly',
        'priority' => '0.80',
    );
}
$query = 'SELECT * from gallery_list order by title asc';
$result = my_query($query, $conn, true);
while ($row = $result->fetch_array()) {
    $pages[] = array(
        'url' => get_gallery_list_href($row["id"]),
        'changefreq' => 'monthly',
        'priority' => '0.80',
    );
}
$query = 'SELECT * from cat_part order by num,title asc';
$result = my_query($query, $conn, true);
while ($row = $result->fetch_array()) {
    $url = get_cat_part_href($row['id']);
    $pages[] = array(
        'url' => $url,
        'changefreq' => 'monthly',
        'priority' => '0.80',
    );
}
$query = 'SELECT * from cat_item order by num,title asc';
$result = my_query($query, $conn, true);
while ($row = $result->fetch_array()) {
    $url = get_cat_part_href($row['part_id']) . $row['seo_alias'];
    $pages[] = array(
        'url' => $url,
        'changefreq' => 'monthly',
        'priority' => '0.80',
    );
}

//    var_dump($pages);

$SITEMAP_NS = 'http://www.sitemaps.org/schemas/sitemap/0.9';
$SITEMAP_NS_XSD = 'http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd';

// ...and urlset (root) element
$urlSet = $dom->createElementNS($SITEMAP_NS, 'urlset');
$dom->appendChild($urlSet);
$urlSet->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
$urlSet->setAttributeNS('http://www.w3.org/2001/XMLSchema-instance', 'schemaLocation', $SITEMAP_NS . " " . $SITEMAP_NS_XSD);

foreach ($pages as $page) {

    $url = $ServerUrl . $page['url'];

    $content.= "Add $url <br>";

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
//сохраняем файл sitemap.xml на диск
file_put_contents($_SERVER['DOCUMENT_ROOT'] . $SUBDIR . 'sitemap.xml', $xml);
$content.= my_msg_to_str('', '', 'Готово');

echo get_tpl_by_title($part[tpl_name], $tags, '', $content);
?>

