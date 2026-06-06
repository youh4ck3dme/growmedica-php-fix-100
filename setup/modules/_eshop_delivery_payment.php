<?
if (!$user->isAdmin()) {
    header("Location:" . ROOTDIR . "/setup/index.php?module=eshop_orders");
    exit;
}
require_once('../shared/classes/class.eshop.php');

/*

  CREATE TABLE IF NOT EXISTS `boutiqueivaneli_delivery_payment_rel` (
  `rel_id` int(11) NOT NULL AUTO_INCREMENT,
  `delivery_type_id` int(11) NOT NULL,
  `payment_type_id` int(11) NOT NULL,
  PRIMARY KEY (`rel_id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

 */
?>
<div id="leftMenu">
    <h2>Spôsoby dopravy a platby</h2>
    <p>V tejto sekcii sa spravujú spôsoby dopravy a platby za objednávky.</p>
    <ul class="side-menu">
        <li><a<?= ($_GET['module'] == 'eshop_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_product">Produkty</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_gallery_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_gallery_product&amp;eshop=1">Fotogaléria produktov</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_orders' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_orders&amp;eshop=1">Objednávky</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_manufacturers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_manufacturers&amp;eshop=1">Výrobcovia</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_delivery_payment' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_delivery_payment&amp;eshop=1">Platba &amp; doručenie</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_supliers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_supliers&amp;eshop=1">Dodávatelia</a></li>
    </ul>
    <div id="submenu">
        <a class="addNew" href="./index.php?module=eshop_delivery_payment&eshop=1&action=new_delivery">Pridať nový <br />spôsob dopravy</a>
        <a class="addNew" href="./index.php?module=eshop_delivery_payment&eshop=1&action=new_payment">Pridať nový <br />spôsob platby</a>
    </div>
</div>
<div id="moduleContent">
    <?
    switch ($_REQUEST['action']) {
        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */

        case 'new_delivery':

            if ($_POST['send_y'] == 'y') {
                $queryInsert = 'INSERT INTO ' . TABLE_PREFIX . 'delivery_type
                                    (name, heureka_delivery_type_id, min_price, max_price, default_choice, price_eur)
                                VALUES
                                    ("' . $_POST['name_input'] . '", ' . $_POST['heureka_delivery_type'] . ', ' . $_POST['min_price_input'] . ', ' . $_POST['max_price_input'] . ', "' . $_POST['default_choice_input'] . '", ' . $_POST['price_eur_input'] . ')';
                if (mysql_query($queryInsert)) {
                    $new_delivery_id = mysql_insert_id();

                    foreach ($_POST['payment_rel_select'] as $payment_rel_select) {
                        $queryInsert2 = 'INSERT INTO ' . TABLE_PREFIX . 'delivery_payment_rel (delivery_type_id,
						                                                                   payment_type_id) VALUES (' . $new_delivery_id . ', ' . $payment_rel_select . ')';
                        mysql_query($queryInsert2);
                    }
                    Message::setMessage('Spôsob dopravy bol úspešne vložený.', 0);
                } else {
                    Message::setMessage('Spôsob dopravy nebol vložený.', 2);
                }

                header("Location: ./index.php?module=eshop_delivery_payment&eshop=1");
            }
            ?>
            <h1>Pridať nový spôsob dopravy</h1>
            <form method="post" action="" >
                <table class="tableform">
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <label for="name_input">Názov spôsobu dopravy:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="name_input" id="name_input" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="heureka_delivery_type">Heureka - podporovaná dopravca:</label>
                        </td>
                        <td colspan="2">
                            <select name="heureka_delivery_type" id="heureka_delivery_type">
                                <option value="0"> - </option>
                                <?
                                $queryH = 'SELECT hdt_id, hdt_name FROM ' . TABLE_PREFIX . 'heureka_delivery_type WHERE 1 ORDER BY hdt_name ASC';
                                if ($resultH = mysql_query($queryH)) {
                                    while ($rowH = mysql_fetch_object($resultH)) {
                                        echo '<option value="' . $rowH->hdt_id . '">' . $rowH->hdt_name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_eur_input">Cena dopravy:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="price_eur_input" id="price_eur_input" value="0" /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_input">Minimálny limit hodnoty nákupu:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="min_price_input" id="min_price_input" value="0"  /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_input">Maximálny limit hodnoty nákupu:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="max_price_input" id="max_price_input" value="1000000" /> &euro;
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label for="default_choice_input">Defaultna voľba:</label>
                        </td>
                        <td colspan="2">
                            <input type="checkbox" name="default_choice_input" id="default_choice_input" value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="payment_rel_select">Ktoré spôsoby platby budú závislé od tohto spôsobu dopravy:</label>
                        </td>
                        <td colspan="2">
                            <select name="payment_rel_select[]" id="payment_rel_select" multiple style="height:100px;">
                                <option value="0"> - </option>
                                <?
                                $query = 'SELECT payment_type_id, name FROM ' . TABLE_PREFIX . 'payment_type WHERE 1 ORDER BY name ASC';
                                if ($result = mysql_query($query)) {
                                    while ($row = mysql_fetch_object($result)) {
                                        echo '<option value="' . $row->payment_type_id . '">' . $row->name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <input type="hidden" name="send_y" value="y" />
                            <input type="submit" name="submit" id="submit" value="Uložiť" />
                        </td>
                    </tr>
                </table>
            </form>
            <?
            break;

        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */

        case 'new_payment':
            if ($_POST['send_y'] == 'y') {
                $queryInsert = "INSERT INTO " . TABLE_PREFIX . "payment_type
                                    (name, price, default_choice, price_eur, description, payment_action)
                                VALUES
                                    ('" . $_POST['name_input'] . "', " . $_POST['price_input'] . ", '" . $_POST['default_choice_input'] . "', " . $_POST['price_eur_input'] . ", '" . $_POST['description_textarea'] . "', '" . $_POST['payment_action'] . "');";
                if (mysql_query($queryInsert)) {
                    $new_payment_id = mysql_insert_id();

                    foreach ($_POST['delivery_rel_select'] as $delivery_rel_select) {
                        $queryInsert2 = 'INSERT INTO ' . TABLE_PREFIX . 'delivery_payment_rel (delivery_type_id,
						                                                                   payment_type_id) VALUES (' . $delivery_rel_select . ', ' . $new_payment_id . ')';
                        mysql_query($queryInsert2);
                    }
                    Message::setMessage('Spôsob platby bol úspešne vložený.', 0);
                } else {
                    Message::setMessage('Spôsob dopravy nebol vložený.', 2);
                }

                header("Location: ./index.php?module=eshop_delivery_payment&eshop=1");
            }
            ?>
            <h1>Pridať nový spôsob platby</h1>
            <form method="post" action="" >
                <table class="tableform">
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <label for="name_input">Názov spôsobu platby:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="name_input" id="name_input" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_eur_input">Cena platby:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="price_eur_input" id="price_eur_input" value="0" /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_input">Limit hodnoty nákupu:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="price_input" id="price_input" value="0" /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="default_choice_input">Defaultna voľba:</label>
                        </td>
                        <td colspan="2">
                            <input type="checkbox" name="default_choice_input" id="default_choice_input" value="1" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="default_choice_input">Platforma platby:</label>
                        </td>
                        <td colspan="2">
                            <select name="payment_action">
                                <option value="credit">Platba cez kreditnú kartu / kreditný systém</option>
                                <option value="cash">Platba v hotovosti / Platba na účet</option>
                                <option value="gpwebpay">GP Webpay</option>
                            </selecT>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="delivery_rel_select">Ktoré spôsoby dopravy budú nutné pre tento spôsob platby:</label>
                        </td>
                        <td colspan="2">
                            <select name="delivery_rel_select[]" id="delivery_rel_select" multiple style="height:100px;">
                                <option value="0"> - </option>
                                <?
                                $query = 'SELECT delivery_type_id, name FROM ' . TABLE_PREFIX . 'delivery_type WHERE 1 ORDER BY name ASC';
                                if ($result = mysql_query($query)) {
                                    while ($row = mysql_fetch_object($result)) {
                                        echo '<option value="' . $row->delivery_type_id . '">' . $row->name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="description_textarea">Popis:</label>
                        </td>
                        <td colspan="2">
                            <textarea name="description_textarea" id="description_textarea"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <input type="hidden" name="send_y" value="y" />
                            <input type="submit" name="submit" id="submit" value="Uložiť" />
                        </td>
                    </tr>
                </table>
            </form>
            <?
            break;

        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */

        case 'edit_delivery':
            if ($_POST['send_y'] == 'y') {
                $queryInsert = 'UPDATE ' . TABLE_PREFIX . 'delivery_type SET name = "' . $_POST['name_input'] . '",
                                    heureka_delivery_type_id = ' . $_POST['heureka_delivery_type'] . ',
                                    min_price = ' . $_POST['min_price_input'] . ',
                                    max_price = ' . $_POST['max_price_input'] . ',
                                    default_choice = "' . $_POST['default_choice_input'] . '",
                                    price_eur = ' . $_POST['price_eur_input'] . '
				WHERE delivery_type_id = "' . $_REQUEST['delivery_type_id'] . '"';
                $queryInsert = sprintf($queryInsert, $_REQUEST['delivery_type_id']);
                if (mysql_query($queryInsert)) {
                    $new_delivery_id = $_REQUEST['delivery_type_id'];

                    $deleteRel = 'DELETE FROM ' . TABLE_PREFIX . 'delivery_payment_rel WHERE delivery_type_id = %d';
                    $deleteRel = sprintf($deleteRel, $_REQUEST['delivery_type_id']);
                    mysql_query($deleteRel);

                    foreach ($_POST['payment_rel_select'] as $payment_rel_select) {
                        $queryInsert2 = 'INSERT INTO ' . TABLE_PREFIX . 'delivery_payment_rel (delivery_type_id,
						                                                                   payment_type_id) VALUES (' . $new_delivery_id . ', ' . $payment_rel_select . ')';
                        mysql_query($queryInsert2);
                    }
                    Message::setMessage('Spôsob dopravy bol úspešne upravený.', 0);
                } else {
                    Message::setMessage('Spôsob dopravy nebol upravený.', 2);
                }

                header("Location: ./index.php?module=eshop_delivery_payment&eshop=1");
            }

            $querySelect = 'SELECT * FROM ' . TABLE_PREFIX . 'delivery_type WHERE delivery_type_id = %d';
            $querySelect = sprintf($querySelect, $_REQUEST['delivery_type_id']);
            if ($result = mysql_query($querySelect)) {
                $row = mysql_fetch_object($result);
            }
            ?>
            <h1>Upraviť spôsob dopravy</h1>
            <form method="post" action="" >
                <table class="tableform">
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <label for="name_input">Názov spôsobu dopravy:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="name_input" id="name_input" value="<?= $row->name; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="heureka_delivery_type">Heureka - podporovaná dopravca:</label>
                        </td>
                        <td colspan="2">
                            <select name="heureka_delivery_type" id="heureka_delivery_type">
                                <option value="0"> - </option>
                                <?
                                $queryH = 'SELECT hdt_id, hdt_name FROM ' . TABLE_PREFIX . 'heureka_delivery_type WHERE 1 ORDER BY hdt_name ASC';
                                if ($resultH = mysql_query($queryH)) {
                                    while ($rowH = mysql_fetch_object($resultH)) {
                                        echo '<option value="' . $rowH->hdt_id . '"' . ($rowH->hdt_id == $row->heureka_delivery_type_id ? ' selected="selected"' : '') . '>' . $rowH->hdt_name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_eur_input">Cena dopravy:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="price_eur_input" id="price_eur_input" value="<?= $row->price_eur; ?>" /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_input">Minimálny limit hodnoty nákupu:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="min_price_input" id="min_price_input" value="<?= $row->min_price; ?>" /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_input">Maximálny limit hodnoty nákupu:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="max_price_input" id="max_price_input" value="<?= $row->max_price; ?>" /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="default_choice_input">Defaultna voľba:</label>
                        </td>
                        <td colspan="2">
                            <input type="checkbox" name="default_choice_input" id="default_choice_input" value="1" <?= (($row->default_choice == '1') ? 'checked' : ''); ?> />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="payment_rel_select">Ktoré spôsoby platby budú závislé od tohto spôsobu dopravy:</label>
                        </td>
                        <td colspan="2">
                            <select name="payment_rel_select[]" id="payment_rel_select" multiple style="height:100px;">
                                <option value="0"> - </option>
                                <?
                                $queryRel = 'SELECT * FROM ' . TABLE_PREFIX . 'delivery_payment_rel WHERE delivery_type_id = %d';
                                $queryRel = sprintf($queryRel, $_REQUEST['delivery_type_id']);
                                if ($resultRel = mysql_query($queryRel)) {
                                    while ($rowRel = mysql_fetch_object($resultRel)) {
                                        $rel[] = $rowRel->payment_type_id;
                                    }
                                }

                                $query = 'SELECT payment_type_id, name FROM ' . TABLE_PREFIX . 'payment_type WHERE 1 ORDER BY name ASC';
                                if ($result = mysql_query($query)) {
                                    while ($row = mysql_fetch_object($result)) {
                                        echo '<option value="' . $row->payment_type_id . '" ' . (in_array($row->payment_type_id, $rel) ? 'selected' : '') . '>' . $row->name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <input type="hidden" name="send_y" value="y" />
                            <input type="submit" name="submit" id="submit" value="Uložiť" />
                        </td>
                    </tr>
                </table>
            </form>
            <?
            break;

        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */

        case 'edit_payment':
            if ($_POST['send_y'] == 'y') {
                $queryInsert = 'UPDATE ' . TABLE_PREFIX . 'payment_type SET name = "' . $_POST['name_input'] . '",
                                    price = ' . $_POST['price_input'] . ',
                                    default_choice = "' . $_POST['default_choice_input'] . '",
                                    price_eur = ' . $_POST['price_eur_input'] . ',
                                    description = "' . $_POST['description_textarea'] . '",
                                    payment_action = "' . $_POST['payment_action'] . '"
				WHERE payment_type_id = %d';
                $queryInsert = sprintf($queryInsert, $_REQUEST['payment_type_id']);
                if (mysql_query($queryInsert)) {
                    $new_payment_id = $_REQUEST['payment_type_id'];

                    $deleteRel = 'DELETE FROM ' . TABLE_PREFIX . 'delivery_payment_rel WHERE payment_type_id = %d';
                    $deleteRel = sprintf($deleteRel, $_REQUEST['payment_type_id']);
                    mysql_query($deleteRel);

                    foreach ($_POST['delivery_rel_select'] as $delivery_rel_select) {
                        $queryInsert2 = 'INSERT INTO ' . TABLE_PREFIX . 'delivery_payment_rel (delivery_type_id,
						                                                                   payment_type_id) VALUES (' . $delivery_rel_select . ', ' . $new_payment_id . ')';
                        mysql_query($queryInsert2);
                    }
                    Message::setMessage('Spôsob platby bol úspešne upravený.', 0);
                } else {
                    Message::setMessage('Spôsob dopravy nebol upravený.', 2);
                }

                header("Location: ./index.php?module=eshop_delivery_payment&eshop=1");
            }

            $querySelect = 'SELECT * FROM ' . TABLE_PREFIX . 'payment_type WHERE payment_type_id = %d';
            $querySelect = sprintf($querySelect, $_REQUEST['payment_type_id']);
            if ($result = mysql_query($querySelect)) {
                $row = mysql_fetch_object($result);
            }
            ?>
            <h1>Upraviť spôsob platby</h1>
            <form method="post" action="">
                <table class="tableform">
                    <tr>
                        <th colspan="3">&nbsp;</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td>
                            <label for="name_input">Názov spôsobu platby:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="name_input" id="name_input" value="<?= $row->name; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_eur_input">Cena platby:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="price_eur_input" id="price_eur_input" value="<?= $row->price_eur; ?>" /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="price_input">Limit hodnoty nákupu:</label>
                        </td>
                        <td colspan="2">
                            <input type="text" name="price_input" id="price_input" value="<?= $row->price; ?>" /> &euro;
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="default_choice_input">Defaultna voľba:</label>
                        </td>
                        <td colspan="2">
                            <input type="checkbox" name="default_choice_input" id="default_choice_input" value="1" <?= (($row->default_choice == '1') ? 'checked' : ''); ?> />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="default_choice_input">Platforma platby:</label>
                        </td>
                        <td colspan="2">
                            <select name="payment_action">
                                <option value="credit" <?= (($row->payment_action == 'credit') ? 'selected' : ''); ?>>Platba cez kreditnú kartu / kreditný systém</option>
                                <option value="cash" <?= (($row->payment_action == 'cash') ? 'selected' : ''); ?>>Platba v hotovosti / Platba na účet</option>
                                <option value="gpwebpay" <?= (($row->payment_action == 'gpwebpay') ? 'selected' : ''); ?>>GP Webpay</option>
                            </selecT>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <label for="delivery_rel_select">Ktoré spôsoby dopravy budú nutné pre tento spôsob platby:</label>
                        </td>
                        <td colspan="2">
                            <select name="delivery_rel_select[]" id="delivery_rel_select" multiple style="height:100px;">
                                <option value="0"> - </option>
                                <?
                                $queryRel = 'SELECT * FROM ' . TABLE_PREFIX . 'delivery_payment_rel WHERE payment_type_id = %d';
                                $queryRel = sprintf($queryRel, $_REQUEST['payment_type_id']);
                                if ($resultRel = mysql_query($queryRel)) {
                                    while ($rowRel = mysql_fetch_object($resultRel)) {
                                        $rel[] = $rowRel->delivery_type_id;
                                    }
                                }

                                $queryOption = 'SELECT delivery_type_id, name FROM ' . TABLE_PREFIX . 'delivery_type WHERE 1 ORDER BY name ASC';
                                if ($resultOption = mysql_query($queryOption)) {
                                    while ($rowOption = mysql_fetch_object($resultOption)) {
                                        echo '<option value="' . $rowOption->delivery_type_id . '" ' . (in_array($rowOption->delivery_type_id, $rel) ? 'selected' : '') . '>' . $rowOption->name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="description_textarea">Popis:</label>
                        </td>
                        <td colspan="2">
                            <textarea name="description_textarea" id="description_textarea"><?= $row->description; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            <input type="hidden" name="send_y" value="y" />
                            <input type="submit" name="submit" id="submit" value="Uložiť" />
                        </td>
                    </tr>
                </table>
            </form>
            <?
            break;

        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */

        case 'delete_delivery':

            $delete = 'DELETE FROM ' . TABLE_PREFIX . 'delivery_type WHERE delivery_type_id = %d';
            $delete = sprintf($delete, $_REQUEST['delivery_type_id']);
            mysql_query($delete);

            $deleteRel = 'DELETE FROM ' . TABLE_PREFIX . 'delivery_payment_rel WHERE delivery_type_id = %d';
            $deleteRel = sprintf($deleteRel, $_REQUEST['delivery_type_id']);
            mysql_query($deleteRel);

            Message::setMessage('Spôsob dopravy bol úspešne odstránený.', 0);
            header("Location: ./index.php?module=eshop_delivery_payment&eshop=1");

            break;


        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */

        case 'delete_payment':

            $delete = 'DELETE FROM ' . TABLE_PREFIX . 'payment_type WHERE payment_type_id = %d';
            $delete = sprintf($delete, $_REQUEST['payment_type_id']);
            mysql_query($delete);

            $deleteRel = 'DELETE FROM ' . TABLE_PREFIX . 'delivery_payment_rel WHERE payment_type_id = %d';
            $deleteRel = sprintf($deleteRel, $_REQUEST['delivery_type_id']);
            mysql_query($deleteRel);

            Message::setMessage('Spôsob platby bol úspešne odstránený.', 0);
            header("Location: ./index.php?module=eshop_delivery_payment&eshop=1");

            break;

        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */
        /*         * ***************************************************************************************************************************************** */

        default:
            ?>
            <h1>Spôsoby dopravy</h1>
            <table border="0" cellspacing="0" cellpadding="2" class="tablelist item-list" summary="">

                <tr>
                    <th>ID</th>
                    <th>Názov</th>
                    <th>Cena dopravy</th>
                    <th>Limit hodnoty nákupu</th>
                    <th>Defaultna voľba</th>
                    <th>&nbsp;</th>
                </tr>
                <?php
                $sql = "SELECT * FROM " . TABLE_PREFIX . "delivery_type AS d LEFT JOIN " . TABLE_PREFIX . "heureka_delivery_type AS h ON(h.hdt_id = d.heureka_delivery_type_id) WHERE 1";
                $re1 = @mysql_query($sql);
                if ($re1) {
                    $_parny = false;
                    while ($line = @mysql_fetch_assoc($re1)) {
                        echo '<tr class="' . ((!$_parny) ? "style1" : "style2") . '">';
                        echo '<td>'.$line['delivery_type_id'].'</td>';
                        echo '<td><a href="./index.php?module=eshop_delivery_payment&eshop=1&action=edit_delivery&amp;delivery_type_id=' . $line['delivery_type_id'] . '">' . $line['name'] . ' (' . $line['hdt_name'] . ')</a></td>';
                        echo '<td>' . number_format($line['price_eur'], 2, ',', ' ') . ' &euro;</td>';
                        echo '<td>' . number_format($line['min_price'], 2, ',', ' ') . ' - ' . number_format($line['max_price'], 2, ',', ' ') . ' &euro;</td>';
                        echo '<td>' . (($line['default_choice'] == '1') ? '<input type="checkbox" disabled checked="checked" />' : '<input type="checkbox" disabled />') . '</td>';
                        echo '<td class="actions"><a href="./index.php?module=eshop_delivery_payment&eshop=1&action=edit_delivery&amp;delivery_type_id=' . $line['delivery_type_id'] . '">Upraviť</a>
		                                             	<a href="./index.php?module=eshop_delivery_payment&eshop=1&action=delete_delivery&amp;delivery_type_id=' . $line['delivery_type_id'] . '">Odstrániť</a> </td>';
                        echo '</tr>';

                        $_parny = !$_parny;
                    }
                } else {
                    print mysql_error();
                }
                @mysql_free_result($re1);
                ?>
                <tr><th colspan="6">&nbsp;</th></tr>
            </table>
            <br/><br/><br/><br/>
            <h1>Spôsoby platby</h1>
            <table border="0" cellspacing="0" cellpadding="2" class="tablelist item-list" summary="">

                <tr>
                    <th>Názov</th>
                    <th>Cena platby</th>
                    <th>Limit hodnoty nákupu</th>
                    <th>Defaultna voľba</th>
                    <th>Poznámka</th>
                    <th>&nbsp;</th>
                </tr>
                <?php
                $sql = "SELECT * FROM " . TABLE_PREFIX . "payment_type WHERE 1";
                $re1 = @mysql_query($sql);
                if ($re1) {
                    $_parny = false;
                    while ($line = @mysql_fetch_assoc($re1)) {
                        echo '<tr class="' . ((!$_parny) ? "style1" : "style2") . '">';
                        echo '<td><a href="./index.php?module=eshop_delivery_payment&eshop=1&action=edit_payment&amp;payment_type_id=' . $line['payment_type_id'] . '">' . $line['name'] . '</a></td>';
                        echo '<td>' . number_format($line['price_eur'], 2, ',', ' ') . ' &euro;</td>';
                        echo '<td>' . number_format($line['price'], 2, ',', ' ') . ' &euro;</td>';
                        echo '<td>' . (($line['default_choice'] == '1') ? '<input type="checkbox" disabled checked="checked" />' : '<input type="checkbox" disabled />') . '</td>';
                        echo '<td>' . $line['description'] . '</td>';
                        echo '<td class="actions"><a href="./index.php?module=eshop_delivery_payment&eshop=1&action=edit_payment&amp;payment_type_id=' . $line['payment_type_id'] . '">Upraviť</a>
		                                             	<a href="./index.php?module=eshop_delivery_payment&eshop=1&action=delete_payment&amp;payment_type_id=' . $line['payment_type_id'] . '">Odstrániť</a></td>';
                        echo '</tr>';

                        $_parny = !$_parny;
                    }
                } else {
                    print mysql_error();
                }
                @mysql_free_result($re1);
                ?>
                <tr><th colspan="6">&nbsp;</th></tr>
            </table>

            <?
            break;
    }
    ?>
</div>