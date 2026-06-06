<?php
require_once("../../shared/config.inc.php");
if (is_array($_POST['items'])) {
    $i = 0;
    foreach ($_POST['items'] as $key => $val) {
        $queryString = "update " . TABLE_PREFIX . "manufacturer set sorter = '" . $i . "' where 1 and manufacturer_id = '" . $val . "';";
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
        <title>Zoradenie výrobcov</title>
        <script type="text/javascript" src="../../js/functions.js"></script>
        <style type="text/css">
            body {
                margin: 0 5px 0 5px;
            }
        </style>
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
                            $queryString = "SELECT manufacturer_id AS id, " . strtolower($_SESSION['lang']) . "_name AS name FROM " . TABLE_PREFIX . "manufacturer WHERE 1 ORDER BY sorter, " . strtolower($_SESSION['lang']) . "_name ASC;";
                            if (!$Result = mysql_query($queryString)) {
                                print("<option>MySql Error (" . mysql_errno() . "): "
                                        . mysql_error() . "</option>");
                            } else {
                                while ($Row = mysql_fetch_assoc($Result)) {
                                    print '<option value="' . $Row['id'] . '"' . ($Row['id'] == $_GET['id'] ? ' selected="selected"' : '') . '>' . $Row['name'] . '</option>';
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
