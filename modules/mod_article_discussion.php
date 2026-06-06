<?

include_once('shared/classes/class.discussion.php');

// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_article_discussion.css" /><link rel="stylesheet" type="text/css" href="css/mod_fotogaleria.css" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
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
//
$_SESSION['userPrefs']['prodMnozstvoNaStrane'] = 8;
// vykonanie akcii spojenych s odoslanim
// nacitanie obsahu modulu
ob_start();
// VYKONANIE AKCII
switch ($navigateArrayUrlWithoutBase[0]) {
    case "insert":
        break;

    case "edit":
        break;

    case "detail":

        $queryPocitadlo = 'UPDATE ' . TABLE_PREFIX . 'article SET pocitadlo = pocitadlo + 1 WHERE article_id = "' . $navigateEnd . '"';
        mysql_query($queryPocitadlo);

        $queryStringArticle = "
				select a.*, if(u.username is null,
				'anonymný užívateľ', u.username) as username,
				date_format(a._date, '%d.%m.%Y') as _date,
				date_format(a.last_update, '%d.%m.%Y') as last_update,
				ac." . $lang . "_name_seo as aname_seo
				from " . TABLE_PREFIX . "article as a
				join " . TABLE_PREFIX . "article_category as ac using (article_category_id)
				left join " . TABLE_PREFIX . "user as u using (user_id)
				where 1 and publish = 1 and a.article_id = '" . $navigateEnd . "' limit 1;";
        if (!$ResultArticle = mysql_query($queryStringArticle)) {
            if (mysql_errno())
                print("MySql Error (" . mysql_errno() . "): "
                        . mysql_error() . "<br />");
        }
        else {
            $RowArticle = mysql_fetch_assoc($ResultArticle);
            $txt = substr($RowArticle[$_SESSION['lang'] . '_preview'], strpos($RowArticle[$_SESSION['lang'] . '_preview'], "<img"));
            $odsek = (strlen($txt) - strpos($txt, ">")) - 1;
            $image = substr($txt, 0, -$odsek);
            $MODULE_TITLE = $RowArticle[$_SESSION['lang'] . '_name'] . " : " . PROJECT_NAME;


            print '
					<div id="article-detail">
						' . $image . '
						<h2>' . $RowArticle[$_SESSION['lang'] . '_name'] . '</h2>

						<span class="date-author">' .
                    $cTranslator->getTranslation('Pridané: ') . ' <strong>' . $RowArticle['_date'] . '</strong> &nbsp;&nbsp; ' .
                    $cTranslator->getTranslation('pridal: ') . ' <strong>' . $RowArticle['autor'] . '</strong>
						</span>

						<div class="both content-separator"></div>
						<div class="preview">' . strip_tags($RowArticle[$_SESSION['lang'] . '_preview'], "<p><a>") . '</div>
						<div class="text">' . $RowArticle[$_SESSION['lang'] . '_article'] . '</div>';

            include ('include/mod_article_gallery.php');

            //<div class="text">'. str_replace('rel="lightbox[foto]"', 'rel="lytebox[vacation]"', str_replace('rel="lightbox_galeria"', 'rel="lytebox[vacation]"', str_replace('rel="lightbox_foto"', 'rel="lytebox[vacation]"', $RowArticle[$_SESSION['lang'].'_article']))) . '</div>';
            print '<br/><br/>' . $cTranslator->getTranslation('Posledná zmena: ') . ' ' . $RowArticle['last_update'];

            if ($user->isAdmin()) {
                print '
								<div class="footer">
									<img src="images/icons/edit.gif" alt="alt="' . SEO_TITLE . '""/> <a href="setup/index.php?module=article&amp;action=update&amp;article_id=' . $RowArticle["article_id"] . '" target="_blank">Upravit článok</a>&nbsp;&nbsp;<img src="images/icons/delete.gif" alt="' . SEO_TITLE . ' /> <a href="javascript:;" onclick="javascript:ConfirmBoxAc(\'Naozaj si želáte odstránit tento clánok?\', \'setup/index.php?module=article&amp;action=delete&amp;main_page=1&amp;article_id=' . $RowArticle['article_id'] . '\', \'\');">Zmazat</a>
								</div>';
            }
            print '<div class="footer">
						<a href="' . Menu::getHyperLinkByID(5) . '">' . $cTranslator->getTranslation("Späť na zoznam článkov") . '</a>
						<div class="fb-like right" data-send="false" data-width="350" data-show-faces="true" data-action="recommend"></div>
					   </div>
					</div>';

            echo '<div class="both content-separator"></div>';

            $obj = new Discussion;
            $obj->setArticleId($navigateEnd);

            echo $obj->showFormNew();
            if (!empty($_POST)) {
                $obj->insertComment();
            }

            $obj->getLookIn();
            echo '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/diskusia/' . $navigateEnd . '">' . $cTranslator->getTranslation('Vstup do diskusie') . '</a>';
        }
        break;

    case 'diskusia':

        $obj = new Discussion;
        $obj->setArticleId($navigateEnd);

        if (!empty($_POST)) {
            echo $obj->insertComment();
        }
        echo $obj->showFormNew();

        $obj->getDiscussion();

        break;

    case 'reagovat':

        $obj = new Discussion;
        $obj->setArticleId($navigateArrayUrlWithoutBase[1]);
        $obj->setReplyId($navigateEnd);

        if (!empty($_POST)) {
            echo $obj->insertComment();
        }
        echo $obj->showFormNew();

        $obj->getDiscussion();

        break;

    case "delete":
        break;
    default:
        if (isset($_GET['year']) && isset($_GET['month']))
            $sqlAddition = "AND DATE_FORMAT(a._date, '%Y')='" . $_GET['year'] . "' AND DATE_FORMAT(a._date, '%c')='" . $_GET['month'] . "'";

        $queryStringArticle = "select a.*,
									if(u.username is null, 'anonymny uzivatel', u.username) as username,
									date_format(a._date, '%d.%m.%Y') as _date,
									a." . $_SESSION['lang'] . "_name_seo as aname_seo,
									ac." . $lang . "_name as acname
									from " . TABLE_PREFIX . "article as a join " . TABLE_PREFIX . "article_category as ac using (article_category_id) left join " . TABLE_PREFIX . "user as u using (user_id) where 1 and publish = 1 and a." . $_SESSION['lang'] . "_name_seo!='' and ac." . $lang . "_name_seo = '" . $Row[$_SESSION['lang'] . "_name_seo"] . "' " . $sqlAddition . "  order by sorter asc, a._date desc";
        //print $queryStringArticle;
        tabulator1($queryStringArticle);
        $ResultArticle = mysql_query($queryStringArticle . $limit . ";");
        if (!$ResultArticle) {
            print mysql_error();
        } else {
            print '<div id="article-zoznam">';
            //print "<h1>".$Row[$_SESSION['lang']."_name"]."</h1><br />";

            while ($RowArticle = mysql_fetch_assoc($ResultArticle)) {
                $txt = substr($RowArticle[$_SESSION['lang'] . '_preview'], strpos($RowArticle[$_SESSION['lang'] . '_preview'], "<img"));
                $odsek = (strlen($txt) - strpos($txt, ">")) - 1;
                $image = substr($txt, 0, -$odsek);

                //echo $image;

                print '<div class="article">
								<table>
									<tr>
										<td rowspan="2" valign="top">' . $image . '</td>
										<td valign="top" height="10"><h2><a href="./' . $_SESSION['lang'] . '/' . $menuUrl . '/detail/' . $RowArticle['aname_seo'] . '/' . $RowArticle['article_id'] . '" >' . $RowArticle[$_SESSION['lang'] . '_name'] . '</a></h2></td>
									</tr>
									<tr>
										<td valign="top">' . strip_tags($RowArticle[$_SESSION['lang'] . '_preview']) . '</td>
									</tr>
								</table>';

                if ($user->isAdmin()) {
                    print '
								<div class="footer">
									<img src="images/icons/edit.gif" alt="' . SEO_TITLE . '"/> <a href="setup/index.php?module=article&amp;action=update&amp;article_id=' . $RowArticle["article_id"] . '" target="_blank">Upravit článok</a>&nbsp;&nbsp;<img src="images/icons/delete.gif" alt="' . SEO_TITLE . '" /> <a href="javascript:;" onclick="javascript:ConfirmBoxAc(\'Naozaj si želáte odstránit tento clánok?\', \'setup/index.php?module=article&amp;action=delete&amp;main_page=1&amp;article_id=' . $RowArticle['article_id'] . '\', \'\');">Zmazat</a>
								</div>';
                }

                print '</div>';
                print '<div class="clear"></div>';
            }
            print tabulator_zobrazeny($queryStringArticle, $_GET['param']);
            print '</div>';
        }
}

$moduleContent = ob_get_contents();
ob_clean();
?>