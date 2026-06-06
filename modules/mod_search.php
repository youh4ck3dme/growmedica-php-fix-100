<?

// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_search.css" />' . n; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
$css_file .= '<link rel="stylesheet" type="text/css" href="css/mod_eshop.css" />' . n;
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
?>
<div class="container">
<?
switch ($navigateArrayUrlWithoutBase[0]) {
    case "insert":
        break;

    case "edit":
        break;

    case "detail":
        break;

    case "delete":
        break;
    default:
        echo '<h1>' . $cTranslator->getTranslation('Vyhľadávanie', 0) . '</h1>';
        ?>
        <form id="search-form" name="search-form" class="form-horizontal" action="<?= ROOTDIR . '/' . Menu::getHyperlinkById($navigateId); ?>" method="get">
            <div class="form-group row">
                <label for="q" class="col-md-3 control-label"><span><?= $cTranslator->getTranslation('Hľadaný výraz'); ?>: <strong></strong></span></label>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="q" name="q" value="<?= $_GET['q']; ?>" />
                </div>
                <div class="col-md-3">
                    <input type="submit" value="<?= $cTranslator->getTranslation('Hľadať', 0); ?>" class="" />
                </div>
            </div>
            
        </form>
        <script language="javascript" type="text/javascript">
            var frmvalidator = new Validator("search-form");
            frmvalidator.addValidation("q", "minlen=3", "<?= $cTranslator->getTranslation('Minimálna dlžka hľadaného výrazu sú 4 znaky', 0); ?>");
            frmvalidator.addValidation("q", "req", "<?= $cTranslator->getTranslation('Prosím zadajte hľadaný výraz', 0); ?>");
        </script>
        <?

        if (isset($_GET['q'])) {
            echo '<br/><br/>';
            echo '<h2>' . $cTranslator->getTranslation("Pre hľadaný výraz") . ' <em>' . $_GET['q'] . '</em> ' . $cTranslator->getTranslation("sa našli tieto záznamy") . '</h2>';
            echo '<div class="mod-search">';

            if (!empty($_GET['q']) and $_GET['q'] != "Search") {
                mysql_query("INSERT INTO " . TABLE_PREFIX . "search_log VALUES ('" . $_GET['q'] . "','" . $_SERVER['REMOTE_ADDR'] . "',now())");
            }

            $error1 = false;
            $error2 = false;

            if (strlen($_GET['q']) <= 3) {
                echo '<p class="red">' . $cTranslator->getTranslation("Hľadaný výraz nesmie byť kratší ako štyri znaky.", 0) . '</p>';
            } else {
                if (PRODUCT_SEARCH == '1') {
                    /*                     * vyhľadávanie v produktoch************************************************************************************************* */
                    $sqlSearch = 'SELECT *,
                                        product_id AS id,
                                        ' . $lang . '_name AS name,
                                        ' . $lang . '_name_seo AS name_seo,
                                        ' . $lang . '_description AS description,
                                        ' . $lang . '_keywords AS keywords,
                                        MATCH(' . $lang . '_name,
                                              ' . $lang . '_description,
                                              ' . $lang . '_keywords) AGAINST ("' . $_GET['q'] . '") AS score
                                    FROM
                                        ' . TABLE_PREFIX . 'product
                                    WHERE
                                        MATCH(' . $lang . '_name,
                                              ' . $lang . '_description,
                                              ' . $lang . '_keywords) AGAINST ("' . $_GET['q'] . '") AND available="1"
                                    ORDER BY
                                        score DESC';

                    //echo $sqlSearch;
                    $resultSearch = mysql_query($sqlSearch);
                    if ($resultSearch) {
                        if (mysql_num_rows($resultSearch) > 0) {
                            //echo '<h2>vyhľadávanie v produktoch</h2>';
                            echo '<div id="catalogue" class="row">';
                            while ($rowC = mysql_fetch_object($resultSearch)) {
                                ?>
                                <div class="col-6 col-sm-4 col-xl-3">
                                    <!--<div class="left-bar">
                                        <? /*
                                        if ($rowC->action == 1) {
                                            echo '<img src="images/wrapper/vypredaj.png">';
                                        }
                                        if ($rowC->action == 1) {
                                            echo '<img src="images/wrapper/akcia.png">';
                                        } 
                                        if ($rowC->recommend == 1) {
                                            echo '<img src="images/wrapper/odporucane.png">';
                                        }
                                        if ($rowC->novelty == 1) {
                                            echo '<img src="images/wrapper/novinka.png">';
                                        } */
                                        ?>
                                    </div>-->
                                    <div class="item">
                                        <a href="<?= ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) ?>/produkt/<?= String::SEOFriendlyText($rowC->name) . '/' . $rowC->product_id ?>" title="<?= $rowC->name ?>">
                                            <div class="crust ratio-4_3">
                                                <div class="core">
                                                    <?
                                                    if (is_file("./photos/thumbnail/" . $rowC->image_src)) { ?>
                                                        <img src="photos/thumbnail/<?= $rowC->image_src; ?>" alt="<?= $rowC->name; ?>" /><?
                                                    } else {
                                                        echo '<center>' . $cTranslator->getTranslation('Žiaden obrázok', 1) . '</center>';
                                                    }
                                                    /*if($rowC->delivery_time == '1') {
                                                        echo '<div class="delivery-time"><img src="images/wrapper/icon-mame-skladom.png" alt="' . $cTranslator->getTranslation('skladom', 0) . '" /></div>';
                                                    }
                                                    else {
                                                        echo '<div class="delivery-time"><img src="images/wrapper/icon-na-objednavku.png" alt="' . $cTranslator->getTranslation('na objednávku', 0) . '" /></div>';
                                                    }*/
                                                    ?>
                                                    <div class="status-container">
                                                        <?
                                                        /*if (strtotime(date("Y-m-d H:i")) < strtotime($rowC->date . NEW_PRODUCT_LENGTH)) {
                                                            echo '<span class="new">' . $cTranslator->getTranslation('nový', 0) . '</span>';
                                                        }*/
                                                        if ($rowC->novelty == '1') {
                                                            echo '<span class="new">' . $cTranslator->getTranslation('nový', 0) . '</span>';
                                                        }
                                                        if ($rowC->sale == '1') {
                                                            echo '<span class="sale">' . $cTranslator->getTranslation('výpredaj', 0) . '</span>';
                                                        }
                                                        if ($rowC->recommend == '1') {
                                                            echo '<span class="recommended">' . $cTranslator->getTranslation('odporúčaný', 0) . '</span>';
                                                        }
                                                        if ($rowC->action == '1') {
                                                            echo '<span class="' . ($rowC->action == '1' ? ' action' : '') . '">' . $cTranslator->getTranslation('akcia', 0) . '</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="directive">
                                                <h2><?= $rowC->name; ?></h2>
                                                <div class="price-box">
                                                    <?
                                                    if($rowC->delivery_time == '1') {
                                                        echo '<img src="images/wrapper/skladom.svg"><span style="font-size:9px;color:#4A6D22;">'.$cTranslator->getTranslation('skladom', 0).'</span>';
                                                    }
                                                    else {
                                                        echo '<img src="images/wrapper/na_objednavku.svg"><span style="font-size:9px;color:#936544;">'.$cTranslator->getTranslation('na objednávku', 0).'</span>';
                                                    }
                                                    if (VAT_VISIBILITY === TRUE) {
                                                        echo '<p class="price old">' . (!empty($rowC->price_old) ? number_format(($rowC->price_old / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;' : '') . '</p>';
                                                        echo '<p class="price">' . number_format(($rowC->price / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;</p>';
                                                    }
                                                    else {
                                                        echo '<p class="price">' . number_format($rowC->price, 2, '.', ' ') . '&nbsp;&euro;<br/>';
                                                        if (!empty($rowC->price_old)) {
                                                            echo '<p class="price old">-' . (!empty($rowC->price_old) ? number_format(((1-($rowC->price)/($rowC->price_old))*100),2,'.',' ') : '') . '%</p>';
                                                        }
                                                        else {
                                                            echo '<p class="price old"></p>';
                                                        }
                                                        echo'</p>';
                                                    }
                                                    if ($rowC->price_old > 0 AND $rowC->price < $rowC->price_old) {
                                                        echo '<div class="discount-box">';
                                                            echo '<p class="percentage-discount"><span>' . $cTranslator->getTranslation('Zľava') . '</span><br />' . percentageDiscount($rowC->price, $rowC->price_old, 0) . '<span>%</span></p>';            
                                                        echo '</div>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </a>
                                         <a href="<?= ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) ?>/produkt/<?= String::SEOFriendlyText($rowC->name) . '/' . $rowC->product_id ?>" title="<?= $rowC->name ?>" class="to-detail"><?= $cTranslator->getTranslation('detail');?></a>
                                    </div>
                                </div>
                                <?
                                /*
                                echo '<div class="item product">';
                                echo '<h3><a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/produkt/' . $line->name_seo . '/' . $line->id . '">' . $line->name . '</a></h3>';

                                $buf = strip_tags($line->description); // odstranime html tagy a ine hluposti
                                $buf = strtolower($buf); // vsetky pismena budu male

                                $sstr = strpos($buf, strtolower($_GET['q']));
                                $buf = '... ' . substr($buf, $sstr, 300) . ' ...';
                                $buf = str_replace(strtolower($_GET['q']), '<span class="strong italic">' . strtolower($_GET['q']) . '</span>', $buf);  // vyznacime hladany sa retazec

                                echo '<p class="content">' . $buf . '</p>';

                                echo '<a class="link" href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/produkt/' . $line->name_seo . '/' . $line->id . '" >' . ROOTDIR . '/' . $_SESSION['lang'] . '/' . $line->name_seo . '</a>';
                                echo '</div>';
                                */
                            }
                            echo '</div>';
                        } else {
                            $error['product'] = $cTranslator->getTranslation('Hľadaný výraz nebol nájdený medzi produktami.', 0);
                        }
                    } else {
                        echo mysql_error();
                    }

                    @mysql_free_result($resultSearch);
                }

                /*                 * ************************************************************************************* */


                /*                 * vyhľadávanie v stránkach************************************************************************************************** */
                
                /*
                $sqlSearch = 'SELECT
                                    menu_id AS id,
                                    ' . $lang . '_name AS name,
                                    ' . $lang . '_name_seo AS name_seo,
                                    ' . $lang . '_content AS content,
                                    MATCH(' . $lang . '_name,
                                          ' . $lang . '_name_seo,
                                          ' . $lang . '_content,
                                          ' . $lang . '_page_title,
                                          ' . $lang . '_description,
                                          ' . $lang . '_keywords) AGAINST ("' . $_GET['q'] . '") AS score
				                FROM
                                    ' . TABLE_PREFIX . 'menu
				                WHERE
                                    MATCH(' . $lang . '_name,
                                          ' . $lang . '_name_seo,
                                          ' . $lang . '_content,
                                          ' . $lang . '_page_title,
                                          ' . $lang . '_description,
                                          ' . $lang . '_keywords) AGAINST ("' . $_GET['q'] . '") AND (private="0" OR menu_id = 91)
                                ORDER BY
                                    score DESC';
                //echo $sqlSearch;
                $resultSearch = mysql_query($sqlSearch);
                if ($resultSearch) {
                    if (mysql_num_rows($resultSearch) > 0) {
                        //echo '<h2>štruktúra</h2>';
                        while ($line = mysql_fetch_object($resultSearch)) {
                            echo '<div class="item page">';
                            echo '<h3><a href="' . ROOTDIR . '/' . $_SESSION['lang'] . '/' . $line->name_seo . '">' . $line->name . '</a></h3>';

                            $buf = strip_tags($line->content); // odstranime html tagy a ine hluposti
                            $buf = strtolower($buf); // vsetky pismena budu male

                            $sstr = strpos($buf, strtolower($_GET['q']));
                            $buf = '... ' . substr($buf, $sstr, 300) . ' ...';
                            $buf = str_replace(strtolower($_GET['q']), '<span class="strong italic">' . strtolower($_GET['q']) . '</span>', $buf);  // vyznacime hladany sa retazec

                            echo '<p class="content">' . $buf . '</p>';

                            echo '<a class="link" href="' . ROOTDIR . '/' . $_SESSION['lang'] . '/' . $line->name_seo . '" >' . ROOTDIR . '/' . $_SESSION['lang'] . '/' . $line->name_seo . '</a>';
                            echo '</div>';
                        }
                    } else {
                        $error['page'] = $cTranslator->getTranslation('Hľadaný výraz nebol nájdený v štruktúre stránky.', 0);
                    }
                } else {
                    echo mysql_error();
                }

                @mysql_free_result($resultSearch);

                */

                /*                 * ************************************************************************************* */


                /*                 * vyhľadávanie v článkoch*************************************************************************************************** */
                
                /*

                $sqlSearch = 'SELECT
                                    ac.' . $lang . '_name_seo AS category_name_seo,
                                    a.' . $lang . '_name AS name ,
                                    a.' . $lang . '_name_seo AS name_seo ,
                                    a.article_id AS id,
                                    MATCH(a.' . $lang . '_name,
                                          a.' . $lang . '_name_seo ,
                                          a.' . $lang . '_preview,
                                          a.' . $lang . '_article,
                                          a.' . $lang . '_title,
                                          a.' . $lang . '_description,
                                          a.' . $lang . '_keywords) AGAINST ("' . $_GET['q'] . '") AS score
                                FROM
                                    ' . TABLE_PREFIX . 'article AS a
                                LEFT JOIN
                                    ' . TABLE_PREFIX . 'article_category AS ac using (article_category_id)
                                WHERE
                                    MATCH(a.' . $lang . '_name,
                                          a.' . $lang . '_name_seo,
                                          a.' . $lang . '_preview,
                                          a.' . $lang . '_article,
                                          a.' . $lang . '_title,
                                          a.' . $lang . '_description,
                                          a.' . $lang . '_keywords) AGAINST ("' . $_GET['q'] . '")
                                ORDER BY
                                    score DESC';

                //echo $sqlSearch.'<br />';
                $resultSearch = mysql_query($sqlSearch);
                if ($resultSearch) {
                    if (mysql_num_rows($resultSearch) > 0) {
                        //echo '<h2>vyhľadávanie v článkoch</h2>';
                        while ($line = mysql_fetch_object($resultSearch)) {
                            echo '<div class="item article">';
                            echo '<h3><a href="' . ROOTDIR . '/' . $_SESSION['lang'] . '/' . $line->category_name_seo . '/detail/' . $line->name_seo . '/' . $line->id . '">' . $line->name . '</a></h3>';

                            if (strpos($line->article, strtolower($_GET['q'])) !== false) {
                                $buf = strip_tags($line->name); // odstranime html tagy a ine hluposti
                            } elseif (strpos($line->preview, strtolower($_GET['q'])) !== false) {
                                $buf = strip_tags($line->name); // odstranime html tagy a ine hluposti
                            } else {
                                $buf = strip_tags($line->name); // odstranime html tagy a ine hluposti
                            }
                            $buf = strtolower($buf); // vsetky pismena budu male

                            $sstr = strpos($buf, strtolower($_GET['q']));
                            $buf = '... ' . substr($buf, $sstr, 300) . ' ...';
                            $buf = str_replace(strtolower($_GET['q']), '<span class="strong italic">' . strtolower($_GET['q']) . '</span>', $buf);  // vyznacime hladany sa retazec

                            echo '<p class="content">' . $buf . '</p>';

                            echo '<a class="link" href="' . ROOTDIR . '/' . $_SESSION['lang'] . '/' . $line->category_name_seo . '/detail/' . $line->name_seo . '/' . $line->id . '" >' . ROOTDIR . '/' . $_SESSION['lang'] . '/' . $line->category_name_seo . '/detail/' . $line->name_seo . '/' . $line->id . '</a>';
                            echo '</div>';
                        }
                    } else {
                        $error['article'] = $cTranslator->getTranslation('Hľadaný výraz nebol nájdený medzi článkami.', 0);
                    }
                } else {
                    echo mysql_error();
                }

                @mysql_free_result($resultSearch);
                */

                // vypis chybovych hlasok
                if (!empty($error)) {
                    foreach ($error as $key => $value) {
                        echo '<strong>' . $value . '</strong><br />';
                    }
                }
            }
            echo '</div>';
        }
}
?>
</div>
<?

$moduleContent = ob_get_contents();
ob_clean();
?>