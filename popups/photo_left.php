<?php
require_once("../shared/config.inc.php");

if (!$user->isAdmin()) {
    header("Location: ../index.php");
    exit;
}

$reload = false;
session_start();

//if ($_GET['action'] == 'insert') {
//    if ($_POST['category_name'] != '') {
//        $sql_str = "INSERT INTO " . TABLE_PREFIX . "photo_category (name) VALUES ('" . $_POST['category_name'] . "');";
//
//        $result = @mysql_query($sql_str);
//        if ($result != 0) {
//            $_GET['action'] = '';
//            if ($_SESSION['reload'] == 1) {
//                $_SESSION['reload'] = 0;
//                ?>
<!--                <script>-->
<!--                    var inserted = true;-->
<!--                </script>-->
<!--                --><? //
//                header("Refresh: 0;");
//                exit;
//            }
//
//
//        } else
//            print mysql_error();
//
//        @mysql_free_result($result);
//    }
//}

if ($_GET['action'] == 'delete') {
    if ($_SESSION['reload'] != 0 && $_GET['id'] != '') {
        $sql_str = "SELECT src as image_src FROM " . TABLE_PREFIX . "photo_images WHERE photo_category_id='" . $_GET['id'] . "';";

        $result = @mysql_query($sql_str);

        if ($result != 0) {
            if (mysql_num_rows($result) > 0) {
                while ($line = mysql_fetch_object($result)) {
                    $object->RemoveFileEx($line->image_src);
                }
            }
            $sql_str = "DELETE FROM " . TABLE_PREFIX . "photo_category WHERE photo_category_id='" . $_GET['id'] . "';";
            $_GET['id'] = '';
            $result2 = @mysql_query($sql_str);
            if ($result2 != 0) {
                $_SESSION['reload'] = 0;
                $_GET['action'] = '';
                header("Refresh: 0;");
               ?>
                <script type="text/javascript">
                    window.parent.frames['photo_right'].location='photo_right.php?category_id=0';
               </script >
                <?
                exit;

            } else
                print mysql_error();

            @mysql_free_result($result2);
        } else
            print mysql_error();

        @mysql_free_result($result);
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Sixadmin iFrame</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <script type="text/javascript" src="../js/jquery/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="../js/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="../js/bootbox/bootbox.min.js"></script>
    <script type="text/javascript">
        var rootdir = '<?= ROOTDIR; ?>';
    </script>
    <script type="text/javascript">
        function kontrola(data) {
            if (data == "") {
                alert("Nazov kategorie je povinny");
                go = false;
            }
            else go = true;
        }


    </script>
        <?
        if ($_GET['action'] == 'insert') {
if( $_POST['category_name']!='') {
   $sql_str = "INSERT INTO " . TABLE_PREFIX . "photo_category (name) VALUES ('" . $_POST['category_name'] . "');";

   $result = @mysql_query($sql_str);
   if ($result != 0) {
       $_GET['action'] = '';
           if($_SESSION['reload']==1){
               $_SESSION['reload']=0;

                header("Refresh: 0;");
               ?>
               <script type="text/javascript">
                   window.parent.frames['photo_right'].location='photo_right.php?category_id=<?=mysql_insert_id()?>';
               </script >

               <?
        exit;
        }
    } else
        print mysql_error();

    @mysql_free_result($result);
    }
    }
         ?>






    <script type="text/javascript" src="../js/functions-admin.js"></script>
    <link rel="stylesheet" href="../js/bootstrap/css/bootstrap.css"/>
</head>
<body>
<div>
    <form action="?action=insert" method="post" enctype="multipart/form-data" onsubmit="MM_validateForm('category_name', '', 'R');
                    return document.MM_returnValue">
        <div class="col-md-12 nopadding">
            <div class="form-group">
                <label for="category_name">Pridať kategóriu:</label>
                <input class="form-control" name="category_name" type="text" id="category_name"/>
            </div>
        </div>
        <div class="col-md-12 nopadding">
            <button class="btn btn-primary" type="submit" name="submit"
                    onclick="kontrola(category_name.value);return go;">Pridať
            </button>
            <br/>
            <br/>
        </div>
        <div class="col-md-12 nopadding">
            <h4>Zoznam kategórii</h4>
            <?php

            $_SESSION['reload'] = 1;
            $query = 'SELECT photo_category_id AS id, name AS name FROM ' . TABLE_PREFIX . 'photo_category;';
            $result = mysql_query($query);
            if ($result != 0) {
                echo '<ul class="list-group">';

                while ($row = mysql_fetch_object($result)) {
                    echo '<li class="list-group-item">';
                    echo '<a href="#" onClick="javascript:window.parent.frames[\'photo_right\'].location=\'photo_right.php?category_id=' . $row->id . '\';">' . $row->name . '</a>';
                    echo ' <a class="btn btn-default btn-xs btn-danger" href="javascript:confirmWindow(\'Naozaj zmazat tuto kategoriu a vsetky polozky v nej?\', \'photo_left.php?action=delete&id=' . $row->id . '\', \'\');">Zmazať</a>';
                    echo '</li>';
                    //echo "<a href='javascript:;' onClick=\"javascript:window.parent.frames['photo_right'].location = 'photo_right.php?category_id=" . $row->id . "';\" ><strong>" . $row->name . "</strong></a><br>[<a href=\"javascript:ConfirmBoxAc('Naozaj zmazat tuto kategoriu a vsetky polozky v nej?', 'photo_left.php?action=delete&id=" . $row->id . "', '');\"><font color='red'>zmazať</font></a>]<br> ";
                    //echo "<a href='javascript:;' onClick=\"javascript:window.parent.frames['photo_right'].location = 'photo_right.php?category_id=" . $row->id . "';\" ><strong>" . $row->name . "</strong></a><br>[<a href=\"javascript:ConfirmBoxAc('Naozaj zmazat tuto kategoriu a vsetky polozky v nej?', 'photo_left.php?action=delete&id=" . $row->id . "', '');\"><font color='red'>zmazať</font></a>]<br> ";
                }
                echo '</ul>';
            } else
                print mysql_error();

            mysql_free_result($result);
            ?>
        </div>
    </form>
</div>
</body>
</html>
