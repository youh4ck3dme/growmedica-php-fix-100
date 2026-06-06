<?
require_once('../shared/classes/class.eshop.php');
Installator::checkIfTableExist();
?>

<script>
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
</script>
<?php
if (isset($_POST['manufacturer_id']) and ! is_numeric($_POST['manufacturer_id'])) {
    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
        $queryInclude1 .= "`" . strtolower($val) . "_name`,";
        $queryInclude2 .= "'" . $_POST[strtolower($val) . '_name'] . "', ";
    }

    $sql_str = "INSERT INTO `" . TABLE_PREFIX . "manufacturer`
                    (" . $queryInclude1 . "`doplnok`)
		VALUES (" . $queryInclude2 . "'');";

    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        $manufacturer_id = mysql_insert_id();
        Message::setMessage('Výrobca bol úspešne vložený.', 0);
        // posielanie loga
        if (isset($_FILES['logo']) and $_FILES['logo']['name'] <> '') {

            require_once('../shared/classes/class.image.php');
            $image = new abeautifulsite\SimpleImage();

            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $name = str_pad($manufacturer_id, 4, '0', STR_PAD_LEFT) . '-logo-' . String::SEOFriendlyText($_POST[strtolower($val) . '_name']) . '.' . $ext;
            $thumb_dm = explode(',', THUMBS_DIMENSIONS);
            $preview_dm = explode(',', PREVIEW_DIMENSIONS);

            try {
                $image->load($_FILES['logo']['tmp_name'])->save('../images/manufacturers/original/' . $name)->best_fit($preview_dm[0], $preview_dm[1])->save('../images/manufacturers/preview/' . $name)->best_fit($thumb_dm[0], $thumb_dm[1])->save('../images/manufacturers/thumbnail/' . $name);
            }
            catch (Exception $e) {
                Message::setMessage('Chyba nahrávania loga: ' . $e->getMessage(), 2);
            }
            $q = 'UPDATE ' . TABLE_PREFIX . 'manufacturer SET logo = "' . $name . '" WHERE 1 AND manufacturer_id = ' . $manufacturer_id . ';';
            if(!$r = mysql_query($q))
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
        // posielanie logo END

        // posielanie pozadie loga
        if (isset($_FILES['logo_background']) and $_FILES['logo_background']['name'] <> '') {

            require_once('../shared/classes/class.image.php');
            $image = new abeautifulsite\SimpleImage();

            $ext = pathinfo($_FILES['logo_background']['name'], PATHINFO_EXTENSION);
            $name = str_pad($manufacturer_id, 4, '0', STR_PAD_LEFT) . '-logo_bg-' . String::SEOFriendlyText($_POST[strtolower($val) . '_name']) . '.' . $ext;
            $thumb_dm = explode(',', THUMBS_DIMENSIONS);
            $preview_dm = explode(',', PREVIEW_DIMENSIONS);

            try {
                $image->load($_FILES['logo_background']['tmp_name'])->save('../images/manufacturers/original/' . $name)->best_fit($preview_dm[0], $preview_dm[1])->save('../images/manufacturers/preview/' . $name)->best_fit($thumb_dm[0], $thumb_dm[1])->save('../images/manufacturers/thumbnail/' . $name);
            }
            catch (Exception $e) {
                Message::setMessage('Chyba nahrávania pozadia loga: ' . $e->getMessage(), 2);
            }
            $q = 'UPDATE ' . TABLE_PREFIX . 'manufacturer SET logo_background = "' . $name . '" WHERE 1 AND manufacturer_id = ' . $manufacturer_id . ';';
            if(!$r = mysql_query($q))
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
        // posielanie pozadie loga END

        header("Location: ./index.php?module=eshop_manufacturers&eshop=1&action=list");
        exit;
    } else {
        Message::setMessage('Výrobca nebol vložený.', 2);
        die(mysql_error());
    }
    mysql_free_result($ResultA);
}

