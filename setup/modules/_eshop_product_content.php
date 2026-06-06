<?
if (!$user->isAdmin()) {
    exit;
}
?>
<div id="leftMenu">
    <? include('_eshop-left-menu.php'); ?>
    <div id="submenu">
        <a href="./index.php?module=eshop_product_content<?= ($_GET['category_id'] != "" ? '&amp;category_id=' . $_GET['category_id'] : '') ?>" class="addNew">Pridať nový<br />  produkt</a>
        <a href="./index.php?module=eshop_product" class="detail">Zobraziť všetky <br /> produkty</a>
        <a href="./index.php?module=eshop_product&amp;display=special" class="detail">Zobraziť akciové <br /> produkty</a>
    </div>
</div>

<div id="moduleContent">
    <?php
    $obj_categories = new Categories;
    $obj_categories->find_subcategories(ESHOP_MAIN_CATEGORY);
    ?>
    <?
    switch ($_GET['action']) {
        case "update":
            if ($_POST AND ! empty($_POST["sk_name"])) {

                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude .= "`" . strtolower($val) . "_description` = '" . str_replace(array('"content/', '"../../../content/', '"../../../../content/'), '"' . ROOTDIR . '/content/', str_replace('\'', '&#039;', html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8"))) . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_name` = '" . str_replace("'", '&#39;', str_replace('"', '&quot;', html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8"))) . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_name_seo` = '" . mysql_real_escape_string(String::SEOFriendlyText($_POST[strtolower($val) . '_name'])) . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_keywords` = '" . html_entity_decode($_POST[strtolower($val) . "_keywords"], ENT_QUOTES, "UTF-8") . "', ";
                }

                $queryString = "UPDATE " . TABLE_PREFIX . "product SET
                                " . $queryInclude . "
                                price = '" . (($_POST["percent_off"] AND $_POST["percent_off"] > 0) ? preparePrice($_POST["price_new"]) : preparePrice($_POST["price"])) . "',
                                price_old = '" . (($_POST["percent_off"] AND $_POST["percent_off"] > 0) ? preparePrice($_POST["price"]) : preparePrice($_POST["price_new"])) . "',
                                manufacturer_id = " . ((is_numeric($_POST["manufacturer_id"])) ? $_POST["manufacturer_id"] : "null") . ",
                                available = '" . (($_POST["available"] == 1) ? 1 : 0) . "',
                                skladom = '" . (($_POST["skladom"] == 1) ? 1 : 0) . "',
                                delivery_time = '" . (($_POST["delivery_time"] == 1) ? 1 : 0) . "',
                                code_1 = '" . $_POST["code_1"] . "',
                                code_ean = '" . $_POST["code_ean"] . "',
                                code_suplier = '" . $_POST["code_suplier"] . "',
                                suplier_id = '" . $_POST["suplier_id"] . "',
                                recommend = '" . (($_POST["recommend"] == 1) ? 1 : 0) . "',
                                action = '" . (($_POST["action"] == 1) ? 1 : 0) . "',
                                novelty = '" . (($_POST["novelty"] == 1) ? 1 : 0) . "',
                                sale = '" . (($_POST["sale"] == 1) ? 1 : 0) . "'
                            WHERE 1 AND product_id = '" . $_GET["product_id"] . "';";
                if (!$Result = mysql_query($queryString)) {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error());
                    Message::setMessage('Produkt nebol upravený.', 2);
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

                    if (!empty($_POST['related'])) {
                        $queryString = "DELETE FROM " . TABLE_PREFIX . "product_related WHERE 1 AND related_product_id = '" . $_GET["product_id"] . "';";
                        if (!$ResultC = mysql_query($queryString)) {
                            if (mysql_errno())
                                print("MySql Error (" . mysql_errno() . "): " . mysql_error());
                        }
                        if (is_array($_POST["related"]) and sizeof($_POST["related"]) > 0) {
                            foreach ($_POST["related"] as $value) {
                                $queryString = "INSERT INTO " . TABLE_PREFIX . "product_related (related_product_id, product_id) values ('" . $_GET["product_id"] . "', '" . $value . "');";
                                if (!$ResultB = mysql_query($queryString)) {
                                    if (mysql_errno())
                                        print("MySql Error (" . mysql_errno() . "): "
                                                . mysql_error());
                                }
                            }
                        }else {
                            $_POST["related"][] = $_GET["related"];
                            foreach ($_POST["related"] as $value) {
                                $queryString = "INSERT INTO " . TABLE_PREFIX . "product_related (related_product_id, product_id) values ('" . $_GET["product_id"] . "', '" . $value . "');";
                                if (!$ResultB = mysql_query($queryString)) {
                                    if (mysql_errno())
                                        print("MySql Error (" . mysql_errno() . "): "
                                                . mysql_error());
                                }
                            }
                        }
                    }

                    // delete fotografia
                    if ($_POST['delete_foto'] == 1 OR ( isset($_FILES['image_preview']) and $_FILES['image_preview']['name'] <> '')) {
                        $query = "SELECT image_src FROM " . TABLE_PREFIX . "product WHERE 1 AND product_id = '" . $_GET["product_id"] . "';";
                        if ($result = mysql_query($query)) {

                            $row = mysql_fetch_object($result);
                            unlink('../photos/thumbnail/' . $row->image_src);
                            unlink('../photos/preview/' . $row->image_src);
                            unlink('../photos/original/' . $row->image_src);

                            $query = "UPDATE " . TABLE_PREFIX . "product SET image_src = '0' WHERE 1 AND product_id = '" . $_GET["product_id"] . "';";
                            mysql_query($query);

                            if (mysql_errno())
                                print("MySql Error (" . mysql_errno() . "): "
                                        . mysql_error());
                        }
                    }

                    // posielanie foto produktu
                    if (isset($_FILES['image_preview']) and $_FILES['image_preview']['name'] <> '') {

                        require_once('../shared/classes/class.image.php');

                        $thumbs_dimensions = explode(',', PRODUCT_THUMBS_DIMENSIONS);
                        $preview_dimensions = explode(',', PREVIEW_DIMENSIONS);
                        $original_dimensions = explode(',', ORIGINAL_DIMENSIONS);

                        $img = new abeautifulsite\SimpleImage($_FILES['image_preview']['tmp_name']);
                        $background = new abeautifulsite\SimpleImage(null, $thumbs_dimensions[0], $thumbs_dimensions[1], '#fff');
                        $u_image = $img->get_original_info();
                        if (is_array($u_image) AND ! empty($u_image)) { // main_image
                            $extension = '.' . pathinfo($_FILES['image_preview']['name'], PATHINFO_EXTENSION);
                            preg_match('/image/', $u_image['mime'], $type_match);
                            if ($type_match[0] == 'image') {
                                $name = sha1(time() . rand(0, 999)) . $extension;
                                try {
                                    // original
                                    if ($img->get_height() >= $original_dimensions[1]) {
                                        $img->fit_to_height($original_dimensions[1])->save('../photos/original/' . $name);
                                    } elseif ($img->get_width() >= $original_dimensions[0]) {
                                        $img->fit_to_width($original_dimensions[0])->save('../photos/original/' . $name);
                                    } else {
                                        $img->save('../photos/original/' . $name);
                                    }
                                    // thumb
                                    $thumbnail = $img->load($_FILES['image_preview']['tmp_name'])->best_fit($thumbs_dimensions[0], $thumbs_dimensions[1]);
                                    $background->overlay($thumbnail)->save('../photos/thumbnail/' . $name);
                                    $img->load($_FILES['image_preview']['tmp_name'])->best_fit($preview_dimensions[0], $preview_dimensions[1])->save('../photos/preview/' . $name);

                                    $queryString = "UPDATE " . TABLE_PREFIX . "product SET image_src='" . $name . "' WHERE 1 AND product_id='" . $_GET["product_id"] . "';";
                                    mysql_query($queryString);
                                } catch (Exception $e) {
                                    Message::setMessage('Chyba nahrávania obrázka: ' . $e->getMessage(), 2);
                                }
                            }
                        } else {
                            Message::setMessage('Chyba nahrávania obrázka ' . $_FILES['image_preview']['name'], 2);
                        }
                    }
                    // posielanie foto produktu END
                    Message::setMessage('Výrobok bol úspešne upravený', 0);
                    if ($_POST['gtc'] == 'true') {
                        header('Location: index.php?module=eshop_product');
                        exit;
                    } else {
                        $category_id = ((sizeof($_POST["category_id"]) > 0) ? $_POST["category_id"][0] : $_GET["category_id"]);
                        header("Location:./index.php?module=eshop_product_content&action=update&product_id=" . $_GET["product_id"] . (!empty($category_id) ? '&category_id=' . $category_id : ''));
                        exit;
                    }
                }
            }
            //
            //
            // výber údajov z DB
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

                    // zistime si zoznam suvisiacich produktov
                    $queryString = "SELECT product_id from " . TABLE_PREFIX . "product_related WHERE 1 AND related_product_id='" . $_GET["product_id"] . "';";
                    if ($Result = mysql_query($queryString)) {
                        if (mysql_num_rows($Result) > 0) {
                            $related = array();
                            while ($Row = mysql_fetch_assoc($Result)) {
                                $related[] = $Row["product_id"];
                            }
                        }
                        $_POST["related"] = $related;
                    }
                } else {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error());
                }
            } else {
                Message::setMessage('Chyba! Nesprávny Tvar ID produktu!', 2);
                header('Location: index.php?module=eshop_product');
                exit;
            }
            // výber údajov z DB END
            ?>
            <h1>Úprava produktu</h1>
            <form id="update-product" method="post" action="" enctype="multipart/form-data" >
                <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform item-edit">
                    <tr>
                        <td colspan="3" class="pseudo-tabs">
                            <ul>
                                <li><span>Detail produktu</span></li>
                                <li><a href="./index.php?module=eshop_product_content&amp;action=gallery&amp;product_id=<?= $_GET["product_id"]; ?>">Galéria produktu</a></li>
                            </ul>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <table summary="" border="0" cellspacing="0" cellpadding="0" class="tableform">
                                <tr>
                                    <td width="344">
                                        <table border="0" cellspacing="0" cellpadding="0" summary="" class="tableProduct_left">
                                            <?
                                            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                                ?>
                                                <tr>
                                                    <td>Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;"><?= $val; ?></sup></td>
                                                    <td>
                                                        <input type="text" style="width: 308px;" id="name"  name="<?= strtolower($val); ?>_name" value="<?= str_replace('"', '&quot;', $_POST[strtolower($val) . '_name']); ?>" />
                                                    </td>
                                                </tr>
                                                <?
                                            }
                                            ?>
                                            <tr>
                                                <td>Novinka </td>
                                                <td><input name="novelty" type="checkbox" class="w201px" id="novelty" value="1" <?= (($_POST['novelty'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                            <tr>
                                                <td>Odporúčame </td>
                                                <td><input name="recommend" type="checkbox" class="w201px" id="code_1" value="1" <?= (($_POST['recommend'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                            <tr>
                                                <td>Akcia </td>
                                                <td><input name="action" type="checkbox" class="w201px" value="1" <?= (($_POST['action'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                            <tr>
                                                <td>Výpredaj </td>
                                                <td><input name="sale" type="checkbox" class="w201px" value="1" <?= (($_POST['sale'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>                                            
                                            <tr>
                                                <td>Dodávateľ</td>
                                                <td>
                                                    <select name="suplier_id" class="w201px" id="suplier_id">
                                                        <option value=""></option>
                                                        <?
                                                        $query_combo = 'SELECT suplier_id, suplier FROM ' . TABLE_PREFIX . 'product_suplier WHERE 1 ORDER BY suplier ASC';
                                                        if ($result_combo = mysql_query($query_combo)) {
                                                            while ($row_combo = mysql_fetch_object($result_combo)) {
                                                                echo '<option value="' . $row_combo->suplier_id . '" ' . (($row_combo->suplier_id == $_POST["suplier_id"]) ? 'selected="selected"' : '') . '>' . $row_combo->suplier . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                        <?php //create_combo("select manufacturer_id as id, sk_name as name from " . TABLE_PREFIX . "manufacturer where 1 order by name;", $_POST["manufacturer_id"]);  ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>EAN kód </td>
                                                <td><input name="code_ean" type="text" style="width: 308px;" id="code_ean" value="<?= $_POST["code_ean"] ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Objednávací kód </td>
                                                <td><input name="code_suplier" type="text" style="width: 308px;" id="code_suplier" value="<?= $_POST["code_suplier"] ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Kód produktu </td>
                                                <td><input name="code_1" type="text" style="width: 308px;" id="code_1" value="<?= $_POST["code_1"] ?>" /></td>
                                            </tr>
                                            <?
                                            if($_POST["price_old"] AND $_POST["price_old"] > 0) {
                                                $price = $_POST["price_old"];
                                                $price_new = $_POST["price"];
                                                $percent_off = ($_POST["price_old"] - $_POST["price"]) * 100 / $_POST["price_old"];
                                            }
                                            else {
                                                $price = $_POST["price"];
                                            }
                                            ?>
                                            <tr>
                                                <td>Cena produktu s DPH</td>
                                                <td><input name="price" id="price" type="text" style="width: 308px;" id="price" value="<?= $price; ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Zľava v %</td>
                                                <td>
                                                    <input name="percent_off" id="percent_off" type="text" style="width: 50px;" value="<?= $percent_off; ?>" /> % 
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; cena po zľave: <input name="price_new" id="price_new" type="text" style="width: 100px;" value="<?= $price_new; ?>" />
                                                </td>
                                            </tr>
                                            <?
                                            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                                echo '<tr><td>Keywords<sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>';
                                                echo '<td><textarea name="' . $val . '_keywords" style="width: 308px; height: 60px;">' . $_POST[$val . '_keywords'] . '</textarea></td></tr>';
                                            }
                                            ?>
                                            <tr>
                                                <td>Výrobca</td>
                                                <td>
                                                    <select name="manufacturer_id" class="w201px" id="manufacturer_id">
                                                        <option value=""></option>
                                                        <?
                                                        $query_combo = 'SELECT manufacturer_id, sk_name FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 ORDER BY sk_name ASC';
                                                        if ($result_combo = mysql_query($query_combo)) {
                                                            while ($row_combo = mysql_fetch_object($result_combo)) {
                                                                echo '<option value="' . $row_combo->manufacturer_id . '" ' . (($row_combo->manufacturer_id == $_POST["manufacturer_id"]) ? 'selected="selected"' : '') . '>' . $row_combo->sk_name . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                        <?php //create_combo("select manufacturer_id as id, sk_name as name from " . TABLE_PREFIX . "manufacturer where 1 order by name;", $_POST["manufacturer_id"]);  ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Produkt je možné kúpiť</td>
                                                <td>
                                                    <select name="available" id="available">
                                                        <option value="1"<?= ((isset($_POST["available"])) and ( $_POST["available"] == 1) or ( !isset($_POST["available"]))) ? ' selected="selected"' : '' ?>>Áno</option>
                                                        <option value="0"<?= ((isset($_POST["available"])) and ( $_POST["available"] == 0)) ? ' selected="selected"' : '' ?>>Nie</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Skladom</td>
                                                <td><input name="delivery_time" type="checkbox" class="w201px" value="1" <?= (($_POST['delivery_time'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="tableProduct_right">
                                        Zaradenie v kategóriách<br />
                                        <select name="category_id[]" size="10" multiple="multiple" style="min-height:60px;" id="category_id">
                                            <?
                                            $menus_array = array();
                                            if (isset($_REQUEST['product_id'])) {
                                                $query_menus = 'SELECT menu_id FROM ' . TABLE_PREFIX . 'product_menu WHERE product_id=' . $_REQUEST['product_id'];
                                                if ($result_menus = mysql_query($query_menus)) {
                                                    while ($row_menus = mysql_fetch_object($result_menus)) {
                                                        $menus_array[] = $row_menus->menu_id;
                                                    }
                                                }
                                            }
                                            Menu::print_tree_combobox(ESHOP_MAIN_CATEGORY, $menus_array, 1);
                                            ?>
                                        </select><br />
                                        Súvisiace produkty<br />
                                        <select name="related[]" multiple="multiple" class="multiple">
                                            <option>Žiaden</option>
                                            <?
                                            $related_query = 'SELECT product_id, sk_name FROM ' . TABLE_PREFIX . 'product WHERE 1 ORDER BY sk_name ASC';
                                            if ($related_result = mysql_query($related_query)) {
                                                while ($relared_row = mysql_fetch_object($related_result)) {
                                                    echo '<option value="' . $relared_row->product_id . '"' . ((isset($_POST["related"]) AND in_array($relared_row->product_id, $_POST["related"])) ? ' selected=""' : '') . ($relared_row->product_id == $_GET["product_id"] ? ' disabled=""' : '') . '>' . $relared_row->sk_name . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                        <?
                                            if($_POST["url_suplier"] AND trim($_POST["url_suplier"]) != '') {
                                                ?>
                                                <span style="text-align: right; display: block; margin-right: 30px;"><a href="<?= $_POST["url_suplier"]; ?>" target="_blank" title="<?= $_POST["url_suplier"]; ?>">url</a></span>
                                                <?
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        ?>
                        <tr>
                            <td>Charakteristika produktu <sup><strong>[<?= strtolower($val) ?>]</strong></sup></td>
                            <td colspan="2">
                                <textarea class="ckeditor" name="<?= strtolower($val); ?>_description"><?= html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8"); ?></textarea>
                            </td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Obrázok produktu:</td>
                        <td colspan="2">
                            <? if (is_file("../photos/thumbnail/" . $_POST['image_src'])) { ?>
                                <a href="../photos/preview/<?= $_POST['image_src']; ?>" class="fancybox-image product-image" title="<?= $_POST['image_src'] ?>" border="0">
                                    <img src="../photos/thumbnail/<?= $_POST['image_src']; ?>" alt="<?= $_POST['image_src']; ?>" border="0" width="150" />
                                </a>
                                <?
                                $image_prenos = $_POST['image_src'];
                            }
                            ?>
                            <div class="product-image-actions">
                                <input name="image_preview" type="file" id="image_preview" />
                                <label><input name="delete_foto" type="checkbox" value="1" /> Odstrániť aktuálny obrázok</label>
                            </div>
                        </td>
                    </tr>
                    <?
                    /*
                    if (is_numeric($_GET['product_id'])) {
                        ?>
                        <td>
                            Varianty produktov:
                            <br />
                            <br />
                            <a class="fancybox-iframe" href="<?= ROOTDIR ?>/setup/modules/_eshop_product_colors.php?product_id=<?= $_GET["product_id"] ?>">Editovať zoznam variantov</a>
                            <br />
                            <a href="index.php?module=eshop_product_color_add&eshop=1" target="_blank">Editovať databázu farieb</a>
                        </td>
                        <td colspan="2">
                            <div>
                                <table style="width:100%;margin-bottom: 10px;" border="0" cellpadding="3" cellspacing="3" style="border: solid 1px #eeeeee;">
                                    <tr style="background-color: #eeeeee; font-weight: bold;">
                                        <td>Názov farby </td>
                                        <td>Názov velkosti</td>
                                        <!--<td>Img</td>-->
                                    </tr>
                                    <?php
                                    $queryString = "SELECT * FROM " . TABLE_PREFIX . "product_color
                                                                        LEFT JOIN " . TABLE_PREFIX . "color USING(color_id)
                                                                        WHERE 1 AND product_id = '" . $_GET["product_id"] . "';";
                                    if (!$Result = mysql_query($queryString)) {
                                        echo 'Chyba vyberu farieb z DB .';
                                    } else {
                                        if (mysql_num_rows($Result) != 0) {
                                            while ($Row = mysql_fetch_assoc($Result)) {
                                                echo '<tr>';
                                                echo '<td style="background:#' . $Row['code'] . ';"><span style="color: ' . invertColor($Row['code']) . '">' . $Row['name'] . '</span></td>';
                                                echo '<td>';
                                                $queryStringVelkosti = "SELECT name FROM " . TABLE_PREFIX . "product_type WHERE 1 AND product_color_id = '" . $Row['product_color_id'] . "' GROUP BY product_type_id ORDER BY name;";
                                                $string = '';
                                                if (!$ResultVelkosti = mysql_query($queryStringVelkosti)) {
                                                    echo 'Chyba vyberu veľkostí z DB .';
                                                } else {
                                                    while ($RowVelkosti = mysql_fetch_assoc($ResultVelkosti)) {
                                                        $string .= $RowVelkosti['name'] . ', ';
                                                    }
                                                }
                                                echo rtrim($string, ', ');
                                                
                                                //  echo '<td>';
                                                //  if (!empty($Row['src']))
                                                //  echo '<a href="../photos/images/' . $Row['src'] . '" rel="thickbox" class="thickbox">img</a>';
                                                //  echo '</td>';
                                                
                                                echo '</tr>';
                                            }
                                        } else {
                                            $queryString1 = "SELECT * FROM " . TABLE_PREFIX . "product_color WHERE 1 AND product_id = '" . $_GET["product_id"] . "';";
                                            if (!$Result1 = mysql_query($queryString1)) {
                                                echo 'Chyba vyberu farieb z DB .';
                                            } else {
                                                if (mysql_num_rows($Result1) != 0) {
                                                    while ($Row1 = mysql_fetch_assoc($Result1)) {
                                                        echo '<tr>';
                                                        echo '<td>Základná farba</td>';
                                                        echo '<td>';
                                                        $queryStringVelkosti1 = "SELECT * FROM " . TABLE_PREFIX . "product_type WHERE 1 AND product_color_id = '" . $Row1['product_color_id'] . "' and univerzal = '0';";
                                                        $string = '';
                                                        if (!$ResultVelkosti1 = mysql_query($queryStringVelkosti1)) {
                                                            echo 'Chyba vyberu veľkostí z DB .';
                                                        } else {
                                                            while ($RowVelkosti1 = mysql_fetch_assoc($ResultVelkosti1)) {
                                                                $string .= $RowVelkosti1['name'] . ' <small>(' . $RowVelkosti['pocet'] . ')</small>, ';
                                                            }
                                                        }
                                                        echo rtrim($string, ', ');
                                                        
                                                        //  echo '<td>';
                                                        //  if (!empty($Row1['src']))
                                                        //  echo '<a href="../photos/images/' . $Row1['src'] . '" rel="thickbox" class="thickbox">img</a>';
                                                        //  echo '</td>';
                                                        
                                                        echo '</tr>';
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    ?>
                                </table>
                            </div>
                        </td>
                        </tr>
                        <?
                    }*/
                    ?>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr class="hide-for-faq">
                        <td colspan="3">
                            <? include("../popups/foto.php"); ?>
                            <? include("../popups/docs.php"); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right">
                            <input name="image_prenos" type="hidden" value="<?= $image_prenos ?>" />
                            <button type="submit" name="gtc" value="true">Upraviť produkt a vrátiť sa do katalógu</button>
                            <button type="submit" />Upraviť produkt</button>
                            <button type="submit" name="BtnSaveAsNew"  onClick="return changeFormAction('index.php?module=eshop_product_content&action=insert');" />Pridať ako nový</button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                </table>
            </form>
            <div class="clear"></div>
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
                                $queryString = "INSERT INTO " . TABLE_PREFIX . "_photo_images
                                                    (photo_category_id, menu_id, name, description, src, image_type, owner, date, sorter)
                                                VALUES
                                                    ('" . $_GET['product_id'] . "', '0','', '', '" . $name . "', '" . $extension . "', '', NOW(), '0');";
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
            $query = 'SELECT sk_name AS name FROM ' . TABLE_PREFIX . 'product WHERE product_id="' . $_GET['product_id'] . '";';
            $result = mysql_query($query);
            if (mysql_num_rows($result) != '0') {
                $row = mysql_fetch_assoc($result);
            } else {
                Message::setMessage('Chyba! Id produktu nebolo nájdené!', 2);
                header('Location: index.php?module=eshop_product');
                exit;
            }
            // výber údajov z DB END
            ?>
            <h1>Úprava produktu</h1>
            <form id="update-product" method="post" action="" enctype="multipart/form-data" >
                <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform item-edit">
                    <tr>
                        <td colspan="3" class="pseudo-tabs">
                            <ul>
                                <li><a href="./index.php?module=eshop_product_content&amp;action=update&amp;product_id=<?= $_GET["product_id"]; ?>">Detail produktu</a></li>
                                <li><span>Galéria produktu</span></li>
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
                                        <form action="" method="post" enctype="multipart/form-data">
                                            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                <tr>
                                                    <td>
                                                        <span class="text-label"><strong>Kategória:</strong></span><br />
                                                        <?= $row['name']; ?>
                                                    </td>
                                                    <td colspan="2">
                                                        <span class="text-label"><strong>Súbory:</strong></span><br />
                                                        <input name="image_file[]" type="file" id="image_file" multiple="multiple" />
                                                        <button name="submit" type="submit">Nahrať</button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">&nbsp;</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="3">
                                                        <div class="product-images-gallery">
                                                            <?
                                                            $query = 'SELECT *, name as image_name, src as image_src
                                                                      FROM ' . TABLE_PREFIX . '_photo_images
                                                                      WHERE 1 AND photo_category_id = "' . $_GET['product_id'] . '"
                                                                      ORDER BY sorter ASC;';
                                                            $result = mysql_query($query);
                                                            if ($result) {
                                                                if (mysql_num_rows($result) > 0) {
                                                                    $i = 0;
                                                                    while ($row = mysql_fetch_object($result)) {
                                                                        $i++;
                                                                        ?>
                                                                        <div>
                                                                            <a class="fancybox-image" rel="gallery" href="<?= ROOTDIR; ?>/photos/original/<?= $row->image_src; ?>">
                                                                                <img src="<?= ROOTDIR; ?>/photos/thumbnail/<?= $row->image_src; ?>" />
                                                                            </a>
                                                                            <span><?= reset(explode(".", $row->image_src)); ?></span>
                                                                            <a href="javascript:confirmAction('Naozaj chcete odstrániť tento obrázok?', '', 'index.php?module=eshop_product_content&action=remove-image&image_src=<?= $row->image_src; ?>&product_id=<?= $_REQUEST['product_id']; ?>');">[Odstrániť]</a>
                                                                        </div>
                                                                        <?
                                                                        if($i % 4 == 0)
                                                                            echo '<div class="clear"></div>';
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
            </form>
            <div class="clear"></div>
            <?
            break;
        case"remove-image":
            @unlink('../photos/thumbnail/' . $_GET['image_src']);
            @unlink('../photos/preview/' . $_GET['image_src']);
            @unlink('../photos/original/' . $_GET['image_src']);

            $queryString = 'DELETE FROM ' . TABLE_PREFIX . '_photo_images WHERE src="' . $_GET['image_src'] . '";';
            Message::setMessage('Obrázok bol úspešne odstránený.', 0);
            mysql_query($queryString);
            header('Location: index.php?module=eshop_product_content&action=gallery&product_id=' . $_GET['product_id']);
            exit;
            break;
        default:
            if ($_POST OR ! empty($_POST["sk_name"])) {
                // zmenime  udaje   v   databaze
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude1 .= "`" . strtolower($val) . "_name`, `" . strtolower($val) . "_name_seo`, `" . strtolower($val) . "_description`, `" . strtolower($val) . "_keywords`,";

                    $queryInclude2 .= "'" . $_POST[strtolower($val) . '_name'] . "', '" . String::SEOFriendlyText($_POST[strtolower($val) . '_name']) . "', '" .
                            str_replace(array('"content/', '"../../../content/', '"../../../../content/'), '"' . ROOTDIR . '/content/', html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8")) . "', '" . $_POST[strtolower($val) . '_keywords'] . "',";
                }

                $queryString = "INSERT INTO " . TABLE_PREFIX . "product
				(
					`date`,
					" . $queryInclude1 . "
					price,
					price_old,
					manufacturer_id,
					available,
					skladom, 
                    delivery_time, 
					code_1,
                    code_ean,
                    code_suplier,
                    suplier_id,
					file_name,
					recommend,
                    action,
                    novelty,
                    sale
				) VALUES (
					now(),
					" . $queryInclude2 . "
					'" . preparePrice($_POST["price"]) . "',
					'" . preparePrice($_POST["price_old"]) . "',
					" . ((is_numeric($_POST["manufacturer_id"])) ? $_POST["manufacturer_id"] : 'null') . ",
					'" . $_POST["available"] . "',
					'" . $_POST["skladom"] . "',
                    '" . ($_POST["delivery_time"] == 1 ? 1 : 0) . "',
					'" . $_POST["code_1"] . "',
                    '" . $_POST["code_ean"] . "',
                    '" . $_POST["code_suplier"] . "',
                    '" . $_POST["suplier_id"] . "',
					'" . $filename_bezdkr . "',
					'" . ((isset($_POST['recommend'])) ? 1 : 0) . "',
					'" . ((isset($_POST['action'])) ? 1 : 0) . "',
                    '" . ((isset($_POST['novelty'])) ? 1 : 0) . "',
					'" . ((isset($_POST['sale'])) ? 1 : 0) . "'
				);";
                if (!$Result = mysql_query($queryString)) {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error());
                    Message::setMessage('Produkt nebol pridaný.', 2);
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

                    if (!empty($_POST['related'])) {
                        $queryString = "DELETE FROM " . TABLE_PREFIX . "product_related WHERE 1 AND related_product_id = '" . $product_id . "';";
                        if (!$ResultC = mysql_query($queryString)) {
                            if (mysql_errno())
                                print("MySql Error (" . mysql_errno() . "): " . mysql_error());
                        }
                        if (is_array($_POST["related"]) and sizeof($_POST["related"]) > 0) {
                            foreach ($_POST["related"] as $value) {
                                $queryString = "INSERT INTO " . TABLE_PREFIX . "product_related (related_product_id, product_id) values ('" . $product_id . "', '" . $value . "');";
                                if (!$ResultB = mysql_query($queryString)) {
                                    if (mysql_errno())
                                        print("MySql Error (" . mysql_errno() . "): "
                                                . mysql_error());
                                }
                            }
                        }else {
                            $_POST["related"][] = $_GET["related"];
                            foreach ($_POST["related"] as $value) {
                                $queryString = "INSERT INTO " . TABLE_PREFIX . "product_related (related_product_id, product_id) values ('" . $product_id . "', '" . $value . "');";
                                if (!$ResultB = mysql_query($queryString)) {
                                    if (mysql_errno())
                                        print("MySql Error (" . mysql_errno() . "): "
                                                . mysql_error());
                                }
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
                    $queryString = "insert into " . TABLE_PREFIX . "product_type (product_id,product_color_id,name,univerzal) values ('" . $product_id . "','" . $product_color_id . "','Univerzálna','1');";
                    if (!$ResultB = mysql_query($queryString)) {
                        if (mysql_errno())
                            print("MySql Error (" . mysql_errno() . "): "
                                    . mysql_error());
                    }

                    // posielanie foto produktu
                    if (isset($_FILES['image_preview']) and $_FILES['image_preview']['name'] <> '') {

                        require_once('../shared/classes/class.image.php');

                        $thumbs_dimensions = explode(',', PRODUCT_THUMBS_DIMENSIONS);
                        $preview_dimensions = explode(',', PREVIEW_DIMENSIONS);
                        $original_dimensions = explode(',', ORIGINAL_DIMENSIONS);

                        $img = new abeautifulsite\SimpleImage($_FILES['image_preview']['tmp_name']);
                        $background = new abeautifulsite\SimpleImage(null, $thumbs_dimensions[0], $thumbs_dimensions[1], '#fff');
                        $u_image = $img->get_original_info();
                        if (is_array($u_image) AND ! empty($u_image)) { // main_image
                            $extension = '.' . pathinfo($_FILES['image_preview']['name'], PATHINFO_EXTENSION);
                            preg_match('/image/', $u_image['mime'], $type_match);
                            if ($type_match[0] == 'image') {
                                $name = sha1(time() . rand(0, 999)) . $extension;
                                try {
                                    // original
                                    if ($img->get_height() >= $original_dimensions[1]) {
                                        $img->fit_to_height($original_dimensions[1])->save('../photos/original/' . $name);
                                    } elseif ($img->get_width() >= $original_dimensions[0]) {
                                        $img->fit_to_width($original_dimensions[0])->save('../photos/original/' . $name);
                                    } else {
                                        $img->save('../photos/original/' . $name);
                                    }
                                    // thumb
                                    $thumbnail = $img->load($_FILES['image_preview']['tmp_name'])->best_fit($thumbs_dimensions[0], $thumbs_dimensions[1]);
                                    $background->overlay($thumbnail)->save('../photos/thumbnail/' . $name);
                                    $img->load($_FILES['image_preview']['tmp_name'])->best_fit($preview_dimensions[0], $preview_dimensions[1])->save('../photos/preview/' . $name);

                                    $queryString = "UPDATE " . TABLE_PREFIX . "product SET image_src='" . $name . "' WHERE 1 AND product_id='" . $product_id . "';";
                                    mysql_query($queryString);
                                } catch (Exception $e) {
                                    Message::setMessage('Chyba nahrávania obrázka: ' . $e->getMessage(), 2);
                                }
                            }
                        } else {
                            Message::setMessage('Chyba nahrávania obrázka ' . $_FILES['image_preview']['name'], 2);
                        }
                    } else {
                        if (isset($_POST["BtnSaveAsNew"])) {
                            if (!empty($_POST["image_prenos"])) {
                                require_once('../shared/classes/class.image.php');
                                $img = new abeautifulsite\SimpleImage('../photos/original/' . $_POST["image_prenos"]);
                                $extension = '.' . pathinfo($_POST["image_prenos"], PATHINFO_EXTENSION);
                                $name = sha1(time() . rand(0, 999)) . $extension;
                                try {
                                    // original
                                    $img->save('../photos/original/' . $name);
                                    // thumb
                                    $img->load('../photos/thumbnail/' . $_POST["image_prenos"])->save('../photos/thumbnail/' . $name);
                                    // preview
                                    $img->load('../photos/preview/' . $_POST["image_prenos"])->save('../photos/preview/' . $name);

                                    $queryString = "UPDATE " . TABLE_PREFIX . "product SET image_src='" . $name . "' WHERE 1 AND product_id='" . $product_id . "';";
                                    mysql_query($queryString);
                                } catch (Exception $e) {
                                    Message::setMessage('Chyba nahrávania obrázka: ' . $e->getMessage(), 2);
                                }
                            }
                        }
                    }

                    // vytvorenie fotogalerie zariadenia
                    $sql_strFotogaleria = "INSERT INTO `" . TABLE_PREFIX . "_photo_category` (photo_category_id, name) VALUES	('" . product_id . "','" . $_POST["sk_name"] . "');";
                    $resultFotogaleria = @mysql_query($sql_strFotogaleria);
                    if ($resultFotogaleria != 0) {

                    } else
                        echo mysql_error();
                    @mysql_free_result($resultFotogaleria);

                    Message::setMessage('Výrobok bol úspešne pridaný', 0);
                    if ($_POST['gtc'] == 'true') {
                        header('Location: index.php?module=eshop_product');
                        exit;
                    } else {
                        //header("Location:./index.php?module=eshop_product_content&eshop=1&product_id=" . $product_id . "&category_id=" . ((sizeof($_POST["category_id"]) > 0) ? $_POST["category_id"][0] : $_GET["category_id"]));
                        //exit;
                    }
                }
            }
            ?>
            <h1>Pridanie produktu</h1>
            <form method="post" action="" name="f0054" id="f0054" enctype="multipart/form-data" >
                <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform item-edit">
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <table summary="" border="0" cellspacing="0" cellpadding="0" class="tableform">
                                <tr>
                                    <td width="344">
                                        <table border="0" cellspacing="0" cellpadding="0" summary="" class="tableProduct_left">
                                            <?
                                            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                                echo '
					<tr>
                                            <td>Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>
                                            <td><input type="text" style="width: 308px;" id="name"  name="' . strtolower($val) . '_name" value="' . str_replace('"', '&quot;', $_POST[strtolower($val) . '_name']) . '" /></td>
					</tr>';
                                            }
                                            ?>
                                            <tr>
                                                <td>Novinka </td>
                                                <td><input name="novelty" type="checkbox" class="w201px" id="novelty" value="1" <?= (($_POST['novelty'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                            <tr>
                                                <td>Odporúčame </td>
                                                <td><input name="recommend" type="checkbox" class="w201px" id="code_1" value="1" <?= (($_POST['recommend'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                            <tr>
                                                <td>Akcia </td>
                                                <td><input name="action" type="checkbox" class="w201px" value="1" <?= (($_POST['action'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                            <tr>
                                                <td>Výpredaj </td>
                                                <td><input name="sale" type="checkbox" class="w201px" value="1" <?= (($_POST['sale'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                            <tr>
                                                <td>Dodávateľ</td>
                                                <td>
                                                    <select name="suplier_id" class="w201px" id="suplier_id">
                                                        <option value=""></option>
                                                        <?
                                                        $query_combo = 'SELECT suplier_id, suplier FROM ' . TABLE_PREFIX . 'product_suplier WHERE 1 ORDER BY suplier ASC';
                                                        if ($result_combo = mysql_query($query_combo)) {
                                                            while ($row_combo = mysql_fetch_object($result_combo)) {
                                                                echo '<option value="' . $row_combo->suplier_id . '" ' . (($row_combo->suplier_id == $_POST["suplier_id"]) ? 'selected="selected"' : '') . '>' . $row_combo->suplier . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                        <?php //create_combo("select manufacturer_id as id, sk_name as name from " . TABLE_PREFIX . "manufacturer where 1 order by name;", $_POST["manufacturer_id"]);   ?>
                                                    </select>                                       </td>
                                            </tr>
                                            <tr>
                                                <td>EAN kód </td>
                                                <td><input name="code_ean" type="text" style="width: 308px;" id="code_suplier" value="<?= $_POST["code_ean"] ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Objednavaci kód </td>
                                                <td><input name="code_suplier" type="text" style="width: 308px;" id="code_suplier" value="<?= $_POST["code_suplier"] ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Kód produktu </td>
                                                <td><input name="code_1" type="text" style="width: 308px;" id="code_1" value="<?= $_POST["code_1"] ?>" /></td>
                                            </tr>
                                            <?
                                            if($_POST["price_old"] AND $_POST["price_old"] > 0) {
                                                $price = $_POST["price_old"];
                                                $price_new = $_POST["price"];
                                                $percent_off = ($_POST["price_old"] - $_POST["price"]) * 100 / $_POST["price_old"];
                                            }
                                            else {
                                                $price = $_POST["price"];
                                            }
                                            ?>
                                            <tr>
                                                <td>Cena produktu s DPH</td>
                                                <td><input name="price" id="price" type="text" style="width: 308px;" id="price" value="<?= $price; ?>" /></td>
                                            </tr>
                                            <tr>
                                                <td>Zľava v %</td>
                                                <td>
                                                    <input name="percent_off" id="percent_off" type="text" style="width: 50px;" value="<?= $percent_off; ?>" /> % 
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; cena po zľave: <input name="price_new" id="price_new" type="text" style="width: 100px;" value="<?= $price_new; ?>" />
                                                </td>
                                            </tr>
                                            <?
                                            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                                echo '<tr><td>Keywords<sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>';
                                                echo '<td><textarea name="' . $val . '_keywords" style="width: 308px; height: 60px;">' . $_POST[$val . '_keywords'] . '</textarea></td></tr>';
                                            }
                                            ?>
                                            <tr>
                                                <td>Výrobca</td>
                                                <td>
                                                    <select name="manufacturer_id" class="w201px" id="manufacturer_id">
                                                        <option value=""></option>
                                                        <?
                                                        $query_combo = 'SELECT manufacturer_id, sk_name FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 ORDER BY sk_name ASC';
                                                        if ($result_combo = mysql_query($query_combo)) {
                                                            while ($row_combo = mysql_fetch_object($result_combo)) {
                                                                echo '<option value="' . $row_combo->manufacturer_id . '" ' . (($row_combo->manufacturer_id == $_POST["manufacturer_id"]) ? 'selected="selected"' : '') . '>' . $row_combo->sk_name . '</option>';
                                                            }
                                                        }
                                                        ?>
                                                        <?php //create_combo("select manufacturer_id as id, sk_name as name from " . TABLE_PREFIX . "manufacturer where 1 order by name;", $_POST["manufacturer_id"]);   ?>
                                                    </select>										</td>
                                            </tr>

                                            <tr>
                                                <td>Produkt je možné kúpiť</td>
                                                <td>
                                                    <select name="available" id="available">
                                                        <option value="1"<?= ((isset($_POST["available"])) and ( $_POST["available"] == 1) or ( !isset($_POST["available"]))) ? ' selected="selected"' : '' ?>>Áno</option>
                                                        <option value="0"<?= ((isset($_POST["available"])) and ( $_POST["available"] == 0)) ? ' selected="selected"' : '' ?>>Nie</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Skladom</td>
                                                <td><input name="delivery_time" type="checkbox" class="w201px" value="1" <?= (($_POST['delivery_time'] == 1) ? ' checked="checked"' : ''); ?> /></td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td class="tableProduct_right">
                                        Zaradenie v kategóriách<br />
                                        <select name="category_id[]" size="10" multiple="multiple" style="min-height:60px;" id="category_id">
                                            <?
                                            $menus_array = array();
                                            if (isset($_REQUEST['product_id'])) {
                                                $query_menus = 'SELECT menu_id FROM ' . TABLE_PREFIX . 'product_menu WHERE product_id=' . $_REQUEST['product_id'];
                                                if ($result_menus = mysql_query($query_menus)) {
                                                    while ($row_menus = mysql_fetch_object($result_menus)) {
                                                        $menus_array[] = $row_menus->menu_id;
                                                    }
                                                }
                                            }
                                            Menu::print_tree_combobox(ESHOP_MAIN_CATEGORY, $menus_array, 1);
                                            ?>
                                        </select><br />
                                        Súvisiace produkty<br />
                                        <select name="related[]" multiple="multiple" class="multiple">
                                            <option>Žiaden</option>
                                            <?
                                            $related_query = 'SELECT product_id, sk_name FROM ' . TABLE_PREFIX . 'product WHERE 1 ORDER BY sk_name ASC';
                                            if ($related_result = mysql_query($related_query)) {
                                                while ($relared_row = mysql_fetch_object($related_result)) {
                                                    echo '<option value="' . $relared_row->product_id . '"' . ((isset($_POST["related"]) AND in_array($relared_row->product_id, $_POST["related"])) ? ' selected=""' : '') . ($relared_row->product_id == $_GET["product_id"] ? ' disabled=""' : '') . '>' . $relared_row->sk_name . '</option>';
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <?
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        ?>
                        <tr>
                            <td>Charakteristika produktu <sup><strong>[<?= strtolower($val) ?>]</strong></sup></td>
                            <td colspan="2">
                                <textarea class="ckeditor" name="<?= strtolower($val); ?>_description"><?= html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8"); ?></textarea>
                            </td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>Obrázok produktu:</td>
                        <td colspan="2">
                            <input name="image_preview" type="file" id="image_preview" />
                        </td>
                    </tr>
                    <? /*
                    <tr>
                        <td>Varianty produktu:</td>
                        <td>
                            <div class="variants-not-available">VLASTNOSTI JE MOŽNÉ PRIDÁVAŤ AŽ PO PRIDANÍ PRODUKTU</div>
                        </td>
                    </tr>
                    */
                    ?>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right">
                            <input name="image_prenos" type="hidden" value="<?= $image_prenos ?>" />
                            <button type="submit" name="gtc" value="true">Pridať produkt a vrátiť sa do katalógu</button>
                            <button type="submit">Pridať produkt</button>
                            <!--<input type="button" value="Zobraziť katalóg" onclick="javascript:document.location.href = './index.php?module=eshop_product&eshop=1&category_id=<?= $_GET["category_id"] ?>';" />-->
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                </table>
            </form>
            <div class="clear"></div>
        <?
    }
    ?>
</div>

<script type="text/javascript">

    $(document).on('focus', '#percent_off', function() {
        //console.log('#percent_off focused');
        $("#percent_off").keyup(function() {
            //console.log($('#percent_off').val(this.value))
            var price = parseFloat($('#price').val());
            //console.log('price: ' + price);
            var percentOff = parseFloat($('#percent_off').val().replace(/\,/g, '.'));
            //console.log('percent off: ' + percentOff);
            var priceNew = ((100 - percentOff) / 100) * price;
            //console.log('price new: ' + priceNew);
            if(priceNew < 0 || priceNew >= price) {
                $('#price_new').css('color', 'red');
            }
            else {
                $('#price_new').css('color', '');
            }
            $('#price_new').val(priceNew.toFixed(2));

            if(percentOff == 0) {
                $('#price_new').val('');
            }
        });
    }).on('blur', '#percent_off', function() {
        //console.log('#percent_off unfocused');
    });

    $(document).on('focus', '#price_new', function() {
        //console.log('#price_new focused');
        $("#price_new").keyup(function() {
            var price = parseFloat($('#price').val());
            var priceNew = parseFloat($('#price_new').val().replace(/\,/g, '.'));
            var percentOff = (price - priceNew) * 100 / price;
            if(percentOff < 0 || percentOff >= 100) {
                $('#percent_off').css('color', 'red');
            }
            else {
                $('#percent_off').css('color', '');
            }
            $('#percent_off').val(percentOff.toFixed(2));
        });
    }).on('blur', '#price_new', function() {
        //console.log('#price_new unfocused');
    });

    $(document).ready(function () {
        
        $("#price").keyup(function() {
            var price = parseFloat($('#price').val());
            var percentOff = parseFloat($('#percent_off').val().replace(/\,/g, '.'));
            var priceNew = ((100 - percentOff) / 100) * price;
            $('#price_new').val(priceNew.toFixed(2));

            if(percentOff == 0) {
                $('#price_new').val('');
            }
        });

    });

</script>