<?

// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_fotogaleria.css" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
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

// VYKONANIE AKCII
switch ($navigateArrayUrlWithoutBase[0]) {
    case "insert":
        break;
    default:
        echo '<h1>' . Menu::getHyperLinkTextById($navigateId) . '</h1>';
        echo '<div class="gallery-content">';
        echo html_entity_decode($Row['content'], ENT_QUOTES, "UTF-8");
        if ($user->isAdmin()) {
            echo '<div class="clear"></div>';
            echo '<div class="edit-link"><img src="images/icons/edit.gif" alt="edit" /> <a href="javascript:;" onclick="javascript:openPopUp1(' . $Row["menu_id"] . ');">Upraviť</a></div>';
            echo '<div class="clear"></div>';
        }
        echo '</div>';
        $queryString_fotogaleria = "select * from " . TABLE_PREFIX . "photo_images where 1 and menu_id = '" . $Row['menu_id'] . "' order by sorter asc";
        $result_fotogaleria = mysql_query($queryString_fotogaleria . $limit . ";");
        if ($result_fotogaleria) {
            echo '<div id="photo-gallery">';
            while ($fotografia = mysql_fetch_assoc($result_fotogaleria)) {
                echo '<div class="photo">';
                echo '<div class="img">';
                //echo '<a href="' . ROOTDIR . '/photos/original/' . $fotografia["src"] . '" rel="lytebox[' . String::SEOFriendlyText(Menu::getHyperLinkTextById($navigateId)) . ']" title="' . $fotografia["name"] . '"><div style="background-image:url(' . ROOTDIR . '/photos/original/' . $fotografia["src"] . ');"></div></a>';
                echo '<a class="gallery-item" href="' . ROOTDIR . '/photos/original/' . $fotografia["src"] . '" rel="' . String::SEOFriendlyText(Menu::getHyperLinkTextById($navigateId)) . '" title="' . (!empty($fotografia["name"]) ? $fotografia["name"] : Menu::getHyperLinkTextById($navigateId)) . '"><img src="' . ROOTDIR . '/photos/thumbnail/' . $fotografia["src"] . '" alt="' . SEO_TITLE . '" /></a>';
                echo '</div>' . $fotografia["description"] . '</div>';
            }
            echo '<div class="clear"></div>';
            echo '<a href="' . ROOTDIR . '/' . Menu::getHyperlinkById($Row['child_of']) . '">' . $cTranslator->getTranslation('Späť na zoznam galérii', 0) . '</a>';
            echo '</div>';
        }
}

$moduleContent = ob_get_contents();
ob_clean();
?>