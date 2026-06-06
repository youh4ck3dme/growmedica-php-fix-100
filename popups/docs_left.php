<?php
require_once("../shared/config.inc.php");

if (!$user->isAdmin())
    exit();

$reload = false;
session_start();
if ($_GET['action'] == 'insert') {
if( $_POST['category_name']!='') {
    $query = 'INSERT INTO ' . TABLE_PREFIX . 'docs_category (category_name) VALUES ("' . $_POST['category_name'] . '");';
    $result = mysql_query($query);
    if ($result != 0) {
        $_GET['action'] = '';
        if ($_SESSION['reload'] == 1) {
            $_SESSION['reload'] = 0;

            header("Refresh: 0;");
            ?>
            <script type="text/javascript">
                window.parent.frames['docs_right'].location = 'docs_right.php?category_id=<?=mysql_insert_id()?>';
            </script>

            <?
            exit;
        } else
            echo mysql_error();

        @mysql_free_result($result);
    }
}
}

if ($_GET['action'] == 'delete') {
if ($_SESSION['reload'] != 0 && $_GET['id'] != '') {
    $query = 'SELECT file_name FROM ' . TABLE_PREFIX . 'docs_files WHERE docs_category_id="' . $_GET['id'] . '"';

    $result = mysql_query($query);
    if ($result != 0) {
        if (mysql_num_rows($result) > 0) {
            while ($line = mysql_fetch_object($result)) {
                unlink("files/" . $line->file_name);
            }
        }
        $query1 = 'DELETE FROM ' . TABLE_PREFIX . 'docs_category WHERE docs_category_id="' . $_GET['id'] . '" LIMIT 1;';
        $_GET['id'] = '';
        $result1 = @mysql_query($query1);
        if ($result1 != 0) {
            $_SESSION['reload'] = 0;
            $_GET['action'] = '';
            header("Refresh: 0;");
            ?>
            <script type="text/javascript">
                window.parent.frames['docs_right'].location='docs_right.php?category_id=0';
            </script >
            <?
            header("Refresh: 0;");
            exit;
        } else
            echo mysql_error();
        mysql_free_result($result1);
    } else
        print mysql_error();
    mysql_free_result($result);
}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>Sixadmin iFrame</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="../js/jquery/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/bootbox/bootbox.min.js"></script>
        <script type="text/javascript">
            var rootdir = '<?= ROOTDIR; ?>';
        </script>
        <script type="text/javascript" src="../js/functions-admin.js"></script>
        <link rel="stylesheet" href="../js/bootstrap/css/bootstrap.css" />
        <script type="text/javascript">
            function kontrola(data) {
                if (data == "") {
                    alert("Nazov kategorie je povinny");
                    go = false;
                }
                else go = true;
            }


        </script>
    </head>
    <body>
        <div>
            <form action="?action=insert" method="post" enctype="multipart/form-data">
                <div class="col-md-12 nopadding">
                    <div class="form-group">
                        <label for="category_name">Pridať kategóriu:</label>
                        <input class="form-control" name="category_name" type="text" id="category_name" />
                    </div>
                </div>
                <div class="col-md-12 nopadding">
                    <button class="btn btn-primary" type="submit" name="submit"
                            onclick="kontrola(category_name.value);return go;">Pridať</button>
                    <br />
                    <br />
                </div>
                <div class="col-md-12 nopadding">
                    <h4>Zoznam kategórii</h4>
                    <?
                    $_SESSION['reload'] = 1;
                    $query = 'SELECT docs_category_id AS id, category_name AS name FROM ' . TABLE_PREFIX . 'docs_category;';
                    $result = mysql_query($query);
                    if ($result != 0) {
                        echo '<ul class="list-group">';

                        while ($row = mysql_fetch_object($result)) {
                            echo '<li class="list-group-item">';
                            echo '<a href="#" onClick="javascript:window.parent.frames[\'docs_right\'].location=\'docs_right.php?category_id=' . $row->id . '\';">' . $row->name . '</a>';
                            echo ' <a class="btn btn-default btn-xs btn-danger" href="javascript:confirmWindow(\'Naozaj zmazat tuto kategoriu a vsetky polozky v nej?\', \'docs_left.php?action=delete&id=' . $row->id . '\', \'\');">Zmazať</a>';
                            echo '</li>';
                            //echo "<a href='javascript:;' onClick=\"javascript:window.parent.frames['foto_right'].location = 'foto_right.php?category_id=" . $row->id . "';\" ><strong>" . $row->name . "</strong></a><br>[<a href=\"javascript:ConfirmBoxAc('Naozaj zmazat tuto kategoriu a vsetky polozky v nej?', 'foto_left.php?action=delete&id=" . $row->id . "', '');\"><font color='red'>zmazať</font></a>]<br> ";
                            //echo "<a href='javascript:;' onClick=\"javascript:window.parent.frames['foto_right'].location = 'foto_right.php?category_id=" . $row->id . "';\" ><strong>" . $row->name . "</strong></a><br>[<a href=\"javascript:ConfirmBoxAc('Naozaj zmazat tuto kategoriu a vsetky polozky v nej?', 'foto_left.php?action=delete&id=" . $row->id . "', '');\"><font color='red'>zmazať</font></a>]<br> ";
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
