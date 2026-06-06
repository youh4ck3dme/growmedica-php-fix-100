<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_' . $Row['module'] . '.css" />';
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
$_SESSION['userPrefs']['prodMnozstvoNaStrane'] = 20;

// vykonanie akcii spojenych s odoslanim
// nacitanie obsahu modulu
ob_start();

// VYKONANIE AKCII

?>
<div class="container">
    <h1><?= Menu::gethyperLinkTextById($navigateId); ?></h1>
        <?
        
                $query = ' SELECT DATE_FORMAT(_date, "%d.%m.%Y") AS _date, ' . $lang . '_name AS name, ' . $lang . '_article AS article
                           FROM ' . TABLE_PREFIX . 'article 
                           WHERE 1 
                           AND publish = 1 
                           AND article_category_id = "1"
                           ORDER BY sorter ASC, _date DESC, ' . $lang . '_name ASC';
                
                //echo $query;
                tabulator1($query);
                $items_count = count(mysql_query($query));
                $result = mysql_query($query . $limit);
                if (!$result) {
                    echo mysql_error();
                }
                else {
                    ?>
                    <div id="faq-list">
                        <?
                        while ($row = mysql_fetch_assoc($result)) {                            
                            ?>
                            <div class="item">
                                <h3><?= $row['name']; ?></h3>
                                <div class="answer default-text"><?=  html_entity_decode($row['article'], ENT_QUOTES, "UTF-8"); ?></div>
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
                                            <a href="setup/index.php?module=article&amp;action=update&amp;article_id=<?= $row["article_id"]; ?>" target="_blank">Upravit</a>
                                        </div>
                                        <div class="clear"></div>
                                    </div>
                                    <?
                                }
                                ?>
                            </div>
                            <?
                        }
                        if ($items_count != 0) {
                            echo '<div class="vaginator">' . pagination($query, $_GET['param'], $_GET['page']) . '</div>';
                        }
                        ?>
                    </div>
                    <?
                }
                ?>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $('#faq-list .item h3').click(function(){
            $(this).siblings().toggle();
        });        
    });
</script>
<?
$moduleContent = ob_get_contents();
ob_clean();
?>