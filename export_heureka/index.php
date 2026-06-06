<?php



require_once('../shared/config.inc.php');

$catalogue = new Catalogue;

$catalogue->set_catalogue_limit(5000);

$catalogue->set_catalogue_order('name ASC');

$products = $catalogue->get_catalogue();

$heureka_cpc = '0.24';

//Creates XML string and XML document using the DOM

$dom = new DomDocument('1.0', 'UTF-8');

//add root / doob

$output = $dom->appendChild($dom->createElement('SHOP'));

//

// delivery
/*
$delivery_array = array();

$result = mysql_query('SELECT name, price_eur FROM ' . TABLE_PREFIX . 'delivery_type WHERE delivery_type_id IN (2);');

while ($row = mysql_fetch_object($result)) {

    $delivery_array[] = array(

        'name' => $row->name,

        'price' => $row->price_eur

    );

}
*/
$delivery_array = [];
$sql = 'SELECT d.name, h.hdt_name, d.price_eur FROM ' . TABLE_PREFIX . 'delivery_type AS d
        LEFT JOIN ' . TABLE_PREFIX . 'heureka_delivery_type AS h 
        ON(h.hdt_id = d.heureka_delivery_type_id) 
        WHERE 1  AND d.min_price = 0 GROUP BY h.hdt_name';
$result = mysql_query($sql);

while ($row = mysql_fetch_object($result)) {
    $delivery_array[] = array(
        'DELIVERY_ID' => $row->hdt_name,
        'DELIVERY_PRICE' => $row->price_eur,
        'DELIVERY_PRICE_COD' => 3 //$row->price_cod
    );

}


// delivery END

/*

  if ($_GET['y'] == 'wrert') {

  $result = mysql_query('SELECT name, price_eur FROM ' . TABLE_PREFIX . 'delivery_type WHERE delivery_type_id IN (2);');

  while ($row = mysql_fetch_object($result)) {

  $delivery_array[] = array(

  'name' => $row->name,

  'price' => $row->price_eur

  );

  }

  }

 */

$i = 0;

