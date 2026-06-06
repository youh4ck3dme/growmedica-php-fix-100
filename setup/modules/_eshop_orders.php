<?
require_once('../shared/classes/class.eshop.php');
Installator::checkIfTableExist();
if ($_GET['deleteall'] == 1) {
    $sql_str = "truncate table " . TABLE_PREFIX . "order;";
    $ResultA = mysql_query($sql_str);
    if ($ResultA) {

    } else {
        print(mysql_error());
    }
    mysql_free_result($ResultA);

    header("Location:./index.php?module=eshop_orders&eshop=1");
    exit;
}
/*
  if (is_numeric($_GET['order_id']) and is_numeric($_GET['state'])) {

  $sql_str = "SELECT " . TABLE_PREFIX . "order
  SET order_state_id = '" . $_GET['state'] . "'
  WHERE 1 AND order_id = '" . $_GET['order_id'] . "';";
  mysql_query($sql_str);

  if ($_GET['state'] == 2) {

  $sql_str = "UPDATE " . TABLE_PREFIX . "order
  SET checked = 1, date_e = NOW()
  WHERE 1 AND order_id = '" . $_GET['order_id'] . "';";
  $ResultA = mysql_query($sql_str);
  if ($ResultA) {

  } else {
  die(mysql_error());
  }
  mysql_free_result($ResultA);
  }

  header("Location:./index.php?module=eshop_orders&eshop=1");
  exit;
  }
 */
