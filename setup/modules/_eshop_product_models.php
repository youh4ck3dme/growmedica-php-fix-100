<?php
include("../../shared/config.inc.php");

if (!$user->isAdmin()) {
    exit;
}

if (isset($_POST['name'])):
    $queryString = "insert into " . TABLE_PREFIX . "product_type (name, product_id,product_color_id) values ('" . $_POST['name'] . "', '" . $_POST['product_id'] . "', '" . $_POST['product_color_id'] . "');";
    $ResultA = mysql_query($queryString);
    if ($ResultA) {
        $queryString = "delete from " . TABLE_PREFIX . "product_type where 1 and product_id = '" . $_POST['product_id'] . "' and univerzal = '1';";
        if (!$ResultC = mysql_query($queryString)) {
            if (mysql_errno())
                print("MySql Error (" . mysql_errno() . "): " . mysql_error());
            Message::setMessage('Variant produktu nebol pridaný.', 2);
        }else {
            Message::setMessage('Variant produktu bol úspešne pridaný.', 0);
        }
        //	header("Location:" . $_SERVER['HTTP_REFERER']);
        //	exit;
    } else {
        print(mysql_error());
    }
endif;

if ($_GET['delete'] == 1):
    $queryString = "delete from " . TABLE_PREFIX . "product_type where 1 and product_type_id = '" . $_GET['product_type_id'] . "';";
    $ResultB = mysql_query($queryString);
    if ($ResultB):
        //zistenie ci to nebola posledna "neuniverzalna" velkost ak ano,tak je potrebne pridat univerzalnu velkost
        $queryString = "select product_type_id from " . TABLE_PREFIX . "product_type where 1 and product_color_id = '" . $_GET['product_color_id'] . "';";
        $ResultB = mysql_query($queryString);
        if (mysql_num_rows($ResultB) == 0) {
            // priradim univerzalnu farbu
            $queryString = "insert into " . TABLE_PREFIX . "product_type (product_id,product_color_id, name,univerzal) values ('" . $_POST['product_id'] . "','" . $_POST['product_color_id'] . "', 'Univerzál', '1');";
            $ResultA = mysql_query($queryString);
            if ($ResultA) {

            }
        }

        Message::setMessage('Variant produktu bol úspešne odstránený.', 0);
        header("Location:" . $_SERVER['HTTP_REFERER']);
        exit;
    else:
        print(mysql_error());
    endif;
endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= DEFAULTTITLENAME ?> - Veľkosti</title>
        <style type="text/css">
            body, form {
                margin: 0px;
                padding: 0px;
            }
        </style>
        <script type="text/javascript" src="../../shared/js/colorpicker/jscolor.js"></script>
        <script type="text/javascript" src="../../shared/js/functions.js"></script>
        <script type="text/javascript" src="../../shared/js/script.js"></script>
        <script type="text/javascript">
            function overForm(thisForm) {
                if (thisForm.name.value == "") {
                    alert('Nebola zadaná veľkosť produktu.');
                    thisForm.name.focus();
                    return false;
                }
            }
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
    </head>

    <body>
        <div style="width:100%; text-align:right"><a href="_eshop_product_colors.php?product_id=<?= $_GET['product_id'] ?>">Späť na zoznam farieb</a></div>
        <strong>Veľkosti</strong><br />
        <div>
            <form method="POST" enctype="multipart/form-data" action="_eshop_product_models.php?product_id=<?= $_GET['product_id'] ?>&amp;product_color_id=<?= $_GET['product_color_id'] ?>" onsubmit="return overForm(this);">
                <table width="100%">
                    <tr>
                        <td style="width: 365px;">
                            Názov: <input name="name" type="text" id="name" maxlength="50" style="width:30%;" value="<?= $Row['name'] ?>" />
                            <? /* počet kusov: <input name="pocet" type="text" id="name" maxlength="50" style="width: 20%;" value="<?= $Row['pocet'] ?>" /> */ ?>
                            <input name="product_id" type="hidden" value="<?= $_GET['product_id']; ?>" />
                            <input name="product_color_id" type="hidden" value="<?= $_GET['product_color_id']; ?>" />
                        </td>
                        <td align="right"><input type="submit" value="Pridať" /></td>
                    </tr>
                </table>
            </form>
        </div>
        <div style="height:256px; overflow: auto;">
            <br />
            <table style="width: 100%;" border="0" cellpadding="0" cellspacing="2">
                <tr><td><strong>Názov</strong></td></tr>
                <?php
                $queryString = "select * from " . TABLE_PREFIX . "product_type where 1 and product_color_id = '" . $_GET['product_color_id'] . "' and univerzal != '1';";
                $Result = mysql_query($queryString);
                if ($Result):
                    while ($Row = mysql_fetch_assoc($Result)):
                        print '
					<tr>
					<td style="height: 18px;">' . $Row['name'] . '</td>';
                        //<td style="height: 18px;">' . $Row['pocet'] . '</td>
                        print '
					<td align="right">';
                        //<a href="_eshop_product_models.php?product_id='.$_GET['product_id'].'&amp;product_type_id=' . $Row['product_type_id'] . '">Upraviť</a> &nbsp;
                        print '
					<a href="javascript:;" onclick="javascript:confirmAction(\'Naozaj zmazať položku?\', \'\', \'_eshop_product_models.php?delete=1&amp;product_type_id=' . $Row['product_type_id'] . '&amp;product_color_id=' . $_GET['product_color_id'] . '&amp;product_id=' . $_GET['product_id'] . '\');">Zmazať</a></td>
					</tr>
				';
                    endwhile;
                else:
                    print(mysql_error());
                endif;
                ?>
            </table>
        </div>
    </body>
</html>
