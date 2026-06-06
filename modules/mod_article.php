<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="' . fileWithLastChange('css/mod_' . $Row['module'] . '.css') . '" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
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
$_SESSION['userPrefs']['prodMnozstvoNaStrane'] = 10;

// vykonanie akcii spojenych s odoslanim
// nacitanie obsahu modulu
ob_start();

// VYKONANIE AKCII

$string_menu_id = 'select menu.menu_id as menu_id from ' . TABLE_PREFIX . 'menu as menu inner join ' . TABLE_PREFIX . 'module as module on menu.module_id=module.module_id where module.module="article"';
$result_menu_id = mysql_query($string_menu_id);
$row_menu_id = mysql_fetch_array($result_menu_id);
?>
<div class="container">
    <h1 class="article_h1"><?= Menu::gethyperLinkTextById($navigateId); ?></h1>

    <div class="row">
        <?
        switch ($navigateArrayUrlWithoutBase[0]) {
            case "detail":
                $query = ' SELECT a.*, IF(u.username IS NULL, "' . $cTranslator->getTranslation('Anonymný užívateľ', 0) . '", u.username) AS username,
                           DATE_FORMAT(a._date, "%d.%m.%Y") AS _date, ac.' . $lang . '_name_seo AS aname_seo
                           FROM ' . TABLE_PREFIX . 'article AS a
                           JOIN ' . TABLE_PREFIX . 'article_category AS ac USING (article_category_id)
                           LEFT JOIN ' . TABLE_PREFIX . 'user AS u USING (user_id)
                           WHERE publish=1 AND a.article_id = "' . $navigateEnd . '"
                           LIMIT 0,1';

                if (!$result = mysql_query($query)) {
                    if (mysql_errno()) {
                        echo("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
                    }
                } else {
                    $row = mysql_fetch_assoc($result);

                    $MODULE_TITLE = strip_tags($row[$lang . '_name']) . " :: " . $MODULE_TITLE;
                    $MODULE_DESCRIPTION = html_entity_decode(strip_tags($row[$lang . '_preview']) ,ENT_QUOTES, "UTF-8"). " :: " . $MODULE_DESCRIPTION;
                    ?>
                    <div id="article-detail" class="col-md-12">
                        <h2>
                            <span><?= $row[$lang . '_name']; ?></span>
                        </h2>
                        <div class="preview"><?= html_entity_decode(strip_tags($row[$lang . '_preview'], "<p><a><img>") ,ENT_QUOTES, "UTF-8"); ?></div>
                        <div class="content default-text"><?=  html_entity_decode($row[$lang . '_article'],ENT_QUOTES, "UTF-8"); ?></div>
                        <?
                        $queryString_fotogaleria = "select * from " . TABLE_PREFIX . "photo_images where 1 and photo_article_id='" . mysql_real_escape_string($navigateEnd) . "' order by sorter asc";
                        $result_fotogaleria = mysql_query($queryString_fotogaleria . $limit . ";");
                        if ($result_fotogaleria) {
                            ?>
                            <div id="photo-gallery" class="row">
                                <?
                                $i = 0;
                                while ($fotografia = mysql_fetch_object($result_fotogaleria)) {
                                    $i++;
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
                                <div class="clear"></div>
                            </div>
                            <?
                        }
                        ?>
                        <div class="footer" style="margin:5px 0 0 0">
                            <a href="<?= ROOTDIR . '/' . Menu::getHyperLinkByID($row_menu_id['menu_id']); ?>">
                                <?= $cTranslator->getTranslation("Späť na zoznam článkov"); ?></a>
                        </div>
                    </div>
                    <div class="clear"></div>
                    <?
                }
                break;
            default:
                $query = 'SELECT a.*, IF(u.username IS NULL, "' . $cTranslator->getTranslation('Anonymný užívateľ', 0) . '", u.username) AS username,
                               DATE_FORMAT(a._date, "%d.%m.%Y") AS _date, a.' . $lang . '_name_seo as aname_seo, ac.' . $lang . '_name as acname
                               FROM ' . TABLE_PREFIX . 'article AS a
                               JOIN ' . TABLE_PREFIX . 'article_category AS ac USING (article_category_id)
                               LEFT JOIN ' . TABLE_PREFIX . 'user AS u USING (user_id)
                               WHERE 1 
                               AND publish = 1 
                               AND a.' . $lang . '_name_seo != "" 
                               AND ac.' . $lang . '_name_seo = "' . $Row[$lang . '_name_seo'] . '"
                               ORDER BY sorter ASC, a._date DESC';
                //echo $query;
                tabulator1($query);
                $items_count = count(mysql_query($query));
                $result = mysql_query($query . $limit);
                if (!$result) {
                    echo mysql_error();
                }
                else {
                    ?>
                    <div id="articles-list" class="row">
                        <?
                        while ($row = mysql_fetch_assoc($result)) {
                            // VYBERIE OBRAZOK Z PREVIEW ALEBO OBSAHU CLANKU
                            $img = NULL;
                            preg_match('/src="([^"]+)"/', $row[$lang . '_preview'], $img_preview);
                            preg_match('/src="([^"]+)"/', $row[$lang . '_article'], $img_article);

                            if (empty($img_preview[1])) {
                                if (!empty($img_article[1])) {
                                    $img = $img_article[1];
                                }
                            }
                            else {
                                $img = $img_preview[1];
                            }
                            ?>
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 col-vs-12">
                                <div class="article item">
                                    <a href="<?= Menu::getHyperLinkByID($row_menu_id['menu_id']) . '/detail/' . $row['aname_seo'] . '/' . $row['article_id']; ?>">
                                        <div class="text">
                                        
                                            <h3><?= $row[$lang . '_name']; ?></h3>
                                        
                                        <?
                                        if (!empty($img)) {
                                            ?>
                                            <div class="image">
                                                <div class="crust ratio-9_4 cover" style="margin: 20px auto;">
                                                    <div class="core">
                                                        <img alt="article_<?= SEO_TITLE; ?>" src="<?= $img; ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <?
                                        }
                                        echo truncateHtml( html_entity_decode(strip_tags($row[$lang . '_preview']), ENT_QUOTES, "UTF-8"), NEWS_PREVIEW_CHAR_LIMIT);
                                        ?>
                                        </div>
                                    </a>
                                    <?
                                    if ($user->isAdmin()) {
                                        ?>
                                        <div class="footer">
                                            <div class="edit-link right">
                                                <img src="images/icons/delete.gif" alt="" />
                                                <a href="javascript:;" onclick="javascript:ConfirmBoxAc('Naozaj si želáte odstránit tento clánok?', 'setup/index.php?module=article&amp;action=delete&amp;main_page=1&amp;article_id=<?= $row['article_id']; ?>', '');">Zmazat</a>
                                            </div>
                                            <div class="edit-link right">
                                                <img src="images/icons/edit.gif" alt="edit" />
                                                <a href="setup/index.php?module=article&amp;action=update&amp;article_id=<?= $row["article_id"]; ?>" target="_blank">Upravit článok</a>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                        <?
                                    }
                                    ?>
                                </div>
                            </div>
                        <?
                        }
                        if ($items_count != 0) {
                            echo '<div class="tx-c col-lg-12 col-md-12 col-sm-12 col-xs-12 col-vs-12">' . pagination($query, $_GET['param'], $_GET['page']) . '</div>';
                        }
                        ?>
                    </div>
                    <?
                }
            }
            ?>
    </div>
</div>
<?
$moduleContent = ob_get_contents();
ob_clean();
?>