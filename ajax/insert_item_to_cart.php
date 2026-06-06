<?

require_once("../shared/config.inc.php");

		$obj_product = new Product;
        $obj_product->set_dph_price_visibility(VAT_VISIBILITY);
        $product = $obj_product->get_product();

        // PRED KAZDYM POUZITIM TREBA NAJPRV OBJEKT UNSERIALIZOVAT ZO SESSION
        // A PO ZMENE OBSAHU OPAT SERIALIZOVAT
        // KOSIK SI VEZME Z NASTAVENIA PRODUKTOV NASTAVENIE O ZOBRAZOVANI DPH
        $obj_cart = unserialize($_SESSION['serialized_cart']);


		$obj_cart->insert_item($_POST['product_id'], $_POST['color'], $_POST['size'], $_POST['amount'], $_POST['price_item']);
        //$_SESSION['product'] = 'added';

        


        $_SESSION['serialized_cart'] = serialize($obj_cart);

        $cart_value = number_format($obj_cart->get_cart_value(), 2, ',', ' ');
        $cart_count = $obj_cart->get_cart_count();
        $cart_quantity = $obj_cart->get_cart_quantity();
        $product_stock = getNumberOfProductInStock($_POST['product_id']);
        $result = array('added', $cart_value, $cart_quantity, $cTranslator->getTranslation('Objednávka bola pridaná do košíka', 0), $product_stock);

        
        $obj_cart = unserialize($_SESSION['serialized_cart']);

        



if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && crossDomainPosted()) { // check ajax
/*    if (!is_numeric($_POST['product_id']) OR ! is_numeric($_POST['size_id'])) {


        


        $result = array('empty-values', '0', $cTranslator->getTranslation('Chyba: Neboli zadané všetky potrebné údaje', 0));
    } else {
        $result = array('count', Product::availableProductsCount($_POST['product_id'], $_POST['size_id']), $cTranslator->getTranslation('Požiadavka prebehla bez prozlémov', 0), $html);
    }
} else {
    $result = array('empty-values', '0', $cTranslator->getTranslation('Nesprávna požiadavka', 0));
*/
}
echo json_encode($result);

?>