<?
require_once("../shared/config.inc.php");

header("Content-Type: application/xml; charset=UTF-8");
$sitemap = new DomDocument('1.0', 'UTF-8');
// root element
$root = $sitemap->createElement("urlset");
$sitemap->appendChild($root);

$root_attr = $sitemap->createAttribute('xmlns');
$root->appendChild($root_attr);
$root_attr_text = $sitemap->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9');
$root_attr->appendChild($root_attr_text);

$root_attr = $sitemap->createAttribute('xmlns:xsi');
$root->appendChild($root_attr);
$root_attr_text = $sitemap->createTextNode('http://www.w3.org/2001/XMLSchema-instance');
$root_attr->appendChild($root_attr_text);

$root_attr = $sitemap->createAttribute('xsi:schemaLocation');
$root->appendChild($root_attr);
$root_attr_text = $sitemap->createTextNode('http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
$root_attr->appendChild($root_attr_text);

/**
 *
 * Vyberie všetky stránky zo štruktúry webu
 *
 */
function structure_pages($menu_id = NULL, $lang = NULL, $output = array()) { //vypis struktury pre stranku
    $queryString = 'SELECT
                        *, ' . $lang . '_name AS name FROM ' . TABLE_PREFIX . 'menu
                    WHERE 1 AND child_of ' . ((!is_numeric($menu_id)) ? ' IS NULL' : ' = "' . $menu_id . '"') . '
                    AND ' . $lang . '_name_seo != ""
                    ORDER BY name ASC, sorter ASC;';
    $Result = mysql_query($queryString);
    if ($Result) {
        while ($Row = mysql_fetch_assoc($Result)) {
            if ($Row['child_of'] != 0) {
                if ($Row[$lang . '_name_seo'] != '') {
                    $output[] = array(
                        'loc' => ROOTDIR . '/' . $lang . '/' . $Row[$lang . '_name_seo'],
                        'lastmod' => ($Row['_date'] == '0000-00-00 00:00:00') ? date('Y-m-d') : date('Y-m-d', strtotime($Row['_date'])),
                        'changefreq' => 'daily'
                    );
                }
            }
            $output = structure_pages($Row['menu_id'], $lang, $output);
        }
    } else {
        print(mysql_error());
    }
    mysql_free_result($Result);
    return array_filter($output);
}

/**
 *
 * Vyberie všetky stránky z noviniek
 *
 */
function news_pages($lang, $output = array()) {
    $queryString = "SELECT article_id, _date, a." . $lang . "_name_seo AS name_seo, ac." . $lang . "_name_seo AS category_name_seo
                FROM " . TABLE_PREFIX . "article AS a
                JOIN " . TABLE_PREFIX . "article_category AS ac USING (article_category_id)
                LEFT JOIN " . TABLE_PREFIX . "user AS u USING (user_id)
                WHERE 1 AND publish=1 AND a." . $lang . "_name_seo!=''
                ORDER BY sorter ASC, a._date DESC";
    $Result = mysql_query($queryString);
    if (mysql_num_rows($Result) > 0) {
        while ($Row = mysql_fetch_array($Result)) {
            if ($Row['name_seo'] != '' AND $Row['category_name_seo'] != '') {
                $output[] = array(
                    'loc' => ROOTDIR . '/' . $lang . '/' . $Row['category_name_seo'] . '/detail/' . $Row['name_seo'] . '/' . $Row['article_id'],
                    'lastmod' => ($Row['_date'] == '0000-00-00 00:00:00') ? date('Y-m-d') : date('Y-m-d', strtotime($Row['_date'])),
                    'changefreq' => 'daily'
                );
            }
        }
    }
    mysql_free_result($Result);
    return array_filter($output);
}

/**
 *
 * Vyberie všetky produkty z e-shopu
 *
 */
function product_pages($lang, $output = array()) {
    $queryString = "SELECT " . $lang . "_name_seo AS name_seo, product_id, date
                        FROM " . TABLE_PREFIX . "product
                        WHERE available = '1'
                        AND " . $lang . "_name_seo != ''
                        ORDER BY date DESC";

    $Result = mysql_query($queryString);

    while ($Row = mysql_fetch_array($Result)) {
        $seo_sql = mysql_query('SELECT ' . $lang . '_name_seo AS name_seo, menu_id
                            FROM ' . TABLE_PREFIX . 'product_menu
                            JOIN ' . TABLE_PREFIX . 'menu USING(menu_id)
                            WHERE 1 AND product_id="' . $Row['product_id'] . '"
                            ORDER BY product_menu_id ASC
                            LIMIT 2');
        $c = mysql_num_rows($seo_sql);
        while ($pws = mysql_fetch_assoc($seo_sql)) {
            // ak produkt je priradený do viac kategórií, tak koreňovú kategóriu 235 (webshop/eshop) vynechať
            if($c > 1 && $pws['menu_id'] == 235)
                continue;

            if ($Row['name_seo'] != '' AND $pws['name_seo'] != '') {
                $output[$Row['product_id']] = array(
                    'loc' => ROOTDIR . '/' . $lang . '/' . $pws['name_seo'] . '/produkt/' . $Row['name_seo'] . '/' . $Row['product_id'],
                    'lastmod' => date('Y-m-d', strtotime('-1 days')),
                    'changefreq' => 'daily',
                );
            }
        }
    }
    mysql_free_result($Result);

    foreach ($output as $key => $item) {
        if (isset($output[$item[$key]])) {
            unset($item[$key]);
        }
        $output[$item[$key]] = TRUE;
    }

    return array_filter($output);
}

$output = array();
foreach ($cLanguage->getLanguageCodes() as $lang_code) {
    if($lang_code != 'sk')
        continue;
    //$output = array_merge($output, structure_pages(NULL, $lang_code), news_pages($lang_code), product_pages($lang_code));
    $output = array_merge($output, structure_pages(1, $lang_code), product_pages($lang_code));
}

foreach ($output as $key => $value) {
    if(empty($value['loc']))
        continue;
    
    // create child element
    $url = $sitemap->createElement("url");
    $root->appendChild($url);

    $loc = $sitemap->createElement("loc");
    $lastmod = $sitemap->createElement("lastmod");
    $changefreq = $sitemap->createElement("changefreq");

    $url->appendChild($loc);
    $url_text = $sitemap->createTextNode($value['loc']);
    $loc->appendChild($url_text);

    $url->appendChild($lastmod);
    $lastmod_text = $sitemap->createTextNode($value['lastmod']);
    $lastmod->appendChild($lastmod_text);

    $url->appendChild($changefreq);
    $changefreq_text = $sitemap->createTextNode($value['changefreq']);
    $changefreq->appendChild($changefreq_text);
}
$sitemap->formatOutput = true;
echo $sitemap->saveXML();