<?
require_once("../shared/config.inc.php");

$product_stock = getNumberOfProductInStock($_POST['product_id']);

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && crossDomainPosted()) { // check ajax
/*    if (!is_numeric($_POST['product_id']) OR ! is_numeric($_POST['size_id'])) {

        $result = array('empty-values', '0', $cTranslator->getTranslation('Chyba: Neboli zadané všetky potrebné údaje', 0));
    } else {
        $result = array('count', Product::availableProductsCount($_POST['product_id'], $_POST['size_id']), $cTranslator->getTranslation('Požiadavka prebehla bez prozlémov', 0), $html);
    }
} else {
    $result = array('empty-values', '0', $cTranslator->getTranslation('Nesprávna požiadavka', 0));
*/
    $result = array('ok', $product_stock);
}
echo json_encode($result);

?>