<?php
if (file_exists("../../shared/config.inc.php")) {
    include("../../shared/config.inc.php");
}
if (!$user->isAdmin()) {
    exit;
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= PROJECT_NAME; ?> - Veľkosti</title>
        <link rel="stylesheet" type="text/css" href="../index.css" />
        <style type="text/css">
            body {
                padding: 20px;
            }
            table td {
                padding: 2px 0;
            }
            table td[scope="col"] {
                width: 150px;
            }
            table select {
                width: 332px;
            }
            #messages-container {
                float: left;
                width: 100%;
                margin: 0;
            }
        </style>
        <script type="text/javascript" src="../../js/jquery/jquery-1.9.1.min.js"></script>
        <!--<script type="text/javascript" src="../js/colorpicker/jscolor.js"></script>-->
        <script type="text/javascript" src="../../js/functions.js"></script>
        <!--<script type="text/javascript" src="../../js/script.js"></script>-->
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
        </script>
    </head>
    <body>
        <h1>Varianty produktu</h1>
        <?
        switch ($_GET['action']) {
            case 'update':
                if ($_POST) {
                    $query = 'UPDATE ' . TABLE_PREFIX . 'product_type SET name="' . $_POST['type_name'] . '", pocet="' . $_POST['amount'] . '" WHERE 1 AND product_type_id="' . mysql_real_escape_string($_GET['product_type_id']) . '";';
                    mysql_query($query);
                    //header('Location: _eshop_product_colors.php?product_id=' . $_GET['product_id']);
                    //exit;
                    //header("Location:" . ROOTDIR . "/setup/modules/_eshop_product_colors.php?product_id=" . $_POST['product_id']);
                    //exit;
                }
                $queryString = 'SELECT pt.name AS type_name, pocet AS amount, pt.product_color_id, pt.product_id, c.code, c.color_id, c.name AS color_name FROM ' . TABLE_PREFIX . 'product_type AS pt
                                    LEFT JOIN ' . TABLE_PREFIX . 'product_color AS pc USING(product_color_id)
                                    LEFT JOIN ' . TABLE_PREFIX . 'color AS c USING(color_id)
                                    WHERE 1 AND pt.product_id="' . $_GET['product_id'] . '" AND pt.product_type_id = "' . $_GET['product_type_id'] . '";';
                $Result = mysql_query($queryString);
                if ($Result) {
                    if (mysql_num_rows($Result) == 1) {
                        $Row = mysql_fetch_assoc($Result);
                    }
                }
                ?>
                <div>
                    <form method="POST" enctype="multipart/form-data" action="">
                        <table width="100%">
                            <tr>
                                <td scope="col">Farba:</td>
                                <td>
                                    <select name="color_id" disabled="disabled">
                                        <option value=""></option>
                                        <?
                                        $query_farba = "select color_id as id, name as name from " . TABLE_PREFIX . "color where 1;";
                                        $result_farba = mysql_query($query_farba);
                                        while ($row_farba = mysql_fetch_object($result_farba)) {
                                            echo '<option value="' . $row_farba->id . '" ' . (($row_farba->id == $Row['color_id']) ? 'selected="selected"' : '') . '>' . $row_farba->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Veľkosť:</td>
                                <td>
                                    <input name="type_name" type="text" value="<?= $Row['type_name']; ?>" autocomplete="off" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td align="left">
                                    <input name="product_id" type="hidden" value="<?= $_GET['product_id']; ?>" />
                                    <input name="product_color_id" type="hidden" value="<?= $_GET['product_color_id']; ?>" />
                                    <input name="update_send" type="hidden" value="1" />
                                    <input type="submit" value="Upraviť" />
                                    <input type="submit" value="Návrat späť" onclick="window.location = '<?= $_SERVER['HTTP_REFERER']; ?>';" />
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <?
                break;
            case 'delete':
                $queryString = "DELETE FROM " . TABLE_PREFIX . "product_color WHERE 1 AND product_color_id = '" . $_GET['product_color_id'] . "';";
                $ResultB = mysql_query($queryString);
                if ($ResultB) {
                    // delete priradene velkosti
                    $queryString = "DELETE FROM " . TABLE_PREFIX . "product_type WHERE 1 AND product_color_id = '" . $_GET['product_color_id'] . "';";
                    $ResultB = mysql_query($queryString);
                    if ($ResultB) {

                    }

                    //zistenie ci to nebola posledna "neuniverzalna" velkost ak ano,tak je potrebne pridat univerzalnu velkost
                    $queryString = "SELECT product_color_id FROM " . TABLE_PREFIX . "product_color WHERE 1 AND product_id='" . $_GET['product_id'] . "';";
                    $ResultB = mysql_query($queryString);
                    if (mysql_num_rows($ResultB) == 0) {
                        // priradim univerzalnu farbu
                        $queryString = "INSERT INTO " . TABLE_PREFIX . "product_color (product_id, univerzal) VALUES ('" . $_GET['product_id'] . "', '1');";
                        $ResultA = mysql_query($queryString);
                        if ($ResultA) {
                            $product_color_id = mysql_insert_id();

                            // priradim univerzalnu velkost
                            $queryString = "INSERT INTO " . TABLE_PREFIX . "product_type (product_id, product_color_id, name, univerzal) VALUES ('" . $_GET['product_id'] . "', '" . $product_color_id . "', 'Univerzálna', '1');";
                            $ResultA = mysql_query($queryString);
                            if ($ResultA) {

                            }
                        }
                    }
                    Message::setMessage('Farba bola úspešne odstránená.', 0);
                    header("Location:" . $_SERVER['HTTP_REFERER']);
                    exit;
                } else {
                    print(mysql_error());
                }
                break;
            default:
                if ($_POST && !is_numeric($_POST['product_color_id'])) {

                    $query = 'SELECT COUNT(product_color_id) AS total, product_color_id FROM ' . TABLE_PREFIX . 'product_color WHERE 1 AND product_id="' . $_POST['product_id'] . '" AND color_id="' . $_POST['color_id'] . '";';
                    $result = mysql_query($query);
                    $row = mysql_fetch_object($result);
                    if ($row->total != '0') {
                        $product_color_id = $row->product_color_id;
                    } else {
                        $queryString = 'INSERT INTO ' . TABLE_PREFIX . 'product_color (product_id, color_id) VALUES ("' . $_POST['product_id'] . '", "' . $_POST['color_id'] . '");';
                        $ResultA = mysql_query($queryString);
                        if ($ResultA) {
                            $product_color_id = mysql_insert_id();
                        } else {
                            print(mysql_error());
                        }
                    }

                    // delete univerzal velkost pokial existuje
                    $queryString = "DELETE FROM " . TABLE_PREFIX . "product_color WHERE 1 AND product_id = '" . $_GET['product_id'] . "' AND univerzal='1';";
                    if (!$ResultC = mysql_query($queryString)) {
                        if (mysql_errno())
                            echo "MySql Error (" . mysql_errno() . "): " . mysql_error();
                    }
                    $queryString = "DELETE FROM " . TABLE_PREFIX . "product_type WHERE 1 AND product_id='" . $_GET['product_id'] . "' AND univerzal='1';";
                    mysql_query($queryString);
                    //
                    // priradim univerzalnu velkost
                    if (!empty($_POST['type_name'])) {
                        $type_name = $_POST['type_name'];
                    } else {
                        $type_name = 'Univerzálna';
                    }
                    if (!empty($_POST['amount'])) {
                        $amount = $_POST['amount'];
                    } else {
                        $amount = '0';
                    }

                    $check_query = 'SELECT product_type_id FROM ' . TABLE_PREFIX . 'product_type WHERE 1 AND product_id="' . $_POST['product_id'] . '" AND product_color_id="' . $product_color_id . '" AND name="' . $type_name . '";';
                    $check_result = mysql_query($check_query);
                    if (mysql_num_rows($check_result) != '0') {
                        Message::setMessage('Zadaný variant sa už nachádza v e-shope!', 2);
                        header('Location: ' . $_SERVER['HTTP_REFERER']);
                        exit;
                    }

                    $queryString = 'INSERT INTO ' . TABLE_PREFIX . 'product_type (product_id, product_color_id, name, univerzal, pocet) VALUES ("' . $_POST['product_id'] . '", "' . $product_color_id . '", "' . $type_name . '", "0", "' . $amount . '");';
                    $ResultA = mysql_query($queryString);
                }
                ?>
                <div>
                    <form method="POST" enctype="multipart/form-data" action="">
                        <table width="100%">
                            <tr>
                                <td scope="col">Farba:</td>
                                <td>
                                    <select name="color_id">
                                        <option value=""></option>
                                        <?
                                        $query_farba = "select color_id as id, name as name from " . TABLE_PREFIX . "color where 1;";
                                        $result_farba = mysql_query($query_farba);
                                        while ($row_farba = mysql_fetch_object($result_farba)) {
                                            echo '<option value="' . $row_farba->id . '">' . $row_farba->name . '</option>';
                                        }
                                        ?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td>Veľkosť:</td>
                                <td>
                                    <input name="type_name" type="text" autocomplete="off" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">&nbsp;</td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td align="left">
                                    <input name="product_id" type="hidden" value="<?= $_GET['product_id']; ?>" />
                                    <input type="submit" value="Pridať" />
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
            <?
        }
        Message::getMessage();
        ?>
        <div style="min-height:150px; overflow: auto;">
            <table style="width: 100%;" border="0" cellpadding="0" cellspacing="2">
                <tr>
                    <td>
                        <strong>Farba</strong>
                    </td>
                    <td>
                        <strong>Veľkosti</strong>
                    </td>
                    <td>
                    </td>
                </tr>
                <?php
                $queryString = 'SELECT pt.name AS type_name, pocet AS amount, pt.product_type_id, pt.product_color_id, pt.product_id, c.code, c.name AS color_name FROM ' . TABLE_PREFIX . 'product_type AS pt
                                    LEFT JOIN ' . TABLE_PREFIX . 'product_color AS pc USING(product_color_id)
                                    LEFT JOIN ' . TABLE_PREFIX . 'color AS c USING(color_id)
                                    WHERE 1 AND pt.product_id="' . $_GET['product_id'] . '" ORDER BY type_name ASC;'; // AND pc.univerzal="0"

                $Result = mysql_query($queryString);
                if ($Result) {
                    if (mysql_num_rows($Result) > 0) {
                        while ($Row = mysql_fetch_assoc($Result)):
                            ?>
                            <tr>
                                <td style="height: 18px; text-align:center; background: #<?= $Row['code']; ?>"><span style="color:<?= invertColor($Row['code']); ?>"><?= $Row['color_name']; ?></span></td>
                                <td><?= $Row['type_name']; ?></td>
                                <td align="right">
                                    <a href="_eshop_product_colors.php?product_id=<?= $_GET['product_id'] . '&amp;product_type_id=' . $Row['product_type_id']; ?>&amp;action=update">Upraviť</a> &nbsp;
                                    <a href="javascript:;" onclick="javascript:confirmAction('Naozaj zmazať položku?', '', '_eshop_product_colors.php?action=delete&amp;product_id=<?= $_GET['product_id'] . '&amp;product_type_id=' . $Row['product_type_id'] . '&amp;product_color_id=' . $Row['product_color_id']; ?>');">Zmazať</a>
                                </td>
                            </tr>
                            <?
                        endwhile;
                    } else {
                        $queryString = "SELECT * FROM " . TABLE_PREFIX . "product_color WHERE 1 AND product_id = '" . $_GET['product_id'] . "' AND univerzal='1';";
                        $Result = mysql_query($queryString);
                        if ($Result) {
                            if (mysql_num_rows($Result) == 1) {
                                $Row = mysql_fetch_assoc($Result);
                                print '<tr><td></td><td>';
                                $queryStringVelkosti = "SELECT * FROM " . TABLE_PREFIX . "product_type WHERE 1 AND product_color_id = '" . $Row['product_color_id'] . "' and univerzal = '0';";
                                if (!$ResultVelkosti = mysql_query($queryStringVelkosti)) {
                                    print 'Chyba vyberu veľkostí z DB .';
                                } else {
                                    while ($RowVelkosti = mysql_fetch_assoc($ResultVelkosti)) {
                                        $string .= $RowVelkosti['name'] . ' <small>(' . $RowVelkosti['pocet'] . ')</small>, ';
                                    }
                                    echo rtrim($string, ', ');
                                }
                                print '</td><td></td></tr>';
                            } else {
                                print 'ERROR: vyber univerzalnej farby';
                            }
                        }
                    }
                } else {
                    print(mysql_error());
                }
                ?>
            </table>
        </div>
    </body>
</html>
