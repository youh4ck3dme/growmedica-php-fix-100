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

    function inverseHex($color) {
        $color = trim($color);
        $prependHash = FALSE;

        if (strpos($color, '#') !== FALSE) {
            $prependHash = TRUE;
            $color = str_replace('#', NULL, $color);
        }

        switch ($len = strlen($color)) {
            case 3:
                $color = preg_replace("/(.)(.)(.)/", "\\1\\1\\2\\2\\3\\3", $color);
                break;
            case 6:
                break;
            default:
            //trigger_error("Invalid hex length ($len). Must be a minimum length of (3) or maxium of (6) characters", E_USER_ERROR);
        }

        if (!preg_match('/^[a-f0-9]{6}$/i', $color)) {
            $color = htmlentities($color);
            //trigger_error( "Invalid hex string #$color", E_USER_ERROR );
        }

        $r = dechex(255 - hexdec(substr($color, 0, 2)));
        $r = (strlen($r) > 1) ? $r : '0' . $r;
        $g = dechex(255 - hexdec(substr($color, 2, 2)));
        $g = (strlen($g) > 1) ? $g : '0' . $g;
        $b = dechex(255 - hexdec(substr($color, 4, 2)));
        $b = (strlen($b) > 1) ? $b : '0' . $b;

        return ($prependHash ? '#' : NULL) . $r . $g . $b;
    }

    if (!$user->isAdmin()) {
        exit;
    }

    if (isset($_POST['name']) and $_GET['update'] != 1):
        $queryString = "insert into " . TABLE_PREFIX . "color (name, code) values ('" . $_POST['name'] . "', '" . (empty($_POST['code']) ? 'ffffff' : $_POST['code']) . "');";
        $ResultA = mysql_query($queryString);

        if ($ResultA) {
            Message::setMessage('Farba bola úspešne pridaná.', 0);
        } else {
            Message::setMessage('Farba nebola úspešne pridaná.', 2);
            print(mysql_error());
        }
    endif;

    if ($_GET['update'] == 1) {
        if ($_POST['update_send'] == 1) {
            $queryString = "update " . TABLE_PREFIX . "color set name = '" . $_POST['name'] . "', code = '" . (empty($_POST['code']) ? 'ffffff' : $_POST['code']) . "' where 1 and color_id = '" . $_GET['color_id'] . "';";
            $ResultC = mysql_query($queryString);

            Message::setMessage('Farba bola úspešne upravená.', 0);
            header("Location:" . ROOTDIR . "/setup/index.php?module=eshop_product_color_add&eshop1=&product_id=" . $_GET['product_id']);
            exit;
        }

        $queryString = "select * from " . TABLE_PREFIX . "color where 1 and color_id = '" . $_GET['color_id'] . "';";
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
                        <td>Farba:</td>
                        <td><input type="text" name="name" value="<?= $Row['name'] ?>" /></td>
                    </tr>
                    <tr>
                        <td>Kód farby:</td>
                        <td><input name="code" type="text" value="<?= $Row['code'] ?>" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td align="left">
                            <input name="update_send" type="hidden" value="1" />
                            <input type="submit" value="Upraviť" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
        <?
    }

    if ($_GET['delete'] == 1):
        $queryString = "delete from " . TABLE_PREFIX . "color where 1 and color_id = '" . $_GET['color_id'] . "';";
        $ResultB = mysql_query($queryString);
        if ($ResultB):
            Message::setMessage('Farba bola úspešne odstránená.', 0);
            header("Location:" . $_SERVER['HTTP_REFERER']);
            exit;
        else:
            print(mysql_error());
        endif;
    endif;
    ?>
    <style type="text/css">
        body, form {
            margin: 0px;
            padding: 0px;
        }
    </style>
    <script type="text/javascript" src="../../shared/js/colorpicker/jscolor.js"></script>
    <script type="text/javascript" src="../../shared/js/functions.js"></script>
    <script type="text/javascript" src="../../shared/js/script.js"></script>
    <link href="../../shared/thickbox.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="../../shared/js/thickbox/jquery.js"></script>
    <script type="text/javascript" src="../../shared/js/thickbox/thickbox.js"></script>
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
    <strong>Farby</strong><br />
    <? if ($_GET['update'] != 1) { ?>
        <div>
            <form method="POST" enctype="multipart/form-data" action="">
                <table width="100%">
                    <tr>
                        <td>Farba:</td>
                        <td><input type="text" name="name" /></td>
                    </tr>
                    <tr>
                        <td>Kód farby:</td>
                        <td><input name="code" type="text" /></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                        <td align="left">
                            <input type="submit" value="Pridať" />
                        </td>
                    </tr>
                </table>
            </form>
        </div>
    <? } ?>
    <div style="height:256px; overflow: auto;">
        <br />
        <table style="width: 100%;" border="0" cellpadding="0" cellspacing="2">
            <tr><td><strong>Názov</strong></td><td><strong>Farba</strong></td><td></td></tr>
            <?php
            $queryString = "select * from " . TABLE_PREFIX . "color";
            $Result = mysql_query($queryString);
            if ($Result):
                if (mysql_num_rows($Result) > 0) {
                    while ($Row = mysql_fetch_assoc($Result)):
                        print '
						<tr>
						<td style="height: 18px; text-align:left; background-color: #' . $Row['code'] . '; ' . 'color: ' . inverseHex('#' . $Row['code']) . '">' . $Row['name'] . '</td>
						<td>';
                        print $Row['code'];
                        print '</td>
						<td align="right">
							<a href="./index.php?module=eshop_product_color_add&eshop=1&color_id=' . $Row['color_id'] . '&amp;update=1">Upraviť</a> &nbsp;
							<a href="javascript:;" onclick="javascript:confirmAction(\'Naozaj zmazať položku?\', \'\', \'index.php?module=eshop_product_color_add&eshop=1&delete=1&amp;color_id=' . $Row['color_id'] . '\');">Zmazať</a></td>
						</tr>
					';
                    endwhile;
                }
                else {
                    $queryString = "select * from " . TABLE_PREFIX . "color where 1 and color_id = '" . $_GET['color_id'] . "';";
                    $Result = mysql_query($queryString);
                }
            else:
                print(mysql_error());
            endif;
            ?>
        </table>
    </div>
</div>