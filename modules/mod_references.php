<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_' . $Row['module'] . '.css" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
$css_file .= n . '<link rel="stylesheet" type="text/css" href="css/mod_fotogaleria.css" />';
$js_file = ''; /* <script type="text/javascript" src="js/mod_xxc.js"></script> */
$footer_js_file = '';
$MODULE_HEADER = $css_file . $js_file;
$MODULE_FOOTER = $footer_js_file;
// definovanie potrebnych inline js action // uvadzat bez oznacenia <script>
$inline_js = ''; /* $(function() { $( "#datepicker" ).datepicker(); }); */
$MODULE_INLINE_JS = $inline_js;
// titulka (ak je prazdna, pouzije sa titulka zo struktury SQL)
$MODULE_TITLE = "";
// seo prvky daneho modulu
$MODULE_DESCRIPTION = "";
$MODULE_KEYWORDS = "";
//
$_SESSION['userPrefs']['prodMnozstvoNaStrane'] = 4;

// vykonanie akcii spojenych s odoslanim
// nacitanie obsahu modulu
ob_start();

// VYKONANIE AKCII
?>
<div class="container">

    <div class="row">
        <?
        switch ($navigateArrayUrlWithoutBase[0]) {
            case "detail":

                $query = 'SELECT *, date_format(_date, "%d.%m.%Y") AS _date, 
                            ' . $_SESSION['lang'] . '_name_seo AS name_seo, 
                            ' . $_SESSION['lang'] . '_name AS name, 
                            ' . $_SESSION['lang'] . '_preview AS preview, 
                            ' . $_SESSION['lang'] . '_article AS article 
                            FROM ' . TABLE_PREFIX . 'article 
                            WHERE 1 
                            AND publish = 1 
                            AND article_id = ' . $navigateEnd . '  
                            LIMIT 0,1;';


                if (!$result = mysql_query($query)) {
                    if (mysql_errno()) {
                        echo("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
                    }
                }
                else {
                    $row = mysql_fetch_object($result);

                    $MODULE_TITLE = strip_tags($row->name) . " :: " . $MODULE_TITLE;
                    $MODULE_DESCRIPTION = html_entity_decode(strip_tags($row->preview), ENT_QUOTES, "UTF-8"). " :: " . $MODULE_DESCRIPTION;
                    ?>
                    <div id="detail" class="col-lg-12">
                        <h1><?= $row->name; ?></h1>
                        <div class="preview">
                            <?= html_entity_decode(strip_tags($row->preview, "<p><a><img>"), ENT_QUOTES, "UTF-8"); ?>
                        </div>
                        <div class="content default-text">
                            <?=  html_entity_decode($row->article, ENT_QUOTES, "UTF-8"); ?>
                        </div>
                        <?
                        $query_fotogaleria = 'SELECT * FROM ' . TABLE_PREFIX . 'photo_images WHERE 1 AND photo_article_id = "' . mysql_real_escape_string($navigateEnd) . '" ORDER BY sorter ASC';
                        $result_fotogaleria = mysql_query($query_fotogaleria . $limit . ";");
                        if ($result_fotogaleria) {
                            ?>
                            <div id="photo-gallery" class="row">
                                <?
                                while ($fotografia = mysql_fetch_object($result_fotogaleria)) {
                                    ?>
                                    <div class="col-md-3 col-sm-3 col-xs-4 col-vs-12">
                                        <div class="item">
                                            <a class="fancybox" rel="gallery" href="<?= ROOTDIR . '/photos/original/' . $fotografia->src; ?>" title="<?= $fotografia->name; ?>">
                                                <div class="crust ">
                                                    <div class="core">
                                                        <img class="" src="<?= ROOTDIR . '/photos/thumbnail/' . $fotografia->src; ?>" alt="<?= $row->name; ?>" />
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                            <?
                        }
                        ?>
                        <div class="footer" style="margin:5px 0 0 0">
                            <a href="<?= ROOTDIR . '/' . Menu::getHyperLinkByID(88); ?>">
                                <?= $cTranslator->getTranslation("Späť na zoznam článkov"); ?>
                            </a>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <?
                    mysql_free_result($result);
                }
                
                break;
            default:

                $limit = 0;
                $query = 'SELECT *, date_format(_date, "%d.%m.%Y") AS _date, 
                            ' . $_SESSION['lang'] . '_name_seo AS name_seo, 
                            ' . $_SESSION['lang'] . '_name AS name, 
                            ' . $_SESSION['lang'] . '_preview AS preview, 
                            ' . $_SESSION['lang'] . '_article AS article 
                            FROM ' . TABLE_PREFIX . 'article 
                            WHERE 1 
                            AND publish = 1 
                            AND article_category_id = 3 
                            AND ' . $_SESSION['lang'] . '_name_seo != "" 
                            AND (' . $_SESSION['lang'] . '_preview != "" 
                                OR ' . $_SESSION['lang'] . '_article != "")
                            ORDER BY sorter ASC, _date DESC 
                            ' . (($limit AND $limit > 0) ? ' LIMIT ' . $limit : '');

                tabulator1($query);
                $items_count = mysql_num_rows(mysql_query($query));
                $result = mysql_query($query . $limit);

                if (!$result) {
                    echo mysql_error();
                }
                else {
                    echo '<h1>' . Menu::gethyperLinkTextById($navigateId) . '</h1>';

                    echo '<div id="references-list" class="">';
                    while ($row = mysql_fetch_object($result)) {

                        unset($img);
                        preg_match('/src="([^"]+)"/', $row->preview, $img_preview);
                        preg_match('/src="([^"]+)"/', $row->article, $img_article);

                        if (empty($img_preview[1])) {
                            if (!empty($img_article[1])) {
                                $img = $img_article[1];
                            }
                        } else {
                            $img = $img_preview[1];
                        }
                        $img_filename = explode('/',  $img);
                        $img_filename = array_reverse($img_filename);
                        ?>

                        <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                            <div class="item">
                                <a href="<?= Menu::getHyperLinkByID(88) . '/detail/' . $row->name_seo . '/' . $row->article_id; ?>" title="<?= $row->name; ?>">
                                    <div class="crust ratio-9_4">
                                        <div class="core">
                                            <?
                                            if(!empty($img)) {
                                                ?>
                                                <img class="" src="<?= ROOTDIR . '/photos/preview/' . $img_filename[0]; ?>" alt="<?= $row->name; ?>" />
                                                <?
                                            }
                                            ?>
                                            <div class="content">
                                                <h2><?= $row->name; ?></h2>
                                                <div class="preview<?= (empty($img) ? ' unfold' : ''); ?>">
                                                    <?= ( trim(strip_tags($row->preview)) != '' ? html_entity_decode(strip_tags($row->preview, '<sup><sub>'), ENT_QUOTES, 'UTF-8') : truncateHtml( html_entity_decode(strip_tags($row->article), ENT_QUOTES, 'UTF-8'), 200) ); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </a>                                        
                            </div>
                        </div>

                        <?
                    }
                    echo '</div>';

                    if ($items_count > $_SESSION['userPrefs']['prodMnozstvoNaStrane']) {
                        echo '<div class="tx-c col-md-12 col-sm-12 col-xs-12 col-vs-12">' . pagination($query, $_GET['param'], $_GET['page']) . '</div>';
                    }
                    mysql_free_result($result);
                }
        }
        ?>
    </div>
</div>
<?
$moduleContent = ob_get_contents();
ob_clean();
?>