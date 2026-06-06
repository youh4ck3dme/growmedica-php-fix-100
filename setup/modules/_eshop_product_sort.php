<?php
require_once("../../shared/config.inc.php");

if (is_array($_POST['items'])) {
    $i = 0;
    foreach ($_POST['items'] as $key => $val) {
        $queryString = "update " . TABLE_PREFIX . "product set sorter = '" . $i . "' where 1 and product_id = '" . $val . "';";
        if (!$Result = mysql_query($queryString)) {
            if (mysql_errno())
                print("MySql Error (" . mysql_errno() . "): "
                        . mysql_error() . "<br />");
        }
        $i++;
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Untitled Document</title>
        <script type="text/javascript" src="../../js/functions.js"></script>
        <style type="text/css">
            body {
                margin: 0 5px 0 5px;
            }
        </style>
        <script>
            function selectAllOptions(obj) {
                if (!hasOptions(obj)) {
                    return;
                }
                for (var i = 0; i < obj.options.length; i++) {
                    obj.options[i].selected = true;
                }
            }
            function moveOptionUp(obj) {
                if (!hasOptions(obj)) {
                    return;
                }
                for (i = 0; i < obj.options.length; i++) {
                    if (obj.options[i].selected) {
                        if (i != 0 && !obj.options[i - 1].selected) {
                            swapOptions(obj, i, i - 1);
                            obj.options[i - 1].selected = true;
                        }
                    }
                }
            }

        // -------------------------------------------------------------------
        // moveOptionDown(select_object)
        //  Move selected option in a select list down one
        // -------------------------------------------------------------------
            function moveOptionDown(obj) {
                if (!hasOptions(obj)) {
                    return;
                }
                for (i = obj.options.length - 1; i >= 0; i--) {
                    if (obj.options[i].selected) {
                        if (i != (obj.options.length - 1) && !obj.options[i + 1].selected) {
                            swapOptions(obj, i, i + 1);
                            obj.options[i + 1].selected = true;
                        }
                    }
                }
            }


        </script>

        <link href="../index.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <h1>Zoradenie položiek</h1>
        <form method="post" enctype="multipart/form-data" action="">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr>
                    <td rowspan="2">
                        <select name="items[]" size="10" multiple="multiple" id="items[]" style="width: 98%;">
                            <?php
                            $queryString = "select a.product_id as id, a." . strtolower($_SESSION['lang']) . "_name as name from " . TABLE_PREFIX . "product a
				JOIN " . TABLE_PREFIX . "product_menu b USING (product_id) WHERE 1 " . ((empty($_GET['child_id'])) ? '' : 'AND b.menu_id=' . $_GET["child_id"]) . "
				and deleted!='1' order by sorter;";

                            //$queryString = "select a.product_id as id, a.sk_name as name from " . TABLE_PREFIX . "product a JOIN " . TABLE_PREFIX . "product_menu b USING (product_id) WHERE 1 and b.menu_id= '" . $_GET['child_id'] . "' order by sorter;";


                            if (!$Result = mysql_query($queryString)) {
                                print("<option>MySql Error (" . mysql_errno() . "): "
                                        . mysql_error() . "</option>");
                            } else {
                                while ($Row = mysql_fetch_assoc($Result)) {
                                    print '<option value="' . $Row['id'] . '">' . $Row['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td width="56" valign="top"><input type="button" name="button" id="button" value="Hore" onclick="javascript:moveOptionUp(this.form['items[]']);" /></td>
                </tr>
                <tr>
                    <td valign="bottom"><input type="button" name="button2" id="button2" value="Dole" onclick="javascript:moveOptionDown(this.form['items[]']);" /></td>
                </tr>
                <tr>
                    <td align="center"><input type="submit" name="button3" id="button3" value="Zoradiť" onclick="javascript:selectAllOptions(this.form['items[]']);
                        ;" /></td>
                    <td>&nbsp;</td>
                </tr>
            </table>
        </form>
    </body>
</html>
