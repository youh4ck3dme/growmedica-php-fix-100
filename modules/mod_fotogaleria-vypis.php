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

echo '<div id="content" class="gallery">';

// VYKONANIE AKCII
echo '<h1>' . Menu::getHyperLinkTextById($navigateId) . '</h1>';

$name_parent = Database::getRows('SELECT ' . $lang . '_name AS name FROM ' . TABLE_PREFIX . 'menu WHERE menu_id = ' . $navigateId);

$string = "SELECT * FROM " . TABLE_PREFIX . "menu as me left join " . TABLE_PREFIX . "module as mo on (me.module_id = mo.module_id) where mo.module = 'fotogaleria' and child_of = " . $navigateId . " ORDER BY sorter";
if ($result = mysql_query($string)) {
    echo '<div class="gallery-content">';
    echo html_entity_decode($Row['content'], ENT_QUOTES, "UTF-8");
    if ($user->isAdmin()) {
        echo '<div class="clear"></div>';
        echo '<div class="edit-link"><img src="images/icons/edit.gif" alt="edit" /> <a href="javascript:;" onclick="javascript:openPopUp1(' . $Row["menu_id"] . ');">Upraviť</a></div>';
        echo '<div class="clear"></div>';
    }
    echo '</div>';
    while ($fotogaleria = mysql_fetch_assoc($result)) {

        $queryString_fotogaleria = "select * from " . TABLE_PREFIX . "photo_images where menu_id = '" . $fotogaleria['menu_id'] . "' order by sorter asc limit 1";
        $result_fotogaleria = mysql_query($queryString_fotogaleria . $limit . ";");
        if ($result_fotogaleria && mysql_num_rows($result_fotogaleria) > 0) {
            echo '<div class="gallery-item">';
            if ($fotogaleria[$_SESSION["lang"] . "_description"])
                echo $fotogaleria[$_SESSION["lang"] . "_description"] . '<br /><br />';
            while ($fotografia = mysql_fetch_assoc($result_fotogaleria)) {
                echo '<div class="photo-item left">';
                echo '<a href="' . Menu::getHyperlinkById($fotogaleria['menu_id']) . '"><img src="' . ROOTDIR . '/photos/thumbnail/' . $fotografia["src"] . '" alt="' . SEO_TITLE . '" /></a>';
                echo '</div>';
                echo '<div class="text left">';
                echo '<h2><a href="' . Menu::getHyperlinkById($fotogaleria['menu_id']) . '">' . $fotogaleria[$_SESSION["lang"] . "_name"] . '</a></h2>';
                echo truncate($fotogaleria[$_SESSION["lang"] . "_content"], 200, ' ');
                echo '</div>';
            }
            echo '<div class="clear"></div>';

            echo '</div>';
        }
    }
    echo '<div class="clear"></div>';
}
echo '</div>';

$moduleContent = ob_get_contents();
ob_clean();
?>