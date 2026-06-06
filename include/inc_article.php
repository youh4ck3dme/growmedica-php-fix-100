<?
if($Row['module'] != 'article') 
    echo '<link rel="stylesheet" type="text/css" href="css/mod_article.css" />';

//TREBA SI NASTAVIT NAZOV KATEGORIE NA 4. RIADKU
$limit = 'LIMIT 3';
$queryListArticle = 'SELECT a.*,
                    DATE_FORMAT(a._date, "%d.%m.%Y") AS _date, a.' . $lang . '_name_seo AS aname_seo, ac.' . $lang . '_name AS acname
                    FROM ' . TABLE_PREFIX . 'article AS a
                    JOIN ' . TABLE_PREFIX . 'article_category AS ac USING (article_category_id)
                    WHERE 1 AND publish = 1 
                    AND a.' . $lang . '_name_seo != ""
                    AND article_category_id = "2" ';
// $queryListArticle .= 'AND ac.' . $lang . '_name_seo = "' . $Row[$lang . '_name_seo'] . '" ';
$queryListArticle .= 'ORDER BY sorter ASC, a._date DESC ';
if(!isset($limit)) {
    tabulator1($queryListArticle);
    $items_count = count(mysql_query($queryListArticle));
}
// echo "Q: " . $queryListArticle;
$resultListArticle = mysql_query($queryListArticle . $limit);

if (!$resultListArticle) {
    echo mysql_error();
}
else {
    echo '<div class="container">';
    echo '<div id="articles-list" class="row">';
    echo '<h2 class="section-head"><a href="' . Menu::getHyperlinkById(308) . '">' . $cTranslator->getTranslation('Blog', 0). '</a></h2>';
    while ($rowArticle = mysql_fetch_assoc($resultListArticle)) {
        unset($img);
        preg_match('/src="([^"]+)"/', $rowArticle[$lang . '_preview'], $img_preview);
        preg_match('/src="([^"]+)"/', $rowArticle[$lang . '_article'], $img_article);

        if (empty($img_preview[1])) {
            if (!empty($img_article[1])) {
                $img = $img_article[1];
            }
        }
        else {
            $img = $img_preview[1];
        }        

        echo '<div class="article col-lg-4 col-md-4 col-sm-4 col-xs-6 col-vs-12">';
            echo '<div class="item">';
                echo '<a href="' . Menu::getHyperLinkByID(NEWS_ID) . '/detail/' . $rowArticle['aname_seo'] . '/' . $rowArticle['article_id'] . '">';
                    

                            echo '<div class="">';
                                echo '<h3 class="nadpis">' . $rowArticle[$_SESSION['lang'] . '_name'] . '</h3>';
                                echo '<div class="crust ratio-9_4 cover" style="margin: 20px auto;">';
                                echo '<div class="core">';
                                    echo '<div class="">';
                                    if (!empty($img))
                                        echo '<img src="' .  $img . '" alt="article_' . SEO_TITLE . '" />';
                                    else
                                        echo '<img src="' . ROOTDIR . '/images/wrapper/blank-news-image.png);" alt="blank-news-image" />';
                                    echo '</div>';

                                echo '</div>';
                                echo '</div>';
                                echo '<div class="text">';
                                    if(trim($rowArticle[$lang . '_preview']) != '')
                                        echo truncateHtml(html_entity_decode(strip_tags($rowArticle[$lang . '_preview']), ENT_QUOTES, 'UTF-8'), NEWS_PREVIEW_CHAR_LIMIT);
                                    else
                                        echo truncate(strip_tags($rowArticle[$lang . '_article'], 'img'), NEWS_PREVIEW_CHAR_LIMIT);
                                echo '</div>';
                            echo '</div>';

                    
                echo '</a>';
            if ($user->isAdmin()) {
                echo '<div class="footer col-md-12">';
                    echo '<div class="edit-link"><img src="images/icons/edit.gif" alt="edit" /> <a href="setup/index.php?module=article&amp;action=update&amp;article_id=' . $rowArticle["article_id"] . '" target="_blank">Upravit článok</a></div>';
                    echo '<div class="edit-link"><img src="images/icons/delete.gif" alt="" /> <a href="javascript:;" onclick="javascript:ConfirmBoxAc(\'Naozaj si želáte odstránit tento clánok?\', \'setup/index.php?module=article&amp;action=delete&amp;main_page=1&amp;article_id=' . $rowArticle['article_id'] . '\', \'\');">Zmazat</a></div>';
                echo '</div>';
            }
            echo '</div>';

        echo '</div>';
        
    }
    echo '</div>';
    echo '</div>';
    if ($items_count != 0) {
    //    echo '<div class="col-md-12">' . tabulator_zobrazeny($queryListArticle, $_GET['param']) . '</div>';
        echo '<div class="col-md-12">' . pagination($queryListArticle, Menu::getHyperLinkByID(NEWS_ID)) . '</div>';
    }
}
?>