<link rel="stylesheet" type="text/css" href="./css/mod_article.css"  />
<?
//TREBA SI NASTAVIT NAZOV KATEGORIE NA 4. RIADKU
$queryStringArticle = "select a.*, date_format(a._date, '%d.%m.%Y') as _date, a." . $_SESSION['lang'] . "_name_seo as aname_seo, ac." . $lang . "_name as acname from " . TABLE_PREFIX . "article as a join " . TABLE_PREFIX . "article_category as ac using (article_category_id) left join " . TABLE_PREFIX . "user as u using (user_id) where 1 and publish = 1 and a." . $_SESSION['lang'] . "_name_seo!=''  and " . $_SESSION['lang'] . "_preview!=''
order by sorter asc, a._date desc limit 3;";
//echo $queryStringArticle;
$ResultArticle = mysql_query($queryStringArticle);
if (!$ResultArticle) {
    echo mysql_error();
} else {
    unset($img);
    echo '<div id="articleInclude">';
    while ($RowArticle = mysql_fetch_assoc($ResultArticle)) {
        preg_match('/src="([^"]+)"/', $RowArticle[$_SESSION['lang'] . '_preview'], $img_preview);
        preg_match('/src="([^"]+)"/', $RowArticle[$_SESSION['lang'] . '_article'], $img_article);

        if (empty($img_preview[1])) {
            if (!empty($img_article[1])) {
                $img = $img_article[1];
            }
        } else {
            $img = $img_preview[1];
        }

        echo '<div class="article left">';
        if (!empty($img))
            echo '<div class="image left"><div><a href="' . Menu::getHyperLinkByID(4) . '/detail/' . $RowArticle['aname_seo'] . '/' . $RowArticle['article_id'] . '" ><img alt="article_' . SEO_TITLE . '" src="' . $img . '" /></a></div></div>';
        echo '<div class="left">';
        echo '<h2 class="nadpis"><a href="' . Menu::getHyperLinkByID(NEWS_ID) . '/detail/' . $RowArticle['aname_seo'] . '/' . $RowArticle['article_id'] . '" >' . $RowArticle[$_SESSION['lang'] . '_name'] . '</a></h2>';
        echo '<div class="text">' . truncate(strip_tags($RowArticle[$_SESSION['lang'] . '_preview'], 'img'), 100) . '</div>';
        echo '</div>';
        echo '<div class="clear"></div>';
        /* 			echo '	<div class="image" class="left"><div><img src="'.$img.'" class="left"/></div></div>
          <h2 class="nadpis" class="right"><a href="'.Menu::getHyperLinkByID(16).'/detail/' . $RowArticle['aname_seo'] . '/'.$RowArticle['article_id'].'" >'.$RowArticle[$_SESSION['lang'].'_name'].'</a></h2>

          echo '<div class="both"></div>';

         */
        if ($user->isAdmin()) {
            echo '<div class="footer">';

            echo '<div class="edit-link"><img src="images/icons/edit.gif" alt="edit" /> <a href="setup/index.php?module=article&amp;action=update&amp;article_id=' . $RowArticle["article_id"] . '" target="_blank">Upravit článok</a></div>';
            echo '<div class="edit-link"><img src="images/icons/delete.gif" alt="" /> <a href="javascript:;" onclick="javascript:ConfirmBoxAc(\'Naozaj si želáte odstránit tento clánok?\', \'setup/index.php?module=article&amp;action=delete&amp;main_page=1&amp;article_id=' . $RowArticle['article_id'] . '\', \'\');">Zmazat</a></div>';

            echo '</div>';
        }
        echo '</div>';
    }
    echo '<div class="clear"></div>';

    echo '</div>';
}
?>