<?

// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_fotogaleria-vypis.css" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
$js_file = ''; /* <script type="text/javascript" src="js/mod_xxc.js"></script> */
$MODULE_HEADER = $css_file . $js_file;
// definovanie potrebnych inline js action // uvadzat bez oznacenia <script>
$inline_js = ''; /* $(function() { $( "#datepicker" ).datepicker(); }); */
$MODULE_INLINE_JS = $inline_js;
// titulka (ak je prazdna, pouzije sa titulka zo struktury SQL)
$MODULE_TITLE = "";
// seo prvky daneho modulu
$MODULE_DESCRIPTION = "";
$MODULE_KEYWORDS = "";

// vykonanie akcii spojenych s odoslanim
// if(isset($_POST['send'])){
// nacitanie obsahu modulu
ob_start();

require_once('shared/classes/class.uploadimage.php');

// VYKONANIE AKCII
$name_parent = Database::getRows('SELECT ' . $lang . '_name AS name FROM ' . TABLE_PREFIX . 'menu WHERE menu_id = ' . $navigateId);
echo '<div id="fotogalerie-vypis">';
echo '<h1>' . $name_parent[0]->name . '</h1>';


$fotogalerie_vypis = Database::getRows('SELECT menu_id, ' . $lang . '_name AS name FROM ' . TABLE_PREFIX . 'menu JOIN ' . TABLE_PREFIX . 'module USING (module_id) WHERE child_of = ' . $navigateId . ' AND module = "fotogaleria-vypis" ORDER BY sorter');
foreach ($fotogalerie_vypis as $fotogaleria_vypis) {
    echo '<div id="fotogaleria">';

    $fotogalerie = Database::getRows('SELECT menu_id FROM ' . TABLE_PREFIX . 'menu JOIN ' . TABLE_PREFIX . 'module USING (module_id) WHERE child_of = ' . $fotogaleria_vypis->menu_id . ' AND module = "fotogaleria" ORDER BY sorter LIMIT 0,1');
    foreach ($fotogalerie as $fotogaleria) {
        $fotografia = Database::getRows('SELECT src FROM ' . TABLE_PREFIX . 'photo_images WHERE menu_id = ' . $fotogaleria->menu_id . ' ORDER BY sorter LIMIT 0,1');
        if (!file_exists('photos/thumbail2/' . $fotografia['src'])) {
            UploadImage::OrezavanieImage('photos/original/' . $fotografia[0]->src, 'photos/thumbail2/' . $fotografia[0]->src, 150, 150);
        }
    }
    echo '<div class="fotografia left">';
    echo '<a href="' . Menu::getHyperlinkById($fotogaleria_vypis->menu_id) . '"><img alt="' . SEO_TITLE . '" src="' . ROOTDIR . '/photos/thumbail2/' . $fotografia[0]->src . '" /></a>';
    echo '</div>';
    echo '<div class="text left"><h2><a href="' . Menu::getHyperlinkById($fotogaleria_vypis->menu_id) . '">' . $fotogaleria_vypis->name . '</a></h2></div>';
    echo '<div class="clear"></div>';
    echo '</div>';
    unset($fotografia, $fotogalerie, $fotogaleria);
}

echo '</div>';




$moduleContent = ob_get_contents();
ob_clean();
?>