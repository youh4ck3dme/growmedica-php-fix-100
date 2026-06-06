<?php
require_once('../shared/config.inc.php');
ini_set('display_errors', '1');

$catalogue = new Catalogue;
//$catalogue->set_catalogue_limit(100);
$catalogue->set_catalogue_order('name ASC');
$products = $catalogue->get_catalogue();
/*
echo '<pre>';
var_dump($products);
echo '</pre>';
*/
$xmlns = [
  'atom' => 'http://www.w3.org/2005/Atom',
  'g' =>'http://base.google.com/ns/1.0'
];

$doc = new DOMDocument('1.0', 'UTF-8');

$xmlRoot = $doc->createElement('rss');
$xmlRoot = $doc->appendChild($xmlRoot);
$xmlRoot->setAttribute('version', '2.0');
$xmlRoot->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:g', "http://base.google.com/ns/1.0");

$channelNode = $xmlRoot->appendChild($doc->createElement('channel'));
$channelNode->appendChild($doc->createElement('title', htmlspecialchars(PROJECT_TITLE)));
$channelNode->appendChild($doc->createElement('link', htmlspecialchars(ROOTDIR)));


foreach ($products as $product) {
    //if($product->product_id != 68)
    if($product->available != 1)
        continue;

    $itemNode = $channelNode->appendChild($doc->createElement('item'));
    $itemNode->appendChild($doc->createElementNS($xmlns['g'], 'g:id', $product->product_id));
    $itemNode->appendChild($doc->createElementNS($xmlns['g'], 'g:title', htmlspecialchars($product->name)));
    //$itemNode->appendChild($doc->createElementNS($xmlns['g'], 'g:description', $product->description));
    
    // ADD product url
    $query = 'SELECT menu_id, m.sk_name_seo AS url FROM ' . TABLE_PREFIX . 'product_menu AS pm
              LEFT JOIN ' . TABLE_PREFIX . 'menu AS m USING(menu_id)
              WHERE 1 AND pm.product_id = "' . $product->product_id . '"';
    $result = mysql_query($query);
    $row = mysql_fetch_object($result);

    $itemNode->appendChild($doc->createElementNS($xmlns['g'], 'g:link', ROOTDIR . '/sk/' . $row->url . '/produkt/' . String::SEOFriendlyText($product->name) . '/' . $product->product_id));
    
    if (!empty($product->image_src) && file_exists('../photos/original/' . $product->image_src)) {
        $itemNode->appendChild($doc->createElementNS($xmlns['g'], 'g:image_link', ROOTDIR . '/photos/original/' . $product->image_src));
    }

    $itemNode->appendChild($doc->createElementNS($xmlns['g'], 'g:availability', $product->delivery_time == 1 ? 'in_stock' : 'preorder'));
    if($product->delivery_time != 1) {
        $itemNode->appendChild($doc->createElementNS($xmlns['g'], 'g:availability_date', date('Y-m-d', strtotime('+3 weeks'))));
    }
    $itemNode->appendChild($doc->createElementNS($xmlns['g'], 'g:price', number_format($product->price, 2, '.', '') . ' EUR'));
}



$doc->formatOutput = true;
header("Content-Type: application/xml; charset=UTF-8");
echo $doc->saveXML();

exit;