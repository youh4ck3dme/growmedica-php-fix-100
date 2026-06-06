<?

require_once("../shared/config.inc.php");

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest' && crossDomainPosted()) { // check ajax
    if (!is_numeric($_POST['product_id']) OR ! is_numeric($_POST['color_id'])) {
        $result = array('empty-values', '0', $cTranslator->getTranslation('Chyba: Neboli zadané všetky potrebné údaje', 0));
    } else {
        $sizes = Product::get_product_sizes($_POST['product_id'], $_POST['color_id']);
        if (!empty($sizes) AND $sizes{0}->univerzal != 1) {
            $html = '';
            foreach ($sizes as $size) {
                $html .= '<option value="' . $size->product_type_id . '">' . $size->name . '</option>';
            }
            $result = array('options', $sizes{0}->amount, $cTranslator->getTranslation('Požiadavka prebehla bez prozlémov', 0), $html);
        } else {
            $html = '<input type="hidden" name="size" value="0" />';
            $result = array('universal', $sizes{0}->amount, $cTranslator->getTranslation('Požiadavka prebehla bez prozlémov', 0), $html);
        }
    }
} else {
    $result = array('empty-values', '0', $cTranslator->getTranslation('Nesprávna požiadavka', 0));
}
echo json_encode($result);
?>