<div id="leftMenu">
    <h2>Články</h2>
    <p>V tejto sekcii sa spravuje štruktúra stránky a jednotlivé podstránky.</p>
    <div id="submenu">
        <a href="index.php?module=article&amp;action=insert" class="addNew">Pridať <br /> článok</a>
        <a href="index.php?module=article_gallery&amp;action=insert" class="addNew">Galéria k<br /> článkom</a>
        <!--<a href="index.php?module=article_category" class="detail">Kategórie<br /> článkov</a>
        <a href="index.php?module=article_category&amp;action=insert" class="addNew">Pridať <br /> kategóriu článok</a>-->
    </div>
</div>
<div id="moduleContent">
    <?
    switch ($_GET['action']) {
        case "insert":
            $defaultLang = $cLanguage->getLanguageCodes();
            $defaultLang = $defaultLang[0];
            if (isset($_POST) && !empty($_POST)) {
                //	zmenime  udaje   v   databaze
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {

                    $queryInclude1 .= "`" . strtolower($val) . "_name`, `" . strtolower($val) . "_name_seo`, `" . strtolower($val) . "_preview`, `" . strtolower($val) . "_article`,";

                    $queryInclude2 .= "'" . ( str_replace(array('"content/', '"../../../content/', '"../../../../content/'), '"' . ROOTDIR . '/content/', html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8")) . "', '" . str_replace(array("\r\n", "\r", "\n","'",'"',"`"), "",ReturnSEOFriendlyUrl(html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8"), $_GET['child_of'], strtolower($val))) . "', '" .
                            (str_replace(array('"content/', '"../../../content/', '"../../../../content/'), '"' . ROOTDIR . '/content/', html_entity_decode($_POST[strtolower($val) . '_preview'], ENT_QUOTES, "UTF-8"))) . "', '" . (html_entity_decode($_POST[strtolower($val) . '_article'], ENT_QUOTES, "UTF-8")) ) . "', ";
                }
                $queryString = "insert into " . TABLE_PREFIX . "article (" . $queryInclude1 . "user_id, _default, publish, article_category_id, _checked, _date) values (" . $queryInclude2 . "'" . $_SESSION['user_id'] . "', '" . ((isset($_POST['default'])) ? 1 : 0) . "', '" . ((isset($_POST['publish'])) ? 1 : 0) . "', '" . $_POST['article_category_id'] . "', '1', '" . (empty($_POST["_date_insert"]) ? date("Y-m-d") : $_POST["_date_insert"]) . "');";
                if (!$Result = mysql_query($queryString)) {
                    Message::setMessage('Článok nebol vložený.', 2);
                    if (mysql_errno()) {
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
                        print $queryString;
                    }
                } else {
                    Message::setMessage('Článok bol úspešne vložený.', 0);
                    makeLog("Vlozenie clanku", $_POST[sk_name] . "<br>" . $_POST[sk_preview] . "<br>" . $_POST[sk_article]);
                    header("Location:index.php?module=article");
                    exit;
                }
            }
            ?>
            <h1>Pridanie článku</h1>
            <form method="post" action="" enctype="multipart/form-data">
                <table summary="" class="tableform" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <?
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        ?>
                        <tr>
                            <td>Názov článku<sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;"><?= $val; ?></sup></td>
                            <td>&nbsp;</td>
                            <td><input name="<?= strtolower($val); ?>_name" type="text" class="textbox1" id="name"  value="<?= $_POST[strtolower($val) . '_name']; ?>" /></td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr>
                        <td>Kategória</td>
                        <td></td>
                        <td>
                            <select name="article_category_id" id="article_category_id" style="width: 256px;">
                                <option value="">zvoľte kategóriu</option>
                                <?
                                $queryString = "select article_category_id as id, sk_name from " . TABLE_PREFIX . "article_category where 1 order by sk_name;";

                                if ($ResultQ = mysql_query($queryString)) {
                                    while ($RowQ = mysql_fetch_assoc($ResultQ)) {
                                        print '
						<option value="' . $RowQ['id'] . '"' . (($_POST['article_category_id'] == $RowQ['id']) ? ' selected="selected"' : '') . '>' . $RowQ['sk_name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td><input name="publish" type="checkbox" id="publish"<?= ((isset($_POST['publish'])) ? ' checked' : '') ?> />
                            článok je publikovaný</td>
                    </tr>
                    <tr class="hide-for-faq">
                        <td>&nbsp;</td>
                        <td></td>
                        <td><input name="default" type="checkbox" id="default"<?= ((isset($_POST['_default'])) ? ' checked' : '') ?> />
                            článok sa zobrazuje na titulke</td>
                    </tr>
                    <tr class="hide-for-faq">
                        <td>Dátum pridania článku</td>
                        <td></td>
                        <td>
                            <input name="_date_insert" type="text" class="datepicker" size="10" maxlength="10" readonly="readonly" value="<?= $_POST['_date_insert'] ?>" />
                            <img src="images/img.gif" alt="" name="_date_button" border="0" align="absmiddle" class="datepicker-button" />
                        </td>
                    </tr>
                    <?
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        ?>
                        <tr class="hide-for-faq">
                            <td valign="top">Perex<sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;"><?= $val; ?></sup></td>
                            <td></td>
                            <td>
                                <textarea class="ckeditor" name="<?= strtolower($val); ?>_preview"><?= html_entity_decode($_POST[strtolower($val) . '_preview'], ENT_QUOTES, "UTF-8"); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">Plné znenie článku<sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;"><?= $val; ?></sup></td>
                            <td></td>
                            <td>
                                <textarea class="ckeditor" name="<?= strtolower($val); ?>_article"><?= html_entity_decode($_POST[strtolower($val) . '_article'], ENT_QUOTES, "UTF-8"); ?></textarea>
                            </td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr class="hide-for-faq">
                        <td colspan="3">
                            <? include("../popups/foto.php"); ?>
                            <? include("../popups/docs.php"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td align="left">
                            <button type="submit" class="button">Pridať</button>
                        </td>
                    </tr>
                </table>
            </form>
            <script type="text/javascript">
                    
                $(document).ready(function() {

                    var faqCatId = 1;
                    $('#article_category_id').change(function() {
                        //console.log('this: ' + this.value);
                        if(this.value == faqCatId) {
                            $('.hide-for-faq').hide(200);
                        }
                        else {
                            $('.hide-for-faq').show(200);
                        }
                    });
                    if($("#article_category_id").val() == faqCatId) {
                        //$('#article_category_id').prop('disabled', 'disabled');
                        $('.hide-for-faq').hide();
                    }
                    //console.log('sel: ' + $("#article_category_id").val());

                });

            </script>
            <?
            break;
        case "update":
            $defaultLang = $cLanguage->getLanguageCodes();
            $defaultLang = $defaultLang[0];
            if (isset($_POST) && !empty($_POST)) {
                //	zmenime  udaje   v   databaze
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude .= "`" . strtolower($val) . "_preview` = '" . ( (str_replace(array('"content/', '"../../../content/', '"../../../../content/'), '"' . ROOTDIR . '/content/', html_entity_decode($_POST[strtolower($val) . '_preview'], ENT_QUOTES, "UTF-8"))) ) . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_article` = '" . ( (str_replace(array('"content/', '"../../../content/', '"../../../../content/'), '"' . ROOTDIR . '/content/', html_entity_decode($_POST[strtolower($val) . '_article'], ENT_QUOTES, "UTF-8"))) ) . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_name` = '" . addslashes(html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8")) . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_name_seo` = '" . str_replace(array("\r\n", "\r", "\n","'",'"',"`"), "",ReturnSEOFriendlyUrl(html_entity_decode($_POST[strtolower($val) . '_name'], $_GET['child_of'], strtolower($val)))) . "', ";
                }
                $queryString = "update
				 " . TABLE_PREFIX . "article set
				 `_checked` = '1',
				  _default = '" . ((isset($_POST['default'])) ? 1 : 0) . "',
				  publish = '" . ((isset($_POST['publish'])) ? 1 : 0) . "',
				  " . $queryInclude . "
				  article_category_id = '" . $_POST['article_category_id'] . "',
				  _date = '" . ((eregi("^([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})$", $_POST["_date"])) ? $_POST["_date"] : 'now()') . "'
				  where 1 and
				  article_id = '" . $_GET['article_id'] . "';";

                if (!$Result = mysql_query($queryString)) {
                    Message::setMessage('Článok nebol upravený.', 2);
                    if (mysql_errno()) {
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
                    }
                } else {
                    Message::setMessage('Článok bol úspešne upravený.', 0);
                    makeLog("Uprava clanku", $_POST[sk_name] . "<br>" . $_POST[sk_preview] . "<br>" . $_POST[sk_article]);
                    header("Location:index.php?module=article");
                    exit;
                }
            }

            if (is_numeric($_GET['article_id'])) {
                $queryString = "select * from " . TABLE_PREFIX . "article where 1 and article_id = '" . $_GET['article_id'] . "';";
                if (!$Result = mysql_query($queryString)) {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): "
                                . mysql_error() . "<br />");
                }else {
                    if (mysql_num_rows($Result) == 1)
                        $Row = mysql_fetch_assoc($Result);
                }
            }
            ?>
            <h1>Úprava článku</h1>
            <form method="post" enctype="multipart/form-data" action="">
                <table summary="" class="tableform" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                        <td colspan="3" class="pseudo-tabs">
                            <ul>
                                <li><span>Detail článku</span></li>
                                <li><a href="./index.php?module=article&amp;action=gallery&amp;article_id=<?= $_GET["article_id"]; ?>">Galéria článku</a></li>
                                <li><a href="./index.php?module=article&amp;action=gallery-order&amp;article_id=<?= $_GET["article_id"]; ?>">Zoradenie galérie článku</a></li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <?
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        ?>
                        <tr>
                            <td>Názov článku <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;"><?= $val; ?></sup></td>
                            <td>&nbsp;</td>
                            <td><input type="text" class="textbox1" id="name"  name="<?= strtolower($val); ?>_name" value="<?= htmlspecialchars($Row[strtolower($val) . '_name']); ?>" /></td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr>
                        <td>Kategória</td>
                        <td></td>
                        <td>
                            <select name="article_category_id" id="article_category_id" style="width: 256px;">
                                <option value="">zvoľte kategóriu</option>
                                <?
                                $queryString = "select article_category_id as id, sk_name from " . TABLE_PREFIX . "article_category where 1 order by sk_name;";

                                if ($ResultQ = mysql_query($queryString)) {
                                    while ($RowQ = mysql_fetch_assoc($ResultQ)) {
                                        print '
					                    <option value="' . $RowQ['id'] . '"' . (($Row['article_category_id'] == $RowQ['id']) ? ' selected="selected"' : '') . '>' . $RowQ['sk_name'] . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td></td>
                        <td><input name="publish" type="checkbox" id="publish"<?= (($Row['publish'] == 1) ? ' checked' : '') ?> />
                            článok je publikovaný</td>
                    </tr>
                    <tr class="hide-for-faq">
                        <td>&nbsp;</td>
                        <td></td>
                        <td><input name="default" type="checkbox" id="default"<?= (($Row['_default'] == 1) ? ' checked' : '') ?> />
                            článok sa zobrazuje na titulke</td>
                    </tr>
                    <tr class="hide-for-faq">
                        <td>Dátum pridania článku</td>
                        <td></td>
                        <td>
                            <input name="_date" type="text" class="datepicker" size="10" maxlength="10" readonly="readonly" value="<?= $Row['_date'] ?>" />
                            <img src="images/img.gif" alt="" name="_date_button" border="0" align="absmiddle" class="datepicker-button" />
                        </td>
                    </tr>
                    <?
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        ?>
                        <tr class="hide-for-faq">
                            <td valign="top">Perex<sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;"><?= $val; ?></sup></td>
                            <td></td>
                            <td>
                                <textarea class="ckeditor" name="<?= strtolower($val); ?>_preview"><?= html_entity_decode($Row[strtolower($val) . '_preview'], ENT_QUOTES, "UTF-8"); ?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td valign="top">Plné znenie článku<sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;"><?= $val; ?></sup></td>
                            <td></td>
                            <td>
                                <textarea class="ckeditor" name="<?= strtolower($val); ?>_article"><?= html_entity_decode($Row[strtolower($val) . '_article'], ENT_QUOTES, "UTF-8"); ?></textarea>
                            </td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr class="hide-for-faq">
                        <td colspan="3">
                            <? include("../popups/foto.php"); ?>
                            <? include("../popups/docs.php"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td align="left">
                            <button type="submit" class="button">Uložiť</button>
                        </td>
                    </tr>
                </table>
            </form>
            <script type="text/javascript">
                    
                $(document).ready(function() {

                    var faqCatId = 1;
                    $('#article_category_id').change(function() {
                        //console.log('this: ' + this.value);
                        if(this.value == faqCatId) {
                            $('.hide-for-faq').hide(200);
                        }
                        else {
                            $('.hide-for-faq').show(200);
                        }
                    });
                    if($("#article_category_id").val() == faqCatId) {
                        $('#article_category_id').prop('disabled', 'disabled').after('<input type="hidden" name="article_category_id" value="' + faqCatId + '" />');
                        $('.hide-for-faq').hide();
                    }
                    //console.log('sel: ' + $("#article_category_id").val());

                });

            </script>
            <?
            break;
        case "gallery":
            if (isset($_POST)) {
                $count = count($_FILES['image_file']['name']);

                require_once('../shared/classes/class.image.php');

                $thumbs_dimensions = explode(',', PRODUCT_THUMBS_DIMENSIONS);
                $preview_dimensions = explode(',', PREVIEW_DIMENSIONS);
                $original_dimensions = explode(',', ORIGINAL_DIMENSIONS);

                $img = new abeautifulsite\SimpleImage();
                $background = new abeautifulsite\SimpleImage(null, $thumbs_dimensions[0], $thumbs_dimensions[1], '#fff');

                // prechod a upload odoslanych priloh
                for ($i = 0; $i < $count; $i++) {
                    //print $_FILES['image_file']['name'][$i];
                    $img = $img->load($_FILES['image_file']['tmp_name'][$i]);
                    $u_image = $img->get_original_info();
                    if (is_array($u_image) AND ! empty($u_image)) { // main_image
                        $extension = '.' . pathinfo($_FILES['image_file']['name'][$i], PATHINFO_EXTENSION);
                        preg_match('/image/', $u_image['mime'], $type_match);
                        if ($type_match[0] == 'image') {
                            $name = sha1(time() . rand(0, 999)) . '-' . rand(0, 9999) . $extension;
                            try {
                                if ($img->get_height() >= $original_dimensions[1]) {
                                    $original = $img->fit_to_height($original_dimensions[1])->save('../photos/original/' . $name);
                                } elseif ($img->get_width() >= $original_dimensions[0]) {
                                    $original = $img->fit_to_width($original_dimensions[0])->save('../photos/original/' . $name);
                                } else {
                                    $original = $img->save('../photos/original/' . $name);
                                }
                                $thumbnail = $img->load($_FILES['image_file']['tmp_name'][$i])->best_fit($thumbs_dimensions[0], $thumbs_dimensions[1]);
                                $background->overlay($thumbnail)->save('../photos/thumbnail/' . $name);
                                $preview = $img->load($_FILES['image_file']['tmp_name'][$i])->best_fit($preview_dimensions[0], $preview_dimensions[1])->save('../photos/preview/' . $name);

                                //	vlozime zaznam do databazy
                                $queryString = "INSERT INTO " . TABLE_PREFIX . "photo_images
                                                    (photo_article_id, name, description, src, image_type, owner, date, sorter)
                                                VALUES
                                                    ('" . $_GET["article_id"] . "','" . $_POST["name"] . "', '" . $_POST["description"] . "', '" . $name . "', '" . $extension . "', '" . $_POST["owner"] . "', NOW(), '" . $_POST["sorter"] . "');";
                                mysql_query($queryString);
                            } catch (Exception $e) {
                                Message::setMessage('Chyba nahrávania obrázka: ', $e->getMessage(), 2);
                            }
                        }
                    } else {
                        Message::setMessage('Chyba nahrávania obrázka ', $_FILES['image_file']['name'], 2);
                    }
                }
            }
            // výber údajov z DB
            $query = 'SELECT sk_name AS name FROM ' . TABLE_PREFIX . 'article WHERE 1 AND article_id="' . $_GET['article_id'] . '";';
            $result = mysql_query($query);
            if (mysql_num_rows($result) != '0') {
                $row = mysql_fetch_assoc($result);
            } else {
                Message::setMessage('Chyba! Id článku nebolo nájdené!', 2);
                header('Location: index.php?module=article');
                exit;
            }
            if (isset($_GET['photo_images_id']) AND is_numeric($_GET['photo_images_id'])) {
                $query1 = 'SELECT name, description FROM ' . TABLE_PREFIX . 'photo_images WHERE 1 AND photo_images_id="' . $_GET['photo_images_id'] . '";';
                $result1 = mysql_query($query1);
                if (mysql_num_rows($result1) != '0') {
                    $row1 = mysql_fetch_assoc($result1);
                } else {
                    Message::setMessage('Chyba! Id obrázka nebolo nájdené!', 2);
                    header('Location: index.php?module=article');
                    exit;
                }
            }
            // výber údajov z DB END
            ?>
            <h1>Úprava článku</h1>
            <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform item-edit">
                <tr>
                    <td colspan="3" class="pseudo-tabs">
                        <ul>
                            <li><a href="./index.php?module=article&amp;action=update&amp;article_id=<?= $_GET["article_id"]; ?>">Detail článku</a></li>
                            <li><span>Galéria článku</span></li>
                            <li><a href="./index.php?module=article&amp;action=gallery-order&amp;article_id=<?= $_GET["article_id"]; ?>">Zoradenie galérie článku</a></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th colspan="3">&nbsp;</th>
                </tr>
                <tr>
                    <td colspan="3">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <form action="<?= (isset($_GET['photo_images_id']) ? './index.php?module=article&action=update-image&article_id=' . $_GET["article_id"] . '&photo_images_id=' . $_GET["photo_images_id"] : ''); ?>" method="post" enctype="multipart/form-data">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                            </tr>
                                            <?
                                            if (!isset($_GET['photo_images_id'])) {
                                                ?>
                                                <tr>
                                                    <td>Súbory:</td>
                                                    <td>
                                                        <input name="image_file[]" type="file" id="image_file" multiple="multiple" />
                                                        <button name="submit" type="submit">Nahrať</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">&nbsp;</td>
                                                </tr>
                                                <?
                                            } else {
                                                ?>
                                                <tr>
                                                    <td colspan="3">
                                                        <input name="photo_images_id" type="hidden" value="<?= $_GET['photo_images_id']; ?>" />
                                                        <button name="submit" type="submit">Upraviť</button>
                                                        <button onClick="parent.location = 'index.php?module=article&action=gallery&article_id=<?= $_GET['article_id']; ?>';
                                                                return false;">Návrat späť</button>
                                                    </td>
                                                </tr>
                                                <?
                                            }
                                            ?>
                                            <tr>
                                                <td>Názov:</td>
                                                <td><input type="text" name="name" id="name" style="width: 200px;" value="<?= $row1["name"] ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"><textarea id="ckeditor" class="ckeditor" name="description"><?= $row1['description'] ?></textarea></td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="product-images-gallery">
                                                        <?
                                                        $query = 'SELECT photo_images_id, src AS image_src, sorter
                                                                      FROM ' . TABLE_PREFIX . 'photo_images
                                                                      WHERE 1 AND photo_article_id = "' . $_GET['article_id'] . '"
                                                                      ORDER BY sorter ASC;';
                                                        $result = mysql_query($query);
                                                        if ($result) {
                                                            if (mysql_num_rows($result) > 0) {
                                                                while ($row = mysql_fetch_object($result)) {
                                                                    ?>
                                                                    <div>
                                                                        <a class="fancybox-image" rel="gallery" href="<?= ROOTDIR; ?>/photos/original/<?= $row->image_src; ?>">
                                                                            <img src="<?= ROOTDIR; ?>/photos/thumbnail/<?= $row->image_src; ?>" />
                                                                        </a>
                                                                        <span><?= reset(explode(".", $row->image_src)); ?></span>
                                                                        <a href="index.php?module=article&action=gallery&article_id=<?= $_GET['article_id']; ?>&photo_images_id=<?= $row->photo_images_id; ?>">[Upraviť]</a>
                                                                        <a href="javascript:confirmAction('Naozaj chcete odstrániť tento obrázok?', '', 'index.php?module=article&action=remove-image&image_src=<?= $row->image_src; ?>&article_id=<?= $_GET['article_id']; ?>');">[Odstrániť]</a>
                                                                    </div>
                                                                    <?
                                                                }
                                                            }
                                                        } else {
                                                            print mysql_error();
                                                        }
                                                        mysql_free_result($result);
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div class="clear"></div>
            <?
            break;
        case "gallery-order":
            if (isset($_POST) AND ! empty($_POST['sorter'])) {
                $sorter = explode(',', $_POST['sorter']);
                $status = array();
                foreach ($sorter as $key => $value) {
                    $query = mysql_query('UPDATE ' . TABLE_PREFIX . 'photo_images SET sorter="' . $key . '" WHERE photo_images_id="' . $value . '";');
                    if (!$query) {
                        $status[] = 'error';
                    }
                }
                if (empty($status)) {
                    Message::setMessage('Obrázky boli úspešne zoradené.', 0);
                } else {
                    Message::setMessage('Chyba! Obrázky neboli zoradené!', 2);
                }
                header('Location: index.php?module=article&action=gallery-order&article_id=' . $_GET['article_id']);
                exit;
            }
            // výber údajov z DB
            $query = 'SELECT sk_name AS name FROM ' . TABLE_PREFIX . 'article WHERE 1 AND article_id="' . $_GET['article_id'] . '";';
            $result = mysql_query($query);
            if (mysql_num_rows($result) != '0') {
                $row = mysql_fetch_assoc($result);
            } else {
                Message::setMessage('Chyba! Id článku nebolo nájdené!', 2);
                header('Location: index.php?module=article');
                exit;
            }
            if (isset($_GET['photo_images_id']) AND is_numeric($_GET['photo_images_id'])) {
                $query1 = 'SELECT name, description FROM ' . TABLE_PREFIX . 'photo_images WHERE 1 AND photo_images_id="' . $_GET['photo_images_id'] . '";';
                $result1 = mysql_query($query1);
                if (mysql_num_rows($result1) != '0') {
                    $row1 = mysql_fetch_assoc($result1);
                } else {
                    Message::setMessage('Chyba! Id obrázka nebolo nájdené!', 2);
                    header('Location: index.php?module=article');
                    exit;
                }
            }
            // výber údajov z DB END
            ?>
            <h1>Úprava článku</h1>
            <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform item-edit">
                <tr>
                    <td colspan="3" class="pseudo-tabs">
                        <ul>
                            <li><a href="./index.php?module=article&amp;action=update&amp;article_id=<?= $_GET["article_id"]; ?>">Detail článku</a></li>
                            <li><a href="./index.php?module=article&amp;action=gallery&amp;article_id=<?= $_GET["article_id"]; ?>">Galéria článku</a></li>
                            <li><span>Zoradenie galérie článku</span></li>
                        </ul>
                    </td>
                </tr>
                <tr>
                    <th colspan="3">&nbsp;</th>
                </tr>
                <tr>
                    <td colspan="3">
                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                            <tr>
                                <td>
                                    <form action="<?= (isset($_GET['photo_images_id']) ? './index.php?module=article&action=update-image&article_id=' . $_GET["article_id"] . '&photo_images_id=' . $_GET["photo_images_id"] : ''); ?>" method="post" enctype="multipart/form-data">
                                        <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                            <tr>
                                                <td colspan="3">
                                                    <input id="sorter" name="sorter" type="hidden" />
                                                    <button name="submit" type="submit">Zoradiť</button>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td colspan="3">
                                                    <div class="product-images-gallery sortable">
                                                        <?
                                                        $query = 'SELECT photo_images_id, src AS image_src, sorter
                                                                      FROM ' . TABLE_PREFIX . 'photo_images
                                                                      WHERE 1 AND photo_article_id = "' . $_GET['article_id'] . '"
                                                                      ORDER BY sorter ASC;';
                                                        $result = mysql_query($query);
                                                        if ($result) {
                                                            if (mysql_num_rows($result) > 0) {
                                                                while ($row = mysql_fetch_object($result)) {
                                                                    ?>
                                                                    <div class="ui-state-highlight" id="<?= $row->photo_images_id; ?>">
                                                                        <img src="<?= ROOTDIR; ?>/photos/thumbnail/<?= $row->image_src; ?>" />
                                                                    </div>
                                                                    <?
                                                                }
                                                            }
                                                        } else {
                                                            print mysql_error();
                                                        }
                                                        mysql_free_result($result);
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </form>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div class="clear"></div>
            <?
            break;
        case "update-image":
            if (is_numeric($_POST['photo_images_id'])) {

                $query = 'UPDATE ' . TABLE_PREFIX . 'photo_images
                                SET name="' . $_POST["name"] . '", description="' . $_POST["description"] . '"
                                WHERE photo_images_id="' . $_POST["photo_images_id"] . '"
                                LIMIT 1;';
                if (!$result = mysql_query($query)) {
                    Message::setMessage('Chyba! Obrázok nebol upravený!', 2);
                } else {
                    Message::setMessage('Obrázok bol úspešne upravený!', 0);
                }
            }
            header('Location: index.php?module=article&action=gallery&article_id=' . $_GET['article_id']);
            exit;
            break;
        case"remove-image":
            @unlink('../photos/thumbnail/' . $_GET['image_src']);
            @unlink('../photos/preview/' . $_GET['image_src']);
            @unlink('../photos/original/' . $_GET['image_src']);

            $queryString = 'DELETE FROM ' . TABLE_PREFIX . 'photo_images WHERE src="' . $_GET['image_src'] . '";';
            Message::setMessage('Obrázok bol úspešne odstránený.', 0);
            mysql_query($queryString);
            header('Location: index.php?module=article&action=gallery&article_id=' . $_GET['article_id']);
            exit;
            break;
        case "delete":
            $queryString = "delete from " . TABLE_PREFIX . "article where 1 and article_id = '" . $_GET['article_id'] . "';";
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno())
                    print("MySql Error (" . mysql_errno() . "): "
                            . mysql_error() . "<br />");
            }else {
                header("Location:index.php?module=article");
                exit;
            }

            break;
        default:
            ?>
            <h1>Zoznam článkov</h1>
            <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablelist item-list">
                <tr>
                    <th>&nbsp;</th>
                    <th>Názov článku</th>
                    <th>Dátum pridania</th>
                    <th>Kategória</th>
                    <th>Publikovaný</th>
                    <th>Na titulke</th>
                    <th>&nbsp;</th>
                </tr>
                <?
                $queryString = "select a._checked, if(a._default=1, 'áno', 'nie') as titulka, a.article_id, a.sk_name as sk_name, date_format(a._date, '%d.%m.%Y') as a_date, ac.sk_name as category, ac.article_category_id as article_category_id, if(u.username is null, 'anonýmny užívateľ', u.username) as username, if(a.publish = 1, 'áno', 'nie') as published from " . TABLE_PREFIX . "article as a left join " . TABLE_PREFIX . "article_category as ac using (article_category_id) left join " . TABLE_PREFIX . "user as u using (user_id) where 1 order by sorter,_checked desc, _date desc;";
                //echo $queryString;
                if ($Result = mysql_query($queryString)) {
                    $_parny = false;
                    while ($Row = mysql_fetch_assoc($Result)) {
                        ?>
                        <tr class="<?= ((/* $Row['_checked']=='0' */1 == 2) ? 'style4' : ((!$_parny) ? 'style1' : 'style2')); ?>">
                            <td></td>
                            <td><a href="./index.php?module=article&amp;action=update&amp;article_id=<?= $Row['article_id']; ?>"><?= $Row['sk_name']; ?></a></td>
                            <td><?= $Row['a_date']; ?></td>
                            <td><?= $Row['category']; ?></td>
                            <td><?= $Row['published']; ?></td>
                            <td><?= $Row['titulka']; ?></td>
                            <td class="actions">
                                <a href="javascript:;" onclick="javascript:sortItemB(<?= $Row['article_category_id']; ?>);">Poradie</a>
                                <a href="./index.php?module=article&amp;action=update&amp;article_id=<?= $Row['article_id']; ?>">Editovať</a>
                                <a href="javascript:;" onclick="javascript:ConfirmBoxAc('Naozaj si želáte odstrániť tento článok?', './index.php?module=article&amp;action=delete&amp;article_id=<?= $Row['article_id']; ?>', '');">Zmazať</a>
                            </td>
                        </tr>
                        <?
                        $_parny = !$_parny;
                    }
                }
                ?>
                <tr>
                    <th colspan="8">&nbsp;</th>
                </tr>


            </table>
        <?
    }
    ?>
</div>
<script   type="text/javascript">
    CKEDITOR.replace('ckeditor', {
        customConfig: 'config-basic.js'
    });
</script>