if (is_numeric($_GET['order_id']) and is_numeric($_GET['state'])) {
    $sql_str = "UPDATE " . TABLE_PREFIX . "order
		SET order_state_id = '" . $_GET['state'] . "'
		WHERE 1 AND order_id = '" . $_GET['order_id'] . "';";

    mysql_query($sql_str);

    if ($_GET['state'] == '2' OR $_GET['state'] == '6') {
        $sql_str = "UPDATE " . TABLE_PREFIX . "order
                    SET checked=1, date_e=NOW()
                    WHERE 1 AND order_id = '" . $_GET['order_id'] . "';";

        $ResultA = mysql_query($sql_str);
        if (!$ResultA) {
            die(mysql_error());
        }
        mysql_free_result($ResultA);
    }

    /**/
    $string = 'SELECT u.priezvisko AS priezvisko, u.username, u.mail, u.fullname, o.order_id FROM ' . TABLE_PREFIX . 'order AS o
                LEFT JOIN ' . TABLE_PREFIX . 'user AS u USING(user_id)
                WHERE order_id="' . mysql_real_escape_string($_GET['order_id']) . '"';
    //echo $string;
    $result = mysql_query($string);
    $row = mysql_fetch_assoc($result);

    if ($_GET['state'] == '2' OR $_GET['state'] == '3' OR $_GET['state'] == '4' OR $_GET['state'] == '6') { // OR $_GET['state'] == '5'
        if ($_GET['state'] == '2')
            $status = $cTranslator->getTranslation('potvrdená', 0);
        else if ($_GET['state'] == '3')
            $status = $cTranslator->getTranslation('vyskladnená', 0);
        else if ($_GET['state'] == '4')
            $status = $cTranslator->getTranslation('zaplatená', 0);
        //else if ($_GET['state'] == '5')
        //    $status = $cTranslator->getTranslation('vybavená', 0);
        else if ($_GET['state'] == '6')
            $status = $cTranslator->getTranslation('zrušená', 0);

        $body = '<p>' . getContentByLabel('E-mail o stave objednávky: ' . $status, 0) . '</p>';

        $search_tags = array('{surname}', '{order_number}', '{order_status}', '{project_title}');
        $replace_tags = array($row['priezvisko'], '#' . str_pad($row['order_id'], 8, '0', STR_PAD_LEFT), $status, PROJECT_TITLE);

        $body = str_replace($search_tags, $replace_tags, $body);

        require_once('../shared/classes/class.mail.php');

        $email_message = new email_message_class;
        $email_message->SetEncodedEmailHeader("From", $fromAddress, $fromName);
        $email_message->SetEncodedEmailHeader("Reply-To", $fromAddress, $fromName);
        $email_message->SetHeader("Sender", $fromAddress);
        $email_message->SetHeader("Subject", $cTranslator->getTranslation('Zmena stavu objednávky -', 0) . ' ' . PROJECT_TITLE);
        $email_message->AddHTMLPart($body, 'utf-8');
        // email klientovi
        $email_message->SetEncodedEmailHeader("To", (empty($row['mail']) ? $row['username'] : $row['mail']), $row['fullname']);
        $email_message->Send();

        /*
        require_once('../shared/classes/class.send_mail.php');
        $sendMail = new sendMail();
        $subject = $cTranslator->getTranslation("Zmena stavu objednávky -", 0) . ' ' . PROJECT_TITLE;

        //$body = '<p><img src="http://ireseller.sk/images/eshop/logo-ireseller-circle-new.png" alt="" style="width: 150px; height: 150px; border: 0px none; margin: 15px;" /></p><p>' . getContentByLabel('E-mail o stave objednávky: ' . $status, 0) . '</p>';
        $body = '<p>' . getContentByLabel('E-mail o stave objednávky: ' . $status, 0) . '</p>';


        $search_tags = array('{surname}', '{order_number}', '{order_status}', '{project_title}');
        $replace_tags = array($row['priezvisko'], '#' . str_pad($row['order_id'], 8, '0', STR_PAD_LEFT), $status, PROJECT_TITLE);

        $body = str_replace($search_tags, $replace_tags, $body);
        //$body = $cTranslator->getTranslation("Vaz. p. ", 0) . $row['priezvisko'] . "\n";
        //$body .= $cTranslator->getTranslation("Vasa objednavka cislo", 0) . ' ' . $row['order_id'] . ' ' . $cTranslator->getTranslation("bola potvrdena a objednany tovar pripravime v priebehu niekolkych dni. O vybaveni a odoslani objednavky Vas budeme informovat mailom.", 0) . ".\n\n\n";
        //$body .= $cTranslator->getTranslation("S pozdravom", 0) . "\n\n";
        //$body .= $cTranslator->getTranslation("Tim ", 0) . PROJECT_TITLE;
        $success_message = $cTranslator->getTranslation("Správa o zmene stavu objednávky bola úspešne odoslaná.", 0);
        $error_message = $cTranslator->getTranslation("Nastala chyba! Správa o zmene stavu objednávky nebola odoslaný. Prosím skúste znova.", 0);
        $response = array('subject' => $subject, 'body' => $body, 'success_message' => $success_message, 'error_message' => $error_message);
        // sendMail::send($toName, $sendTo, $subject, $body, $attachment = NULL, $fromEmail = NULL, $fromEmailName = NULL, $response = NULL)
        $sendMail->send($row['fullname'], (empty($row['mail']) ? $row['username'] : $row['mail']), NULL, NULL, NULL, NULL, NULL, $response);
        */
    }
    /**/

    header("Location:./index.php?module=eshop_orders");
    exit;
}
if ($_GET['limit']) {
    $_SESSION['userPrefs']['admin_items_per_page'] = $_GET['limit'];
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (!isset($_SESSION['userPrefs']['admin_items_per_page']))
    $_SESSION['userPrefs']['admin_items_per_page'] = '100';
?>

<div id="leftMenu">
    <h2>Prehľad objednávok</h2>
    <p>V tejto sekcii nájdete archív všetkých vaších objednávok. Taktiež môžte tieto objednavky vymazať.</p>
    <ul class="side-menu">
        <li><a<?= ($_GET['module'] == 'eshop_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_product">Produkty</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_gallery_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_gallery_product&amp;eshop=1">Fotogaléria produktov</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_orders' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_orders&amp;eshop=1">Objednávky</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_manufacturers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_manufacturers&amp;eshop=1">Výrobcovia</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_delivery_payment' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_delivery_payment&amp;eshop=1">Platba &amp; doručenie</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_supliers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_supliers&amp;eshop=1">Dodávatelia</a></li>
    </ul>
    <div id="submenu">
        <a href="#" class="edit" onclick="javascript:(confirm('Naozaj si želáte zmazať všetky objednávky?')) ? document.location.href = './index.php?module=eshop_orders&amp;eshop=1&amp;deleteall=1' : ''">Zmazať <br />objednávky</a>
    </div>
</div>
<div id="moduleContent">
    <h1>Prehľad objednávok</h1>
    <div class="limit-container">
        <div class="limit">
            <a<?= ($_SESSION['userPrefs']['admin_items_per_page'] == '100' ? ' class="selected"' : ''); ?> href="index.php?module=eshop_orders<?= (isset($_GET["display"]) ? '&amp;display=' . $_GET["display"] : '') . (is_numeric($_GET["category_id"]) ? '&amp;category_id=' . $_GET["category_id"] : ''); ?>&amp;limit=100">100</a>
            <a<?= ($_SESSION['userPrefs']['admin_items_per_page'] == '250' ? ' class="selected"' : ''); ?> href="index.php?module=eshop_orders<?= (isset($_GET["display"]) ? '&amp;display=' . $_GET["display"] : '') . (is_numeric($_GET["category_id"]) ? '&amp;category_id=' . $_GET["category_id"] : ''); ?>&amp;limit=250">250</a>
            <a<?= ($_SESSION['userPrefs']['admin_items_per_page'] == '500' ? ' class="selected"' : ''); ?> href="index.php?module=eshop_orders<?= (isset($_GET["display"]) ? '&amp;display=' . $_GET["display"] : '') . (is_numeric($_GET["category_id"]) ? '&amp;category_id=' . $_GET["category_id"] : ''); ?>&amp;limit=500">500</a>
        </div>
    </div>
    <table summary="" border="0" cellspacing="0" cellpadding="2" class="tablelist item-list">
        <tr>
            <th>Dátum <br />vytvorenia</th>
            <th>Dátum <br />spracovania</th>
            <th>Kupujúci</th>
            <th>Cena objednávky <br />s DPH</th>
            <th>Doručenie / Platba</th>
            <!--<th>TrustPay</th>-->
            <th>Stav objednávky</th>
            <th>GP WebPay<br />OrderNumber</th>
            <th></th>
        </tr>
        <?php
        $queryString = "SELECT order_id, 
                                order_state_id as order_status, 
                                order_state_id, 
                                price_total AS amount, 
                                order_discount, 
                                order_discount_price, 
                                order_vs, 
                                o.user_id, 
                                DATE_FORMAT(date_o, '%d.%m.%Y %H:%i') AS date_o, 
                                DATE_FORMAT(date_e, '%d.%m.%Y %H:%i') AS date_e, 
                                checked AS checked, 
                                status, 
                                res, 
                                o.note, 
                                o.payment_type, 
                                dt.price_eur AS delivery_price, 
                                dt.name AS delivery_name, 
                                currency_code, 
                                currency_rate, 
                                pt.price_eur AS payment_price, 
                                pt.name AS payment_name, 
                                u.username, 
                                u.mail 
                    FROM `" . TABLE_PREFIX . "order` AS `o`
                    LEFT JOIN `" . TABLE_PREFIX . "user` AS `u` ON (o.user_id = u.user_id)
                    LEFT JOIN " . TABLE_PREFIX . "user_address_book AS uab ON (o.user_id = uab.user_id)
                    LEFT JOIN " . TABLE_PREFIX . "delivery_type AS dt ON (o.delivery_type = dt.delivery_type_id)
                    LEFT JOIN " . TABLE_PREFIX . "payment_type AS pt ON (o.payment_type = pt.payment_type_id)
                    WHERE 1 AND DATE_FORMAT(date_o,'%Y-%m-%d') >= '" . $start_date . "' AND DATE_FORMAT(date_o,'%Y-%m-%d') <= '" . $end_date . "'
                    " . ((is_numeric($_GET['order_state_id']) and $_GET['order_state_id'] > 0) ? " AND o.order_status = '" . $_GET['order_state_id'] . "'" : "") . "
                    ORDER BY o.date_o DESC";


        $sql_str = "SELECT order_id, 
                            order_state_id as order_status, 
                            order_state_id, 
                            price_total AS amount, 
                            o.fullname as f_username, 
                            DATE_FORMAT(date_o, '%d.%m.%Y %H:%i') AS date_o, 
                            DATE_FORMAT(date_e, '%d.%m.%Y %H:%i') AS date_e, 
                            CONCAT(uab.fname, ' ', uab.lname) AS login, 
                            checked AS checked,
                            res,
                            o.gp_prcode,
                            o.gp_srcode,
                            o.gp_restext,
                            o.gp_ordnum,
                            o.payment_type,
                            dt.name AS delivery_name,
                            pt.name AS payment_name, 
                            invoice, 
                            invoice_file 
                    FROM `" . TABLE_PREFIX . "order` AS `o`
                    LEFT JOIN `" . TABLE_PREFIX . "user` AS `u` ON (o.user_id = u.user_id) 
                    LEFT JOIN " . TABLE_PREFIX . "user_address_book AS uab ON (o.user_id = uab.user_id)
                    LEFT JOIN " . TABLE_PREFIX . "delivery_type AS dt ON (o.delivery_type = dt.delivery_type_id)
                    LEFT JOIN " . TABLE_PREFIX . "payment_type AS pt ON (o.payment_type = pt.payment_type_id)
                    WHERE 1 " . ((is_numeric($_GET['order_state_id']) and $_GET['order_state_id'] > 0) ? " and o.order_status = '" . $_GET['order_state_id'] . "'" : "") . "
                    GROUP BY order_id 
                    ORDER BY o.date_o DESC";
        $ResultA = mysql_query($sql_str);
        if ($ResultA) {
            while ($line = mysql_fetch_object($ResultA)) {
                $date_o = explode(' ', $line->date_o);
                $date_e = explode(' ', $line->date_e);
                ?>
                <tr>
                    <td class="line-thick" align="right">
                        <?= $date_o[0] . '<br /><span style="opacity: 0.5">' . $date_o[1] . '</span>'; ?>
                    </td>
                    <td class="line-thick" align="right">
                        <?= ($date_e[0] == '00.00.0000' ? '' : $date_e[0] . '<br /><span style="opacity: 0.5">' . $date_e[1] . '</span>'); ?>
                    </td>
                    <td>
                        <?= $line->f_username ?>
                    </td>
                    <td style="text-align: right; padding-right: 1em;">
                        <?
                        $objednavka_eur = (VAT_VISIBILITY === FALSE ? $line->amount : ($line->amount / VAT_COEFFICIENT));
                        echo number_format($objednavka_eur, 2, ".", " ") . '&nbsp;&euro;';
                        ?>
                    </td>
                    <td class="line-thick">
                        <?= $line->delivery_name . '<br />' . $line->payment_name . '&nbsp;'; ?>
                    </td>
                    <!--<td>
                        <?= (($line->payment_type == TRUSTPAY_TRANSFER_PAYMENT_ID OR $line->payment_type == TRUSTPAY_CREDIT_PAYMENT_ID) ? trustPayCode($line->res) : ''); ?>
                    </td>-->
                    <td class="status-select">
                        <select onchange="javascript:document.location = './index.php?module=eshop_orders&amp;order_id=<?= $line->order_id ?>&amp;state=' + this.value;">
                            <option value="0"<?= ($line->order_state_id == 0) ? " selected=\"selected\"" : "" ?>>- - - - - -</option>
                            <option value="1"<?= ($line->order_state_id == 1) ? " selected=\"selected\"" : "" ?>>rezervovaná</option>
                            <option value="2"<?= ($line->order_state_id == 2) ? " selected=\"selected\"" : "" ?>>potvrdená</option>
                            <option value="3"<?= ($line->order_state_id == 3) ? " selected=\"selected\"" : "" ?>>vyskladnená</option>
                            <option value="4"<?= ($line->order_state_id == 4) ? " selected=\"selected\"" : "" ?>>zaplatená</option>
                            <option value="5"<?= ($line->order_state_id == 5) ? " selected=\"selected\"" : "" ?>>vybavená</option>
                            <option value="6"<?= ($line->order_state_id == 6) ? " selected=\"selected\"" : "" ?>>zrušená</option>
                        </select>
                    </td>
                    <td>
                        <?
                            if($line->payment_type == 7) {
                                if(is_null($line->gp_prcode) or $line->gp_prcode == '') {                                    
                                    echo '<div style="background: gray; color: #fff; text-align: center;" title="bez záznamu">?</div>';
                                }
                                else {
                                    echo '<div style="background: ' . ($line->gp_prcode == 0 ? 'green' : 'red') . '; color: #fff; text-align: center;" title="' . $line->gp_restext . '">';
                                    echo ($line->gp_prcode == 0 ? 'OK' : $line->gp_prcode);
                                    echo '</div>';
                                }
                                echo $line->gp_ordnum;
                            }
                        ?>
                    </td>
                    <td align="center" class="actions">
                        <?
                        
                        if($line->invoice_file) {
                            ?>
                            <a href="<?= ROOTDIR ?>/docs/faktury/<?= $line->invoice_file; ?>" target="_blank">Faktúra</a>
                            <a href="<?= ROOTDIR ?>/setup/modules/_eshop_orders_invoice.php?order_id=<?= $line->order_id ?>&amp;action=preview" target="_blank">Náhľad</a>
                            <a href="<?= ROOTDIR ?>/setup/modules/_eshop_orders_invoice.php?order_id=<?= $line->order_id ?>&amp;action=print" target="_blank">Tlačiť</a>
                            <?                        
                        }
                        else {
                            ?>
                            <a href="<?= ROOTDIR ?>/setup/modules/_eshop_orders_preview.php?order_id=<?= $line->order_id ?>&amp;action=preview" target="_blank">Náhľad</a>
                            <a href="<?= ROOTDIR ?>/setup/modules/_eshop_orders_preview.php?order_id=<?= $line->order_id ?>&amp;action=print" target="_blank">Tlačiť</a>
                            <?
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        } else {
            print mysql_error();
        }
        mysql_free_result($ResultA);
        ?>
        <tr><th colspan="6">&nbsp;</th></tr>

    </table>
</div>