if (isset($_POST['manufacturer_id'])) {

    if(isset($_POST['delete_logo_background']) AND $_POST['delete_logo_background'] == 1) {
        $query = 'SELECT logo_background FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 AND manufacturer_id = ' . $_POST['manufacturer_id'] . ';';
        if ($result = mysql_query($query)) {
            $row = mysql_fetch_object($result);
            unlink('../images/manufacturers/thumbnail/' . $row->logo_background);
            unlink('../images/manufacturers/preview/' . $row->logo_background);
            unlink('../images/manufacturers/original/' . $row->logo_background);

            if(!mysql_query('UPDATE ' . TABLE_PREFIX . 'manufacturer SET logo_background = NULL WHERE 1 AND manufacturer_id = ' . $_POST['manufacturer_id'] . ';'))
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
    }
    if(isset($_POST['delete_logo']) AND $_POST['delete_logo'] == 1) {
        $query = 'SELECT logo FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 AND manufacturer_id = ' . $_POST['manufacturer_id'] . ';';
        if ($result = mysql_query($query)) {
            $row = mysql_fetch_object($result);
            unlink('../images/manufacturers/thumbnail/' . $row->logo);
            unlink('../images/manufacturers/preview/' . $row->logo);
            unlink('../images/manufacturers/original/' . $row->logo);

            if(!mysql_query('UPDATE ' . TABLE_PREFIX . 'manufacturer SET logo = NULL WHERE 1 AND manufacturer_id = ' . $_POST['manufacturer_id'] . ';'))
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
    }


    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
        $queryInclude .= "`" . strtolower($val) . "_name` = '" . html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8") . "', ";
    }

    $sql_str = "UPDATE `" . TABLE_PREFIX . "manufacturer`
		SET
                    " . $queryInclude . "`doplnok` = ''
		WHERE
                    1
		AND
                    `manufacturer_id` = '" . addslashes($_POST['manufacturer_id']) . "';";
    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        Message::setMessage('Výrobca bol úspešne upravený.', 0);

        // posielanie loga
        if (isset($_FILES['logo']) and $_FILES['logo']['name'] <> '') {

            $query = 'SELECT logo FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 AND manufacturer_id = ' . $_POST['manufacturer_id'] . ';';
            if ($result = mysql_query($query)) {
                $row = mysql_fetch_object($result);
                unlink('../images/manufacturers/thumbnail/' . $row->logo);
                unlink('../images/manufacturers/preview/' . $row->logo);
                unlink('../images/manufacturers/original/' . $row->logo);
            }

            require_once('../shared/classes/class.image.php');
            $image = new abeautifulsite\SimpleImage();

            $ext = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
            $name = str_pad($_POST['manufacturer_id'], 4, '0', STR_PAD_LEFT) . '-logo-' . String::SEOFriendlyText($_POST[strtolower($val) . '_name']) . '.' . $ext;
            $thumb_dm = explode(',', THUMBS_DIMENSIONS);
            $preview_dm = explode(',', PREVIEW_DIMENSIONS);

            try {
                $image->load($_FILES['logo']['tmp_name'])->save('../images/manufacturers/original/' . $name)->best_fit($preview_dm[0], $preview_dm[1])->save('../images/manufacturers/preview/' . $name)->best_fit($thumb_dm[0], $thumb_dm[1])->save('../images/manufacturers/thumbnail/' . $name);
            }
            catch (Exception $e) {
                Message::setMessage('Chyba nahrávania loga: ' . $e->getMessage(), 2);
            }
            $q = 'UPDATE ' . TABLE_PREFIX . 'manufacturer SET logo = "' . $name . '" WHERE 1 AND manufacturer_id = ' . $_POST['manufacturer_id'] . ';';
            if(!$r = mysql_query($q))
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
        // posielanie logo END

        // posielanie pozadie loga
        if (isset($_FILES['logo_background']) and $_FILES['logo_background']['name'] <> '') {

            $query = 'SELECT logo_background FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 AND manufacturer_id = ' . $_POST['manufacturer_id'] . ';';
            if ($result = mysql_query($query)) {
                $row = mysql_fetch_object($result);
                unlink('../images/manufacturers/thumbnail/' . $row->logo_background);
                unlink('../images/manufacturers/preview/' . $row->logo_background);
                unlink('../images/manufacturers/original/' . $row->logo_background);
            }

            require_once('../shared/classes/class.image.php');
            $image = new abeautifulsite\SimpleImage();

            $ext = pathinfo($_FILES['logo_background']['name'], PATHINFO_EXTENSION);
            $name = str_pad($_POST['manufacturer_id'], 4, '0', STR_PAD_LEFT) . '-logo_bg-' . String::SEOFriendlyText($_POST[strtolower($val) . '_name']) . '.' . $ext;
            $thumb_dm = explode(',', THUMBS_DIMENSIONS);
            $preview_dm = explode(',', PREVIEW_DIMENSIONS);

            try {
                $image->load($_FILES['logo_background']['tmp_name'])->save('../images/manufacturers/original/' . $name)->best_fit($preview_dm[0], $preview_dm[1])->save('../images/manufacturers/preview/' . $name)->best_fit($thumb_dm[0], $thumb_dm[1])->save('../images/manufacturers/thumbnail/' . $name);
            }
            catch (Exception $e) {
                Message::setMessage('Chyba nahrávania pozadia loga: ' . $e->getMessage(), 2);
            }
            $q = 'UPDATE ' . TABLE_PREFIX . 'manufacturer SET logo_background = "' . $name . '" WHERE 1 AND manufacturer_id = ' . $_POST['manufacturer_id'] . ';';
            if(!$r = mysql_query($q))
                echo 'Error (' . mysql_errno() . '): ' . mysql_error();
        }
        // posielanie pozadie loga END

        header("Location:./index.php?module=eshop_manufacturers&eshop=1&action=list");
        exit;
    } else {
        Message::setMessage('Výrobca nebol upravený.', 2);
        die(mysql_error());
    }
    mysql_free_result($ResultA);
}



if ($_GET['delete'] == 1) {

    $query = 'SELECT logo FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 AND manufacturer_id = ' . $_GET['manufacturer_id'] . ';';
    if ($result = mysql_query($query)) {
        $row = mysql_fetch_object($result);
        unlink('../images/manufacturers/thumbnail/' . $row->logo);
        unlink('../images/manufacturers/preview/' . $row->logo);
        unlink('../images/manufacturers/original/' . $row->logo);
    }
    $query = 'SELECT logo_background FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 AND manufacturer_id = ' . $_GET['manufacturer_id'] . ';';
    if ($result = mysql_query($query)) {
        $row = mysql_fetch_object($result);
        unlink('../images/manufacturers/thumbnail/' . $row->logo_background);
        unlink('../images/manufacturers/preview/' . $row->logo_background);
        unlink('../images/manufacturers/original/' . $row->logo_background);
    }

    $sql_str = "
			DELETE FROM
				" . TABLE_PREFIX . "manufacturer
			WHERE
				1
			AND
				manufacturer_id = '" . addslashes($_GET['manufacturer_id']) . "'
				;";

    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        Message::setMessage('Výrobca bol úspešne odstránený.', 2);
        header("Location:./index.php?module=eshop_manufacturers&eshop=1&action=list");
        exit;
    } else {
        die(mysql_error());
    }
    mysql_free_result($ResultA);
}