foreach ((array) $products as $key => $value) {

    $i++;

    //add category

    $product = $dom->createElement('SHOPITEM');

    $output->appendChild($product);

    // ADD SHOP ID

    $attr = $dom->createElement('ITEM_ID', $value->product_id);

    $product->appendChild($attr);

    // add product_name

    /*$product_name = $dom->createElement("PRODUCTNAME");
    $product->appendChild($product_name);
    $cdata = $dom->createCDATASection(trim($value->name));
    $product_name->appendChild($cdata);*/

    $attr = $dom->createElement('PRODUCTNAME', htmlspecialchars(trim($value->name)));
    $product->appendChild($attr);



    // add product
    /*
        $product_ = $dom->createElement("PRODUCT");

        $product->appendChild($product_);

        $cdata = $dom->createCDATASection(trim($value->name) . (!empty($value->keywords) ? ' (' . trim($value->keywords) . ')' : ''));

        $product_->appendChild($cdata);
    */
    // add description

    /*$description = $dom->createElement("DESCRIPTION");
    $product->appendChild($description);
    //$cdata = $dom->createCDATASection(preg_replace('#(<[a-z ]*)(style=("|\')(.*?)("|\'))([a-z ]*>)#', '\\1\\6', strip_tags($value->description, '<p>')));
    $cdata = $dom->createCDATASection(preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/si",'<$1$2>', strip_tags($value->description, '<p>')));
    $description->appendChild($cdata);*/

    $attr = $dom->createElement('DESCRIPTION', htmlspecialchars(trim(preg_replace('/\s+/', ' ', strip_tags($value->description)))));
    $product->appendChild($attr);

    // ADD product url

    $query = 'SELECT menu_id, m.sk_name_seo AS url, m.heureka_category_name FROM ' . TABLE_PREFIX . 'product_menu AS pm

              LEFT JOIN ' . TABLE_PREFIX . 'menu AS m USING(menu_id)

              WHERE 1 AND pm.product_id="' . $value->product_id . '"';

    $result = mysql_query($query);

    $row = mysql_fetch_object($result);
    $heureka_category = $row->heureka_category_name;

    $attr = $dom->createElement('URL', ROOTDIR . '/sk/' . $row->url . '/produkt/' . String::SEOFriendlyText($value->name) . '/' . $value->product_id);

    $product->appendChild($attr);



    if (file_exists('../photos/original/' . $value->image_src)) {

        $attr = $dom->createElement('IMGURL', ROOTDIR . '/photos/original/' . $value->image_src);

        $product->appendChild($attr);

    }

    //

    //$attr = $dom->createElement('PRICE', number_format(($value->price * ((100 - $value->percentage_discount) / 100)), 2, '.', ' '));
    /*
    $attr = $dom->createElement('PRICE', number_format($value->price, 2, '.', ' '));

    $product->appendChild($attr);

    //

    $attr = $dom->createElement('PRICE_VAT', number_format(($value->price * EUR_VAT_COEFFICIENT), 2, '.', ' '));

    $product->appendChild($attr);
    */
    $attr = $dom->createElement('PRICE_VAT', number_format($value->price, 2, '.', ' '));

    $product->appendChild($attr);


    // Heureka cpc

    //$attr = $dom->createElement('HEUREKA_CPC', $heureka_cpc);

    //$product->appendChild($attr);

    // ADD manufacturer

    $namufacturer = Product::get_manufacturer($value->manufacturer_id);

    $attr = $dom->createElement('MANUFACTURER', $namufacturer{0}->name);

    $product->appendChild($attr);

    //

    //$attr = $dom->createElement('CATEGORYTEXT', Menu::categoryTitleById($value->product_id)); //$row->url)
    $attr = $dom->createElement('CATEGORYTEXT', $heureka_category);

    $product->appendChild($attr);

    // EAN
    if(!empty(trim($value->code_ean))) {
        $attr = $dom->createElement('EAN', trim($value->code_ean));
        $product->appendChild($attr);
    }

    // DELIVERY_DATE

    /*
	  if($value->skladom=='1'){

        $attr = $dom->createElement('DELIVERY_DATE', '0');

    }else{

        $attr = $dom->createElement('DELIVERY_DATE', '3');

    }*/
    $attr = $dom->createElement('DELIVERY_DATE', '3');
    $product->appendChild($attr);

    //

    //

    // Spôsoby doručenia

    foreach ($delivery_array as $key => $value) {
        $delivery = $dom->createElement('DELIVERY');
        $product->appendChild($delivery);
        $attr = $dom->createElement('DELIVERY_ID', $value['DELIVERY_ID']);
        $delivery->appendChild($attr);
        $attr = $dom->createElement('DELIVERY_PRICE', str_replace('.', ',', $value['DELIVERY_PRICE']));
        $delivery->appendChild($attr);
        $attr = $dom->createElement('DELIVERY_PRICE_COD', str_replace('.', ',', ($value['DELIVERY_PRICE'] + $value['DELIVERY_PRICE_COD'])));
        $delivery->appendChild($attr);
    }



    // ass category_id

    /*

      $parameters = $dom->createElement('PARAM');

      $product->appendChild($parameters);

      //foreach ($cats->getProduct_categories($value['id']) as $key => $category) {

      // parameter

      $attr = $dom->createElement('PARAM_NAME', 'Farba');

      $parameters->appendChild($attr);

      // value

      //

      $attr = $dom->createElement('VAL', 'Čierna');

      $parameters->appendChild($attr);

      //

     */

}



$dom->formatOutput = true; // set the formatOutput attribute of domDocument to true

//

//

//$filename = 'export/export-heureka.xml';

$dom->save('export/export-heureka.xml');

//

//

// save XML as string or file

header("Content-Type: application/xml; charset=UTF-8");

header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT', true, 200);

//header('Content-disposition: attachment; filename="heureka-' . date('Y-m-d-H-s-i') . '.xml"');

echo $dom->saveXML(); // put string