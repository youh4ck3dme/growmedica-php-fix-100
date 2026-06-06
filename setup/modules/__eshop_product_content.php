<div id="leftMenu">
    <h2>Produkty</h2>
    <p>V tejto sekcii môžte spravovať všetky vaše produkty.</p>
    <div id="submenu">
        <a href="./index.php?module=eshop_product_content&amp;eshop=1&amp;category_id=<?= $_GET['category_id'] ?> " class="addNew">Pridať nový<br />  produkt</a>
        <a href="./index.php?module=eshop_product&amp;eshop=1&amp;display=all" id="detail">Zobraziť všetky <br /> produkty</a>
        <a href="./index.php?module=eshop_product&amp;eshop=1&amp;display=special" id="detail">Zobraziť akciové <br /> produkty</a>
    </div>
</div>

<div id="moduleContent">
    <?php
    if (!$user->isAdmin()) {
        exit;
    }

    $obj_categories = new Categories;
    $obj_categories->find_subcategories(ESHOP_MAIN_CATEGORY);
    $navmenu = $obj_categories->get_categories();
    ?>
    <script type="text/javascript">
        function confirmAction(message, abort_action, ok_action) {
            var msg = confirm(message);
            if (!msg) {
                if (abort_action == '') {
                    //return false;
                    this.location;
                } else {
                    this.location = abort_action;
                }
            } else {
                document.location.href = ok_action;
            }
        }
        function openWinColors(product_id) {
            newWin = window.open('<?= ROOTDIR ?>/setup/modules/_eshop_product_colors.php?product_id=' + product_id, 'productModels', 'width=600,height=500');
            newWin.focus();
        }

        function openWinModels(product_id) {
            newWin = window.open('<?= ROOTDIR ?>/setup/modules/_eshop_product_models.php?product_id=' + product_id, 'productModels', 'width=600,height=500');
            newWin.focus();
        }
    </script>
    <?php
    /*  funkcie pre posielanie suboru - start */

    function findExtension($str) {
        $c = 0;
        $ex = "";
        $ext = array();
        for ($x = strlen($str); $x >= 0; $x--) {

            if ($str[$x] == ".")
                break;
            else {
                $ext[$c] = $str[$x];
                $c++;
            }
        }
        $ext = array_reverse($ext);
        foreach ($ext as $ch)
            $ex.=$ch;
        return $ex;
    }

    function ISO88592bezDiakritiky($buf) {
        $ISO_8859 = array(225, 232, 239, 233, 237, 229, 242, 243, 185, 187, 250, 193, 200, 207, 201, 205, 197, 210, 211, 169, 171, 218, 190, 174, 181, 253);
        $Bez_diak = array(97, 99, 100, 101, 105, 108, 110, 111, 115, 116, 117, 97, 99, 100, 101, 105, 108, 110, 111, 115, 116, 117, 122, 122, 108, 121);
        $Windows_1250 = array(225, 232, 239, 233, 237, 229, 242, 243, 154, 157, 250, 193, 200, 207, 201, 205, 197, 210, 211, 138, 141, 218, 158, 142, 190);

        for ($x = 0; $x < strlen($buf); $x++) {
            for ($c = 0; $c < count($ISO_8859); $c++) {
                if ($ISO_8859[$c] == ord($buf[$x])) {
                    $buf[$x] = chr($Bez_diak[$c]);
                }
            }
        }
        return substr($buf, 0, 255);
    }

    if (empty($docs_category_id)) {
        if ($result = mysql_query("select min(docs_category_id) as docs_category_id from " . TABLE_PREFIX . "_docs_category")) {
            if (mysql_num_rows($result) == 1) {
                $row = mysql_fetch_object($result);
                $docs_category_id = $row->docs_category_id;
                mysql_free_result($result);
            }
        } else {
            if (mysql_errno())
                print mysql_error();
        }
    }

    if (!empty($docs_category_id)) {
        $sql = "select category_name from " . TABLE_PREFIX . "_docs_category where docs_category_id = '$docs_category_id'";
        if ($result = mysql_query($sql)) {
            if (mysql_num_rows($result) == 1) {
                $row = mysql_fetch_object($result);
                $category_name = $row->category_name;
                mysql_free_result($result);
            }
        } else {
            if (mysql_errno())
                print mysql_error();
        }
    }else {
        $category_name = "Nie je vytvorená žiadna skupina. Začnite vytvorením skupiny v ľavej časti okna";
    }
    /*  funkcie pre posielanie suboru - end */

    if (!isset($_GET["product_id"]) or ! is_numeric($_GET["product_id"]) or isset($_POST["BtnSaveAsNew"])) { //image_prenos
        if (isset($_POST["sk_name"])) {

            //	zmenime  udaje   v   databaze
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                $queryInclude1 .= "`" . strtolower($val) . "_name`, `" . strtolower($val) . "_name_seo`, `" . strtolower($val) . "_description`, `" . strtolower($val) . "_keywords`,";

                $queryInclude2 .= "'" . $_POST[strtolower($val) . '_name'] . "', '" . String::SEOFriendlyText($_POST[strtolower($val) . '_name']) . "', '" .
                        str_replace(array('"content/', '"../../../content/', '"../../../../content/'), '"' . ROOTDIR . '/content/', html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8")) . "', '" . $_POST[strtolower($val) . '_keywords'] . "',";
            }

            $queryString = "insert into " . TABLE_PREFIX . "product
				(
					`date`,
					" . $queryInclude1 . "
					price,
					price_old,
					manufacturer_id,
					available,
					skladom,
					code_1,
					file_name,
					recommend
				)
				values (
					now(),
					" . $queryInclude2 . "
					'" . str_replace(",", ".", (string) $_POST["price"]) . "',
					'" . str_replace(",", ".", (string) $_POST["price_old"]) . "',
					" . ((is_numeric($_POST["manufacturer_id"])) ? $_POST["manufacturer_id"] : 'null') . ",
					'" . $_POST["available"] . "',
					'" . $_POST["skladom"] . "',
					'" . $_POST["code_1"] . "',
					'" . $filename_bezdkr . "',
					'" . ((isset($_POST['recommend'])) ? 1 : 0) . "'
				);";
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno())
                    print("MySql Error (" . mysql_errno() . "): "
                            . mysql_error());
            }else {
                $product_id = mysql_insert_id();
                if (is_array($_POST["category_id"]) and sizeof($_POST["category_id"]) > 0) {
                    foreach ($_POST["category_id"] as $item => $value) {
                        $queryString = "insert into " . TABLE_PREFIX . "product_menu (menu_id, product_id) values ('" . $value . "', '" . $product_id . "');";
                        if (!$ResultB = mysql_query($queryString)) {
                            if (mysql_errno())
                                print("MySql Error (" . mysql_errno() . "): "
                                        . mysql_error());
                        }
                    }
                }else {
                    $_POST["category_id"][] = $_GET["category_id"];
                    foreach ($_POST["category_id"] as $item => $value) {
                        $queryString = "insert into " . TABLE_PREFIX . "product_menu (menu_id, product_id) values ('" . $value . "', '" . $product_id . "');";
                        if (!$ResultB = mysql_query($queryString)) {
                            if (mysql_errno())
                                print("MySql Error (" . mysql_errno() . "): "
                                        . mysql_error());
                        }
                    }
                }
                // posielanie uni farby
                $queryString = "insert into " . TABLE_PREFIX . "product_color (product_id,univerzal) values ('" . $product_id . "','1');";
                if (!$ResultB = mysql_query($queryString)) {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): "
                                . mysql_error());
                }
                $product_color_id = mysql_insert_id();

                // posielanie uni velkosti
                $queryString = "insert into " . TABLE_PREFIX . "product_type (product_id,product_color_id,name,univerzal) values ('" . $product_id . "','" . $product_color_id . "','univerzálny','1');";
                if (!$ResultB = mysql_query($queryString)) {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): "
                                . mysql_error());
                }

                // posielanie foto produktu
                if (isset($_FILES['image_preview']) and $_FILES['image_preview']['name'] <> '') {


                    require_once('../shared/classes/class.image.php');

                    $thumbs_dimensions = explode(',', THUMBS_DIMENSIONS);
                    $preview_dimensions = explode(',', PREVIEW_DIMENSIONS);
                    $original_dimensions = explode(',', ORIGINAL_DIMENSIONS);

                    $img = new abeautifulsite\SimpleImage($_FILES['image_preview']['tmp_name']);
                    $u_image = $img->get_original_info();
                    if (is_array($u_image) AND ! empty($u_image)) { // main_image
                        $extension = '.' . pathinfo($_FILES['image_preview']['name'], PATHINFO_EXTENSION);
                        preg_match('/image/', $u_image['mime'], $type_match);
                        if ($type_match[0] == 'image') {
                            $name = sha1(time() . rand(0, 999)) . $extension;
                            try {
                                if ($img->get_height() >= $original_dimensions[1]) {
                                    $original = $img->fit_to_height($original_dimensions[1])->save('../photos/original/' . $name);
                                } elseif ($img->get_width() >= $original_dimensions[0]) {
                                    $original = $img->fit_to_width($original_dimensions[0])->save('../photos/original/' . $name);
                                } else {
                                    $original = $img->save('../photos/original/' . $name);
                                }
                                $thumbnail = $img->best_fit($thumbs_dimensions[0], $thumbs_dimensions[1])->save('../photos/thumbnail/' . $name);
                                $preview = $img->best_fit($preview_dimensions[0], $preview_dimensions[1])->save('../photos/preview/' . $name);

                                $queryString = "UPDATE " . TABLE_PREFIX . "product SET image_src='" . $name . "' WHERE 1 AND product_id='" . $product_id . "';";
                                mysql_query($queryString);
                            } catch (Exception $e) {
                                Message::setMessage('Chyba nahrávania obrázka: ', $e->getMessage(), 2);
                            }
                        }
                    } else {
                        Message::setMessage('Chyba nahrávania obrázka ', $_FILES['image_preview']['name'], 2);
                    }
                } else {
                    if (isset($_POST["BtnSaveAsNew"])) {
                        $image_prenos = $_POST["image_prenos"];
                        $queryString = "update " . TABLE_PREFIX . "product set image_src = '" . $image_prenos . "' where 1 and product_id = '" . $product_id . "';";
                        $ResultC = mysql_query($queryString);
                    }
                }

                // vytvorenie fotogalerie zariadenia
                $sql_strFotogaleria = "INSERT INTO `" . TABLE_PREFIX . "_photo_category` (photo_category_id, name) VALUES	('" . product_id . "','" . $_POST["sk_name"] . "');";
                $resultFotogaleria = @mysql_query($sql_strFotogaleria);
                if ($resultFotogaleria != 0) {

                } else
                    print mysql_error();
                @mysql_free_result($resultFotogaleria);

                header("Location:./index.php?module=eshop_product_content&eshop=1&sprava_error=2&product_id=" . $product_id . "&category_id=" . ((sizeof($_POST["category_id"]) > 0) ? $_POST["category_id"][0] : $_GET["category_id"]));
                exit;
            }
        }
    }else {
        //	Upravime existujuci produkt
        if (isset($_POST["sk_name"])) {
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                $queryInclude .= "`" . strtolower($val) . "_description` = '" . str_replace(array('"content/', '"../../../content/', '"../../../../content/'), '"' . ROOTDIR . '/content/', str_replace('\'', '&#039;', html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8"))) . "', ";
                $queryInclude .= "`" . strtolower($val) . "_name` = '" . str_replace("'", '&#39;', str_replace('"', '&quot;', html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8"))) . "', ";
                $queryInclude .= "`" . strtolower($val) . "_name_seo` = '" . mysql_real_escape_string(String::SEOFriendlyText($_POST[strtolower($val) . '_name'])) . "', ";
                $queryInclude .= "`" . strtolower($val) . "_keywords` = '" . html_entity_decode($_POST[strtolower($val) . "_keywords"], ENT_QUOTES, "UTF-8") . "', ";
            }

            $queryString = "update " . TABLE_PREFIX . "product set
			" . $queryInclude . "
			price = '" . str_replace(",", ".", (string) $_POST["price"]) . "',
			price_old = '" . str_replace(",", ".", (string) $_POST["price_old"]) . "',
			manufacturer_id = " . ((is_numeric($_POST["manufacturer_id"])) ? $_POST["manufacturer_id"] : "null") . ",
			available = '" . (($_POST["available"] == 1) ? 1 : 0) . "',
			skladom = '" . (($_POST["skladom"] == 1) ? 1 : 0) . "',
			code_1 = '" . $_POST["code_1"] . "',
			recommend = '" . (($_POST["recommend"] == 1) ? 1 : 0) . "'
			where 1 and product_id = '" . $_GET["product_id"] . "';";
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno())
                    print("MySql Error (" . mysql_errno() . "): "
                            . mysql_error());
            }else {

                $queryString = "delete from " . TABLE_PREFIX . "product_menu where 1 and product_id = '" . $_GET["product_id"] . "';";
                if (!$ResultC = mysql_query($queryString)) {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): "
                                . mysql_error());
                }else {
                    if (is_array($_POST["category_id"]) and sizeof($_POST["category_id"]) > 0) {
                        foreach ($_POST["category_id"] as $item => $value) {
                            $queryString = "insert into " . TABLE_PREFIX . "product_menu (menu_id, product_id) values ('" . $value . "', '" . $_GET["product_id"] . "');";
                            if (!$ResultB = mysql_query($queryString)) {
                                if (mysql_errno())
                                    print("MySql Error (" . mysql_errno() . "): "
                                            . mysql_error());
                            }
                        }
                    }else {
                        $_POST["category_id"][] = $_GET["category_id"];
                        foreach ($_POST["category_id"] as $item => $value) {
                            $queryString = "insert into " . TABLE_PREFIX . "product_menu (menu_id, product_id) values ('" . $value . "', '" . $_GET["product_id"] . "');";
                            if (!$ResultB = mysql_query($queryString)) {
                                if (mysql_errno())
                                    print("MySql Error (" . mysql_errno() . "): "
                                            . mysql_error());
                            }
                        }
                    }
                }

                // posielanie foto produktu
                if (isset($_FILES['image_preview']) and $_FILES['image_preview']['name'] <> '') {

                    require_once('../shared/classes/class.image.php');

                    $thumbs_dimensions = explode(',', THUMBS_DIMENSIONS);
                    $preview_dimensions = explode(',', PREVIEW_DIMENSIONS);
                    $original_dimensions = explode(',', ORIGINAL_DIMENSIONS);

                    $img = new abeautifulsite\SimpleImage($_FILES['image_preview']['tmp_name']);
                    $u_image = $img->get_original_info();
                    if (is_array($u_image) AND ! empty($u_image)) { // main_image
                        $extension = '.' . pathinfo($_FILES['image_preview']['name'], PATHINFO_EXTENSION);
                        preg_match('/image/', $u_image['mime'], $type_match);
                        if ($type_match[0] == 'image') {
                            $name = sha1(time() . rand(0, 999)) . $extension;
                            try {
                                if ($img->get_height() >= $original_dimensions[1]) {
                                    $original = $img->fit_to_height($original_dimensions[1])->save('../photos/original/' . $name);
                                } elseif ($img->get_width() >= $original_dimensions[0]) {
                                    $original = $img->fit_to_width($original_dimensions[0])->save('../photos/original/' . $name);
                                } else {
                                    $original = $img->save('../photos/original/' . $name);
                                }
                                $thumbnail = $img->best_fit($thumbs_dimensions[0], $thumbs_dimensions[1])->save('../photos/thumbnail/' . $name);
                                $preview = $img->best_fit($preview_dimensions[0], $preview_dimensions[1])->save('../photos/preview/' . $name);

                                $queryString = "UPDATE " . TABLE_PREFIX . "product SET image_src='" . $name . "' WHERE 1 AND product_id='" . $_GET["product_id"] . "';";
                                mysql_query($queryString);
                            } catch (Exception $e) {
                                Message::setMessage('Chyba nahrávania obrázka: ', $e->getMessage(), 2);
                            }
                        }
                    } else {
                        Message::setMessage('Chyba nahrávania obrázka ', $_FILES['image_preview']['name'], 2);
                    }
                }

                // delete fotografia
                if ($_POST['delete_foto'] == 1) {
                    $queryString_delete_foto = "update " . TABLE_PREFIX . "product set image_src = '0' where 1 and product_id = '" . $_GET["product_id"] . "';";
                    if (!$Result_delete_foto = mysql_query($queryString_delete_foto)) {
                        if (mysql_errno())
                            print("MySql Error (" . mysql_errno() . "): "
                                    . mysql_error());
                    }
                }

                header("Location:./index.php?module=eshop_product_content&eshop=1&sprava_error=1&product_id=" . $_GET["product_id"] . "&category_id=" . ((sizeof($_POST["category_id"]) > 0) ? $_POST["category_id"][0] : $_GET["category_id"]));
                exit;
            }
        }
    }

    if (is_numeric($_GET['product_id'])) {
        $queryString = "select p.* from " . TABLE_PREFIX . "product as p where 1 and p.product_id = '" . addslashes($_GET['product_id']) . "' limit 3;";
        if ($Result = mysql_query($queryString)) {
            if (mysql_num_rows($Result) == 1)
                $_POST = mysql_fetch_assoc($Result);

            // zistime si zoznam kategorii v ktorych sa produkt nachadza
            $queryString = "select pm.menu_id from " . TABLE_PREFIX . "product_menu as pm where 1 and pm.product_id = '" . $_GET["product_id"] . "';";
            if ($Result = mysql_query($queryString)) {
                if (mysql_num_rows($Result) > 0) {
                    $product = array();
                    while ($Row = mysql_fetch_assoc($Result)) {
                        $product[] = $Row["menu_id"];
                    }
                }
                $_POST["category_id"] = $product;
            }
        } else {
            if (mysql_errno())
                print("MySql Error (" . mysql_errno() . "): "
                        . mysql_error());
        }
    }
    ?>

    <? if ($_GET['sprava_error'] == 1) { ?><div style="width:50%; color:#FF0000; font-size:16px; font-weight:bold; text-align:center">Výrobok bol upravený !</div> <? } ?>
    <? if ($_GET['sprava_error'] == 2) { ?><div style="width:50%; color:#FF0000; font-size:16px; font-weight:bold; text-align:center">Výrobok bol pridaný !</div> <? } ?>
    <form method="post" action="" name="f0054" id="f0054" enctype="multipart/form-data" >
        <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">
            <tr>
                <th colspan="3">Pridanie produktu</th>
            </tr>
            <tr>
                <td>
                    <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">
                        <tr>
                            <td width="364" valign="top">
                                <table border="0" cellpadding="2" cellspacing="2" summary="" class="tableProduct_left">
                                    <?
                                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                        print '
										<tr>
											<td>Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>
											<td><input type="text" style="width: 300px;" id="name"  name="' . strtolower($val) . '_name" value="' . str_replace('"', '&quot;', $_POST[strtolower($val) . '_name']) . '" /></td>
										</tr>';
                                    }
                                    ?>
                                    <tr>
                                        <td>Odporúčame </td>
                                        <td><input name="recommend" type="checkbox" class="w201px" id="code_1" value="1" <?php print(($_POST['recommend'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                    </tr>
                                    <tr>
                                        <td>Kód produktu </td>
                                        <td><input name="code_1" type="text" style="width: 300px;" id="code_1" value="<?= $_POST["code_1"] ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td>Aktuálna cena produktu </td>
                                        <td><input name="price" type="text" style="width: 300px;" id="price" value="<?= $_POST["price"] ?>" /></td>
                                    </tr>
                                    <tr>
                                        <td>Pôvodná cena produktu </td>
                                        <td><input name="price_old" type="text" style="width: 300px;" id="price_old" value="<?= $_POST["price_old"] ?>" /></td>
                                    </tr>


                                    <?
                                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                        print '<tr><td>Keywords<sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>';
                                        print '<td><textarea name="' . $val . '_keywords" style="width: 300px; height: 60px;">' . $_POST[$val . '_keywords'] . '</textarea></td></tr>';
                                    }
                                    ?>




                                    <tr>
                                        <td valign="top">Zaradenie v kategóriách</td>
                                        <td><select name="category_id[]" size="10" multiple="multiple" style="min-height:60px;" id="category_id">
                                                <?
                                                $menus_array = array();
                                                if (isset($_REQUEST['product_id'])) {
                                                    $query_menus = 'SELECT menu_id FROM ' . TABLE_PREFIX . 'product_menu WHERE product_id = ' . $_REQUEST['product_id'];
                                                    echo $query_menus;
                                                    if ($result_menus = mysql_query($query_menus)) {
                                                        while ($row_menus = mysql_fetch_object($result_menus)) {
                                                            $menus_array[] = $row_menus->menu_id;
                                                        }
                                                    }
                                                }

                                                foreach ($navmenu as $row) {
                                                    echo '<option value="' . $row['id'] . '" ' . ((in_array($row['id'], $menus_array) ? 'selected="selected"' : '')) . '>' . $row['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Výrobca</td>
                                        <td><select name="manufacturer_id" class="w201px" id="manufacturer_id">
                                                <option value=""></option>
                                                <?
                                                $query_combo = 'SELECT manufacturer_id, sk_name FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 ORDER BY sk_name ASC';
                                                if ($result_combo = mysql_query($query_combo)) {
                                                    while ($row_combo = mysql_fetch_object($result_combo)) {
                                                        echo '<option value="' . $row_combo->manufacturer_id . '" ' . (($row_combo->manufacturer_id == $_POST["manufacturer_id"]) ? 'selected="selected"' : '') . '>' . $row_combo->sk_name . '</option>';
                                                    }
                                                }
                                                ?>
                                                <?php //create_combo("select manufacturer_id as id, sk_name as name from " . TABLE_PREFIX . "manufacturer where 1 order by name;", $_POST["manufacturer_id"]); ?>
                                            </select>										</td>
                                    </tr>

                                    <tr>
                                        <td>Produkt je možné kúpiť</td>
                                        <td>
                                            <select name="available" id="available">
                                                <option value="1"<?= ((isset($_POST["available"])) and ( $_POST["available"] == 1) or ( !isset($_POST["available"]))) ? ' selected="selected"' : '' ?>>áno</option>
                                                <option value="0"<?= ((isset($_POST["available"])) and ( $_POST["available"] == 0)) ? ' selected="selected"' : '' ?>>nie</option>
                                            </select></td>
                                    </tr>

                                    <tr>
                                        <td>Fotka:</td>
                                        <td><input name="image_preview" type="file" id="image_preview" /><br /></td>
                                    </tr>
                                    <? if (is_numeric($_GET['product_id'])) {
                                        ?><tr>
                                            <td colspan="2">
                                                Farby, v ktorých sa produkt ponúka<br />
                                                <a href="javascript:;" onclick="javascript:openWinColors('<?= $_GET["product_id"] ?>');">EDITOVAŤ ZOZNAM FARIEB A VELKOSTI</a>
                                                <br/>
                                                <a href="index.php?module=eshop_product_color_add&eshop=1" target="_blank">EDITOVAŤ DATABAZU FARIEB</a>
                                                <div>
                                                    <table summary="" border="0" cellpadding="3" cellspacing="3" style="border: solid 1px #eeeeee;">
                                                        <tr style="background-color: #eeeeee; font-weight: bold;">
                                                            <td>Názov farby </td>
                                                            <td>Názov velkosti</td>
                                                            <td>Img</td>
                                                        </tr>
                                                        <?php
                                                        $queryString = "SELECT * FROM " . TABLE_PREFIX . "product_color JOIN " . TABLE_PREFIX . "color USING(color_id) WHERE 1 AND product_id = '" . $_GET["product_id"] . "' and univerzal = '0';";
                                                        if (!$Result = mysql_query($queryString)) {
                                                            print 'Chyba vyberu farieb z DB .';
                                                        } else {
                                                            while ($Row = mysql_fetch_assoc($Result)) {
                                                                print '<tr>
																		<td style="background-color: #' . $Row['color_hex'] . '; text-align:center">' . $Row['name'] . '</td>
																		<td>';
                                                                $queryStringVelkosti = "SELECT * FROM " . TABLE_PREFIX . "product_type WHERE 1 AND product_color_id = '" . $Row['product_color_id'] . "' and univerzal = '0';";
                                                                if (!$ResultVelkosti = mysql_query($queryStringVelkosti)) {
                                                                    print 'Chyba vyberu veľkostí z DB .';
                                                                } else {
                                                                    while ($RowVelkosti = mysql_fetch_assoc($ResultVelkosti)) {
                                                                        print $RowVelkosti['name'] . ', ';
                                                                    }
                                                                }
                                                                print '	<td>';
                                                                if (!empty($Row['src']))
                                                                    print '<a href="../photos/images/' . $Row['src'] . '" rel="thickbox" class="thickbox">img</a>';
                                                                print '</td>
																	 </tr>';
                                                            }
                                                        }
                                                        ?>
                                                    </table>  								<table border="0" cellpadding="2" cellspacing="2" summary="" class="tableProduct">
                                                        <tr>
                                                            <td height="148" valign="top" style="width: 141px;">Foto</td>
                                                            <td valign="top">
                                                                <? if (is_file("../photos/thumbnail/" . $_POST['image_src'])) { ?>
                                                                    <a href="../photos/preview/<?= $_POST['image_src']; ?>" rel="thickbox" class="thickbox" title="<?= $_POST['image_src'] ?>" border="0">
                                                                        <img src="../photos/thumbnail/<?= $_POST['image_src']; ?>" alt="<?= $_POST['image_src']; ?>" border="0" /></a><br />
                                                                    Odstrániť fotografiu : <input name="delete_foto" type="checkbox" value="1" />	<?
                                                                    $image_prenos = $_POST['image_src'];
                                                                } else {
                                                                    print 'K výrobku nie je priradená žiadna fotografia.';
                                                                }
                                                                ?>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </td>
                                        </tr>
                                    <? } else {
                                        ?>
                                        <tr><td colspan="2"><div style="color: #FF0000; font-weight: bold; text-align: center; padding: 5px 0 5px 0; line-height: 18px; border: solid 1px #FF99FF; background:#FFCCFF;">VLASTNOSTI JE MOŽNÉ PRIDÁVAŤ AŽ PO PRIDANÍ PRODUKTU</div></td></tr>
                                    <? } ?>
                                </table>
                            </td>
                            <td width="21">&nbsp;</td>
                            <td width="400" valign="top">
                                <? foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                    ?>
                                    <br />Charakteristika produktu <sup><strong>[<?= strtolower($val) ?>]</strong></sup><br /><br />
                                    <textarea class="ckeditor" name="<?= strtolower($val); ?>_description"><?= html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8"); ?></textarea>
                                <? } ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td style="margin: 2px; padding: 2px;">
                </td>
            </tr>
            <tr>
                <td align="right">
                    <input name="image_prenos" type="hidden" value="<?= $image_prenos ?>" />
                    <input type="submit" value="<?= ((is_numeric($_GET['product_id'])) ? "Upraviť produkt" : "Pridať produkt") ?>" onclick="javascript:document.forms['f0054'].submit();" />
                    <input type="submit" value="Pridať ako nový" name="BtnSaveAsNew" id="BtnSaveAsNew" />
                    <input type="button" value="Zobraziť zoznam produktov" onclick="javascript:document.location.href = './index.php?module=eshop_product&eshop=1&category_id=<?= $_GET["category_id"] ?>';" />
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
            </tr>
            <tr><th colspan="4">&nbsp;</th></tr>
        </table>
    </form>
</div>