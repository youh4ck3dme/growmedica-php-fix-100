<div id="references">
    <div class="container">
        <h2><?= $cTranslator->getTranslation('Naše referencie'); ?></h2>

        <?
        if($Row['module'] != 'references') 
            echo '<link rel="stylesheet" type="text/css" href="css/mod_references.css" />';

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
                    ' . (($limit AND $limit > 0) ? ' LIMIT ' . $limit : '') . ';';

        if (!$result = mysql_query($query)) {
            echo mysql_error();
        }
        else {
            
            echo '<div id="ref-list"' . (($navigateId == 2 AND mysql_num_rows($result) > 3) ? ' data-slick=\'{"rows": 2, "slidesToShow" : 3}\'>' : '') . '>';
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
                
                if (!empty($img)) {
                    ?>
                    <div class="item">
                        <a href="<?= Menu::getHyperLinkByID(88) . '/detail/' . $row->name_seo . '/' . $row->article_id; ?>" title="<?= $row->name; ?>">
                            <div class="crust<?= ($navigateId == 2 ? ' ratio-4_3' : ' ratio-16_9'); ?>">
                                <div class="core">
                                    <img class="" src="<?= ROOTDIR . '/photos/thumbnail/' . $img_filename[0]; ?>" alt="<?= $row->name; ?>" />
                                </div>
                            </div>
                        </a>
                    </div>
                    <?
                }
            }
            echo '</div>';
            mysql_free_result($result);
        }
        ?>

    </div>
</div>