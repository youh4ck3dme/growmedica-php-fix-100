<?php
require ('../../shared/config.inc.php');
if (is_array($_POST['items'])) {
    $i = 0;
    foreach ($_POST['items'] as $key => $val) {
        $queryString = "update " . TABLE_PREFIX . "_photo_images set sorter = '" . $i . "' where 1 and photo_category_id = '" . $_GET['category_id'] . "' and photo_images_id='" . $val . "';";
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
        <link href="../index.css" rel="stylesheet" type="text/css" />
    </head>

    <body>
        <!-- ZORADENIE -->
        <h1>Zoradenie obrázkov v galérii</h1>

        <form method="post" enctype="multipart/form-data" action="">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr>
                    <td rowspan="2">
                        <select name="items[]" size="10" multiple="multiple" id="items[]" style="width: 98%;">
                            <?php
                            $queryString = "select photo_images_id as id, src from " . TABLE_PREFIX . "_photo_images where 1 and photo_category_id = '" . $_GET['category_id'] . "' order by sorter ASC;";
                            if (!$Result = mysql_query($queryString)) {
                                print("<option>MySql Error (" . mysql_errno() . "): "
                                        . mysql_error() . "</option>");
                            } else {
                                while ($Row = mysql_fetch_assoc($Result)) {
                                    print '<option value="' . $Row['id'] . '">' . $Row['src'] . '</option>';
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
