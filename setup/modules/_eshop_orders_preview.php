<?php
require_once("../../shared/config.inc.php");

if (!$user->isAdmin()) {
    header('Content-Type: text/html; charset=utf-8');
    echo 'prístup obmedzený';
    exit;
}
if (empty($_GET['order_id'])) {
    header('Content-Type: text/html; charset=utf-8');
    echo 'Nebolo zadané ID objednávky';
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <title><?= PROJECT_NAME ?> - náhľad objednávky č.: <?= $_GET['order_id']; ?></title>
        <?
        if ($_GET['action'] == 'print') {
            echo '<script type="text/javascript">window.onload=function(){self.print();}</script>';
        }
        ?>
    </head>
    <body>
        <?php
        $query = 'SELECT * FROM ' . TABLE_PREFIX . 'order WHERE 1 AND order_id="' . mysql_real_escape_string($_GET['order_id']) . '"';
        $result = @mysql_query($query);
        if ($result) {
            $row = @mysql_fetch_object($result);
        } else {
            print mysql_error();
        }
        //print_r($row);
        mysql_free_result($result);
        $delivery = Cart::get_delivery_type($row->delivery_type);
        $payment = Cart::get_payment_type($row->payment_type);
        ?>
        <table border="0" cellspacing="0" cellpadding="0" style="font-family: Arial, Helvetica, sans-serif;width: 980px;font-size: 13px;margin: 0 auto;">
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="7" align="right" style="padding: 4px 8px;">Objednávka číslo: #<?= str_pad($row->order_id, 8, '0', STR_PAD_LEFT); ?></td>
            </tr>
            <tr>
                <td class="black" colspan="3" style="padding: 4px 8px;background-color: #ccc;"><strong>Dodávateľ</strong></td>
                <td class="black" colspan="4" style="padding: 4px 8px;background-color: #ccc;"><strong>Odberateľ</strong></td>
            </tr>
            <tr>
                <td class="extra-padding" colspan="3" rowspan="5" valign="top" style="padding: 10px 8px;">
                    <?= getContentByLabel('Údaje o dodávateľovi do faktúry', 0); ?>
                </td>
                <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;"><strong><?= $row->to_name; ?></strong></td>
            </tr>
            <tr class="grey" style="background: #EAEAEA;">
                <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Fakturačná adresa: </strong></td>
            </tr>
            <tr>
                <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                    <?
                    echo $row->to_name . '<br />';
                    $delivery_address = explode(';', $row->delivery_address);
                    foreach ($delivery_address as $value) {
                        echo $value . '<br />';
                    }
                    if (!empty($row->company_data)) {
                        echo '<br />';
                        $company_data = explode(';', $row->company_data);
                        $company_label = ['', 'IČO: ', 'DIČ: '];
                        foreach ($company_data as $key=>$value) {
                            if($key == 0)
                                echo $company_label[$key] . '<strong>' . $value . '</strong><br />';
                            else
                                echo $company_label[$key] . $value . '<br />';
                        }
                    }
                    echo '<br /><a href="mailto:' . User::returnUserMail($row->user_id) . '">' . User::returnUserMail($row->user_id) . '</a><br /><a href="tel:' . User::returnUserPhone($row->user_id) . '">' . User::returnUserPhone($row->user_id) . '</a><br />';
                    ?>
                </td>
            </tr>
            <?
            if (!empty($row->invoice_address)) {
                ?>
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Adresa doručenia: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        <?
                        $invoice_address = explode(';', $row->invoice_address);
                        foreach ($invoice_address as $value) {
                            echo $value . '<br />';
                        }
                        ?>
                    </td>
                </tr>
                <?
            }

            if (!empty($row->packeta)) {
                ?>
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>Packeta: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        <?
                        $packeta = explode(';', $row->packeta);
                        foreach ($packeta as $value) {
                            echo $value . '<br />';
                        }
                        ?>
                    </td>
                </tr>
                <?
            }

            if (!empty($row->dpd)) {
                ?>
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="4" valign="top" style="padding: 4px 8px;"><strong>DPD: </strong></td>
                </tr>
                <tr>
                    <td class="extra-padding" colspan="4" valign="top" style="padding: 10px 8px;">
                        <?
                        $dpd = explode(';', $row->dpd);
                        foreach ($dpd as $value) {
                            echo $value . '<br />';
                        }
                        ?>
                    </td>
                </tr>
                <?
            }
            ?>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td class="black" colspan="7" style="padding: 4px 8px;background-color: #ccc;"><strong>Objednaný tovar</strong></td>
            </tr>
            <?
            if (VAT_VISIBILITY === TRUE) {
                ?>
                <tr class="smaller-font black" style="font-size: 11px;padding: 4px 8px;background-color: #ccc;">
                    <td width="2%" style="padding: 4px 8px;">Množstvo</td>
                    <td width="10%" style="padding: 4px 8px;">Kód tovaru</td>
                    <td style="padding: 4px 8px;">Názov tovaru</td>
                    <td width="10%" style="padding: 4px 8px;">Cena za kus bez DPH</td>
                    <td width="10%" style="padding: 4px 8px;">Cena za kus s DPH</td>
                    <td width="10%" style="padding: 4px 8px;">Cena spolu bez DPH</td>
                    <td width="10%" style="padding: 4px 8px;">Cena spolu s DPH</td>
                </tr>
                <?
            } else {
                ?>
                <tr class="smaller-font black" style="font-size: 11px;padding: 4px 8px;background-color: #ccc;">
                    <td width="2%" style="padding: 4px 8px;">Množstvo</td>
                    <td width="10%" style="padding: 4px 8px;">Kód tovaru</td>
                    <td style="padding: 4px 8px;">Názov tovaru</td>
                    <td width="20%" colspan="2" style="padding: 4px 8px; text-align: right;">Cena za kus</td>
                    <td width="20%" colspan="2" style="padding: 4px 8px; text-align: right;">Cena spolu</td>
                </tr>
                <?
            }
            $query1 = 'SELECT o.order_product_id, o.order_id, o.user_id, o.product_id, o.product_name, o.amount, o.price, o.color, o.size, p.code_1 AS ean FROM ' . TABLE_PREFIX . 'order_product AS o
                       LEFT JOIN ' . TABLE_PREFIX . 'product AS p USING(product_id)
                       WHERE 1 AND order_id="' . mysql_real_escape_string($_GET['order_id']) . '"';
            $result1 = mysql_query($query1);
            $i = 0;
            while ($row1 = mysql_fetch_object($result1)) {
                $i++;
                if (VAT_VISIBILITY === TRUE) {
                    ?>
                    <tr<?= (($i % 2 == 0) ? ' class="grey" style="background: #EAEAEA;"' : ''); ?>>
                        <td style="padding: 4px 0;text-align:center;"><?= $row1->amount; ?>x</td>
                        <td style="padding: 4px 8px;font-size:10px;"><?= $row1->ean . (!empty($row1->color) ? '-' . $row1->color : '') . (!empty($row1->size) ? '-' . Product::get_size_name($row1->size) : ''); ?></td>
                        <td style="padding: 4px 8px;"><?= $row1->product_name; ?></td>
                        <td style="padding: 4px 8px;"><?= number_format(($row1->price / VAT_COEFFICIENT), 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
                        <td style="padding: 4px 8px;"><?= number_format($row1->price, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
                        <td style="padding: 4px 8px;"><?= number_format((($row1->price * $row1->amount) / VAT_COEFFICIENT), 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
                        <td style="padding: 4px 8px;"><?= number_format(($row1->price * $row1->amount), 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
                    </tr>
                    <?
                } else {
                    ?>
                    <tr<?= (($i % 2 == 0) ? ' class="grey" style="background: #EAEAEA;"' : ''); ?>>
                        <td style="padding: 4px 0;text-align:center;"><?= $row1->amount; ?>x</td>
                        <td style="padding: 4px 8px;font-size:10px;"><?= $row1->ean; ?></td>
                        <td style="padding: 4px 8px;"><?= $row1->product_name; ?></td>
                        <td colspan="2" style="padding: 4px 8px; text-align: right;"><?= number_format($row1->price, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
                        <td colspan="2" style="padding: 4px 8px; text-align: right;"><?= number_format(($row1->price * $row1->amount), 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
                    </tr>
                    <?
                }
            }
            ?>
            <tr class="black" style="background-color: #ccc;">
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celková cena nákupu</strong></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><strong><?= number_format($row->price_total, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</strong></td>
            </tr>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;">Spôsob doručenia <small>(<?= $delivery['name']; ?>)</small></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><?= $delivery['price']; ?>&nbsp;&euro;</td>
            </tr>
            <tr>
                <td colspan="6" align="right" style="padding: 4px 8px;">Spôsob platby <small>(<?= $payment['name']; ?>)</small></td>
                <td colspan="1" align="right" style="padding: 4px 8px;"><?= $payment['price']; ?>&nbsp;&euro;</td>
            </tr>
            <?
            if (VAT_VISIBILITY === TRUE) {
                ?>
                <tr class="grey" style="background: #EAEAEA;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celková cena nákupu s DPH</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;"><strong><?= number_format($row->price_total, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</strong></td>
                </tr>
                <?
                if ($row->order_discount != '0') {
                    ?>
                    <tr style="background: #EAEAEA;">
                        <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Zľava za registráciu</strong></td>
                        <td colspan="1" align="right" style="padding: 4px 8px;"><strong><?= REGISTRATION_DISCOUNT; ?>%</strong></td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
                </tr>
                <tr style="font-size: 19px;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celkom</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;">
                        <strong>
                            <?
                            if ($row->order_discount != '0') {
                                echo number_format((($row->price_total * ((100 - REGISTRATION_DISCOUNT) / 100)) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
                            } else {
                                echo number_format(($row->price_total + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
                            }
                            ?>&nbsp;&euro;
                        </strong>
                    </td>
                </tr>
                <?
            } else {
                if ($row->order_discount != '0') {
                    ?>
                    <tr style="background: #EAEAEA;">
                        <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Zľava za registráciu</strong></td>
                        <td colspan="1" align="right" style="padding: 4px 8px;"><strong><?= REGISTRATION_DISCOUNT; ?>%</strong></td>
                    </tr>
                    <?
                }
                ?>
                <tr>
                    <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
                </tr>
                <tr style="font-size: 19px;">
                    <td colspan="6" align="right" style="padding: 4px 8px;"><strong>Celkom</strong></td>
                    <td colspan="1" align="right" style="padding: 4px 8px;">
                        <strong>
                            <?
                            if ($row->order_discount != '0') {
                                echo number_format((($row->price_total * ((100 - REGISTRATION_DISCOUNT) / 100)) + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
                            } else {
                                echo number_format(($row->price_total + $delivery['price'] + $payment['price']), 2, ',', '&nbsp;');
                            }
                            ?>&nbsp;&euro;
                        </strong>
                    </td>
                </tr>
                <?
            }
            if($row->comment) {
                ?>
                <tr>
                    <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
                </tr>
                <tr>
                    <td colspan="7" style="padding: 4px 8px;"><strong>poznámka: </strong><p><?= $row->comment; ?></p></td>
                </tr>
                <?
            }
            ?>
            <tr>
                <td colspan="7" style="padding: 4px 8px;">&nbsp;</td>
            </tr>
        </table>
    </body>
</html>
<?php
die();
require_once("../../shared/config.inc.php");


if (!isset($_GET['objednavka'])) {
    $_SESSION['objednavka'] = 1;
}
if ($_GET['objednavka'] == 1) {
    $_SESSION['objednavka'] = 1;
} else {
    unset($_SESSION['objednavka']);
}
?>