if (!isset($_GET['action'])) {
    header("Location:./index.php?module=eshop_manufacturers&eshop=1&action=list");
    exit;
}

if (isset($_GET['manufacturer_id']) and $_GET['action'] == "show") {

    $sql_str = 'SELECT * FROM ' . TABLE_PREFIX . 'manufacturer
            WHERE 1
            AND manufacturer_id = "' . addslashes($_GET['manufacturer_id']) . '";';

    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        if (mysql_num_rows($ResultA) == 1) {
            $line = mysql_fetch_assoc($ResultA);
        }
    } else {
        die(mysql_error());
    }
    mysql_free_result($ResultA);
}
?>
<?php
switch ($_GET['action']) {
    case "list": {
            ?>
            <div id="leftMenu">
                <h2>Výrobcovia</h2>
                <p>V tejto sekcii môžte definovať a spravovať výrobcov vaších produktov.</p>
                <ul class="side-menu">
                    <li><a<?= ($_GET['module'] == 'eshop_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_product">Produkty</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_gallery_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_gallery_product&amp;eshop=1">Fotogaléria produktov</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_orders' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_orders&amp;eshop=1">Objednávky</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_manufacturers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_manufacturers&amp;eshop=1">Výrobcovia</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_delivery_payment' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_delivery_payment&amp;eshop=1">Platba &amp; doručenie</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_supliers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_supliers&amp;eshop=1">Dodávatelia</a></li>
                </ul>
                <div id="submenu">
                    <a class="addNew" href="./index.php?module=eshop_manufacturers&eshop=1&action=show">Pridať nového <br />výrobcu</a>
                </div>
            </div>
            <div id="moduleContent">
                <h1>Výrobca</h1>
                <table border="0" cellspacing="0" cellpadding="2" class="tablelist item-list" summary="">
                    <tr>
                        <th>Výrobca</th>
                        <th>logo</th>
                        <th>pozadie loga</th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php
                    $sql = 'SELECT * FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 ORDER BY sorter, sk_name ASC;';
                    $re1 = @mysql_query($sql);
                    if ($re1) {
                        $_parny = false;
                        while ($line = @mysql_fetch_assoc($re1)) {
                            print '
						<tr class="' . ((!$_parny) ? "style1" : "style2") . '">';
                            ?>
                            <td>
                                <a href="./index.php?module=eshop_manufacturers&eshop=1&action=show&amp;manufacturer_id=<?= $line[manufacturer_id] ?>"><?= $line[sk_name] ?></a>
                            </td>
                            <td><?= (!empty($line['logo']) ? 'áno' : '' ); ?></td>
                            <td><?= (!empty($line['logo_background']) ? 'áno' : '' ); ?></td>
                            <td class="actions">
                                
                                <a class="sorter-link" href="javascript:void(0);" onclick="javascript:sortItemM(<?= $line['manufacturer_id']; ?>);">Poradie</a>
                                <a href="./index.php?module=eshop_manufacturers&eshop=1&action=show&amp;manufacturer_id=<?= $line[manufacturer_id] ?>">Upraviť</a>
                                <?
                                if(getProductNumberByManufacturer($line['manufacturer_id']) > 0) {
                                    ?>
                                    <span style="text-align: right; display: inline-block; width: 40px; padding-right: 10px;"><?= getProductNumberByManufacturer($line['manufacturer_id']); ?></span>
                                    <?
                                }
                                else {
                                    ?>
                                    <a style="" href="./index.php?module=eshop_manufacturers&eshop=1&delete=1&amp;manufacturer_id=<?= $line[manufacturer_id] ?>">Odstrániť</a>
                                    <?
                                }
                                ?>
                            </td>
                            </tr>
                            <?php
                            $_parny = !$_parny;
                        }
                    } else {
                        print mysql_error();
                    }
                    @mysql_free_result($re1);
                    ?>
                    <tr><th colspan="4">&nbsp;</th></tr>
                </table>
            </div>
            <script type="text/javascript">

                function sortItemM(itemId) { // používané
                    var w = window.open('modules/_eshop_manufacturer_sort.php?id=' + itemId, 'sortItemC', 'width=400,height=240');
                    if (w) {
                        w.focus();
                    }
                }
            </script>

            <?php
            
        };
        break;
    case "show": {
            ?>
            <div id="leftMenu">
                <h2>Výrobcovia</h2>
                <p>V tejto sekcii môžte definovať a spravovať výrobcov vaších produktov.</p>
                <ul class="side-menu">
                    <li><a<?= ($_GET['module'] == 'eshop_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_product">Produkty</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_gallery_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_gallery_product&amp;eshop=1">Fotogaléria produktov</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_orders' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_orders&amp;eshop=1">Objednávky</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_manufacturers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_manufacturers&amp;eshop=1">Výrobcovia</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_delivery_payment' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_delivery_payment&amp;eshop=1">Platba &amp; doručenie</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_supliers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_supliers&amp;eshop=1">Dodávatelia</a></li>
                </ul>
                <div id="submenu">
                    <a id="edit" href="./index.php?module=eshop_manufacturers">Zobraziť všetkých<br />výrobcov</a>
                    <a class="addNew" href="./index.php?module=eshop_manufacturers&eshop=1&action=show">Pridať nového <br />výrobcu</a>
                </div>
            </div>
            <div id="moduleContent">
                <h1>
                    <?
                    if (!empty($_GET['manufacturer_id'])) {
                        echo 'Úprava výrobcu';
                    } else {
                        echo 'Pridať výrobcu';
                    }
                    ?>
                </h1>
                <form method="post" enctype="multipart/form-data" name="pridat_uzivatela" id="pridat_uzivatela" onsubmit="return overForm(this);" action="">
                    <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">
                        <tr>
                            <th colspan="3">&nbsp;</th>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <input type="hidden" id="manufacturer_id" name="manufacturer_id" value="<?= $line['manufacturer_id'] ?>" />
                        <?
                        foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                            print '
							<tr >
								<td>Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>
								<td><input type="text" class="w201px" id="name"  name="' . strtolower($val) . '_name" value="' . $line[$val . '_name'] . '" /></td>
							</tr>';
                        }
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>

                        <tr>
                            <td>logo:</td>
                            <td>
                                <?
                                if (is_file("../images/manufacturers/preview/" . $line['logo'])) {
                                    ?>
                                    <div>
                                        <a href="../images/manufacturers/preview/<?= $line['logo']; ?>" class="" title="<?= $line['logo'] ?>" border="0">
                                            <img src="../images/manufacturers/preview/<?= $line['logo']; ?>" alt="<?= $line['logo']; ?>" border="0" width="150" />
                                        </a>
                                    </div>
                                    <div>
                                        <label><input name="delete_logo" type="checkbox" value="1" /> Odstrániť aktuálne logo</label>
                                    </div>
                                    <?
                                }
                                ?>
                                <div class="logo-actions">
                                    <input name="logo" type="file" id="logo" />
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>obrázok na pozadie:</td>
                            <td>
                                <?
                                if (is_file("../images/manufacturers/preview/" . $line['logo_background'])) {
                                    ?>
                                    <div>
                                        <a href="../images/manufacturers/preview/<?= $line['logo_background']; ?>" class="" title="<?= $line['logo_background'] ?>" border="0">
                                            <img src="../images/manufacturers/preview/<?= $line['logo_background']; ?>" alt="<?= $line['logo_background']; ?>" border="0" width="150" />
                                        </a>
                                    </div>
                                    <div>
                                        <label><input name="delete_logo_background" type="checkbox" value="1" /> odstrániť foto pozadie loga</label>
                                    </div>
                                    
                                    <?
                                }
                                ?>
                                <div class="logo-background-actions">
                                    <input name="logo_background" type="file" id="logo_background" />
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input name="image" type="submit"value="<?= (!empty($line['manufacturer_id'])) ? "Uložiť" : "Pridať" ?>" />
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
            <?php
        };
        break;
}
?>
