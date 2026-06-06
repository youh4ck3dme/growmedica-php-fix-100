<?php
require_once("../../shared/config.inc.php");
require_once("../../shared/classes/class.slideshow.php");
if (is_array($_POST['menu_slideshow_id']) and sizeof($_POST["menu_slideshow_id"]) > 0) {

    $queryString = "delete from " . TABLE_PREFIX . "menu_slideshow_prepojenie where 1 and menu_id = '" . $_GET['menu_id'] . "';";
    $Result = mysql_query($queryString);
    if ($Result) {

    } else {
        die(mysql_error());
    }

    foreach ($_POST["menu_slideshow_id"] as $item => $value) {
        $queryString = "insert into " . TABLE_PREFIX . "menu_slideshow_prepojenie (menu_id, menu_slideshow_id) values ('" . $_GET['menu_id'] . "', '" . $value . "');";
        if (!$ResultB = mysql_query($queryString)) {
            if (mysql_errno())
                print("MySql Error (" . mysql_errno() . "): "
                        . mysql_error());
        }
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
        <h1>Priradenie slideshow k položke menu</h1>
        <form method="post" enctype="multipart/form-data" action="">
            <table width="100%" border="0" cellpadding="2" cellspacing="0">
                <tr>
                    <td>
                        <?
                        $_POST['category_id'] = array();

                        $queryString = "select * from " . TABLE_PREFIX . "menu_slideshow_prepojenie where 1 and menu_id = '" . $_GET['menu_id'] . "';";
                        $Result = mysql_query($queryString);
                        if ($Result) {
                            while ($Row = mysql_fetch_array($Result)) {
                                $_POST['category_id'][] = $Row["menu_slideshow_id"];
                            }
                        } else {
                            die(mysql_error());
                        }
                        @mysql_free_result($Result);
                        //Pre($_POST['category_id']);
                        ?>
                        <select name="menu_slideshow_id[]" size="9" multiple="multiple" class="w201px" id="menu_slideshow_id" style="width: 370px">
                            <?php $slideshow->getCategoriesMultiSelect(); ?>
                        </select>
                    </td>


                </tr>
                <tr>
                    <td align="center"><input type="submit" name="button3" id="button3" value="Priradiť" /></td>
                </tr>
            </table>
        </form>
        <?php require ('_slideshow_sort.php') ?>
    </body>
</html>
