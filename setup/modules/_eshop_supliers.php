<?
require_once('../shared/classes/class.eshop.php');
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
if (isset($_POST['suplier_id']) and ! is_numeric($_POST['suplier_id'])) {
    /*
    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
        $queryInclude1 .= "`" . strtolower($val) . "_name`,";
        $queryInclude2 .= "'" . $_POST[strtolower($val) . '_name'] . "', ";
    }*/
    $queryInclude1 .= "`suplier`";
    $queryInclude2 .= "'" . $_POST['suplier'] . "' ";

    $sql_str = "INSERT INTO `" . TABLE_PREFIX . "product_suplier`
                    (" . $queryInclude1 . ")
		VALUES (" . $queryInclude2 . "'');";

    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        Message::setMessage('Dodávateľ bol úspešne vložený.', 0);
        header("Location: ./index.php?module=eshop_supliers&eshop=1&action=list");
        exit;
    } else {
        Message::setMessage('Dodávateľ nebol vložený.', 2);
        die(mysql_error());
    }
    mysql_free_result($ResultA);
}

if (isset($_POST['suplier_id'])) {
    /*
    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
        $queryInclude .= "`" . strtolower($val) . "_name` = '" . html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8") . "', ";
    }
    */
    $queryInclude .= "`suplier` = '" . html_entity_decode($_POST['suplier'], ENT_QUOTES, "UTF-8") . "' ";

    $sql_str = "UPDATE `" . TABLE_PREFIX . "product_suplier`
		SET
                    " . $queryInclude . "
		WHERE
                    1
		AND
                    `suplier_id` = '" . addslashes($_POST['suplier_id']) . "';";
    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        Message::setMessage('Dodávateľ bol úspešne upravený.', 0);
        header("Location:./index.php?module=eshop_supliers&eshop=1&action=list");
        exit;
    } else {
        Message::setMessage('Dodávateľ nebol upravený.', 2);
        die(mysql_error());
    }
    mysql_free_result($ResultA);
}

if ($_GET['delete'] == 1) {

    $sql_str = "
			DELETE FROM
				" . TABLE_PREFIX . "product_suplier
			WHERE
				1
			AND
				suplier_id = '" . addslashes($_GET['suplier_id']) . "'
				;";

    $ResultA = mysql_query($sql_str);
    if ($ResultA) {
        Message::setMessage('Dodávateľ bol úspešne odstránený.', 2);
        header("Location:./index.php?module=eshop_supliers&eshop=1&action=list");
        exit;
    } else {
        die(mysql_error());
    }
    mysql_free_result($ResultA);
}


if (!isset($_GET['action'])) {
    header("Location:./index.php?module=eshop_supliers&eshop=1&action=list");
    exit;
}

if (isset($_GET['suplier_id']) and $_GET['action'] == "show") {

    $sql_str = "SELECT * FROM " . TABLE_PREFIX . "product_suplier
		WHERE
                    1
		AND
                    suplier_id = '" . addslashes($_GET['suplier_id']) . "';";

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
                <h2>Dodávatelia</h2>
                <p>V tejto sekcii môžte definovať a spravovať dodávateľov vaších produktov.</p>
                <ul class="side-menu">
                    <li><a<?= ($_GET['module'] == 'eshop_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_product">Produkty</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_gallery_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_gallery_product&amp;eshop=1">Fotogaléria produktov</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_orders' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_orders&amp;eshop=1">Objednávky</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_manufacturers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_manufacturers&amp;eshop=1">Výrobcovia</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_delivery_payment' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_delivery_payment&amp;eshop=1">Platba &amp; doručenie</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_supliers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_supliers&amp;eshop=1">Dodávatelia</a></li>
                </ul>
                <div id="submenu">
                    <a class="addNew" href="./index.php?module=eshop_supliers&eshop=1&action=show">Pridať nového <br />dodávateľa</a>
                </div>
            </div>
            <div id="moduleContent">
                <h1>Dodávateľ</h1>
                <table border="0" cellspacing="0" cellpadding="2" class="tablelist item-list" summary="">

                    <tr>
                        <th>Dodávateľ</th>
                        <th>&nbsp;</th>
                    </tr>
                    <?php
                    $sql = "SELECT * FROM " . TABLE_PREFIX . "product_suplier WHERE 1";
                    $re1 = @mysql_query($sql);
                    if ($re1) {
                        $_parny = false;
                        while ($line = @mysql_fetch_assoc($re1)) {
                            print '
						<tr class="' . ((!$_parny) ? "style1" : "style2") . '">';
                            ?>
                            <td>
                                <a href="./index.php?module=eshop_supliers&eshop=1&action=show&amp;suplier_id=<?= $line[suplier_id] ?>"><?= $line[suplier] ?></a>
                            </td>
                            <td class="actions">
                                <a href="./index.php?module=eshop_supliers&eshop=1&action=show&amp;suplier_id=<?= $line[suplier_id] ?>">Upraviť</a>
                                <a href="./index.php?module=eshop_supliers&eshop=1&delete=1&amp;suplier_id=<?= $line[suplier_id] ?>">Odstrániť</a>
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
                    <tr><th colspan="3">&nbsp;</th></tr>
                </table>
            </div>

            <?php
        };
        break;
    case "show": {
            ?>
            <div id="leftMenu">
                <h2>Dodávatelia</h2>
                <p>V tejto sekcii môžte definovať a spravovať dodávateľov vaších produktov.</p>
                <ul class="side-menu">
                    <li><a<?= ($_GET['module'] == 'eshop_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_product">Produkty</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_gallery_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_gallery_product&amp;eshop=1">Fotogaléria produktov</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_orders' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_orders&amp;eshop=1">Objednávky</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_manufacturers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_manufacturers&amp;eshop=1">Výrobcovia</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_delivery_payment' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_delivery_payment&amp;eshop=1">Platba &amp; doručenie</a></li>
                    <li><a<?= ($_GET['module'] == 'eshop_supliers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_supliers&amp;eshop=1">Dodávatelia</a></li>
                </ul>
                <div id="submenu">
                    <a id="edit" href="./index.php?module=eshop_supliers">Zobraziť všetkých<br />dodávateľov</a>
                    <a class="addNew" href="./index.php?module=eshop_supliers&eshop=1&action=show">Pridať nového <br />dodávateľa</a>
                </div>
            </div>
            <div id="moduleContent">
                <h1>
                    <?
                    if (!empty($_GET['manufacturer_id'])) {
                        echo 'Úprava dodávateľa';
                    } else {
                        echo 'Pridať dodávateľa';
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
                        <input type="hidden" id="suplier_id" name="suplier_id" value="<?= $line['suplier_id'] ?>" />
                        <?/*
                        foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                            print '
							<tr >
								<td>Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>
								<td><input type="text" class="w201px" id="name"  name="' . strtolower($val) . '_name" value="' . $line[$val . '_name'] . '" /></td>
							</tr>';
                        }*/
                            print '
                            <tr >
                                <td>Názov</td>
                                <td><input type="text" class="w201px" id="name"  name="suplier" value="' . $line['suplier'] . '" /></td>
                            </tr>';
                        ?>
                        <tr>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                            <td>
                                <input name="image" type="submit"value="<?= (!empty($line['suplier_id'])) ? "Uložiť" : "Pridať" ?>" />
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
