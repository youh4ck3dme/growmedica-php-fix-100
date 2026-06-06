<?
if (isset($_REQUEST['photo_article_id'])) {
    $photo_article_id = $_REQUEST['photo_article_id'];
} else {
    $sql_str = "SELECT MAX(a.article_id) AS id FROM " . TABLE_PREFIX . "article AS a LEFT JOIN " . TABLE_PREFIX . "article_category AS ac USING(article_category_id) WHERE 1;";
    $result = @mysql_query($sql_str);
    if ($result != 0) {
        if (mysql_num_rows($result) > 0) {
            $line = mysql_fetch_object($result);
            $photo_article_id = $line->id;
        } else {
            $photo_article_id = 0;
        }
    } else
        echo mysql_error();
    @mysql_free_result($result);
}
//
// Odstránenie obrázka
if ($_GET['action'] == 'delete') {
    @unlink('../photos/thumbnail/' . $_GET['image_src']);
    @unlink('../photos/preview/' . $_GET['image_src']);
    @unlink('../photos/original/' . $_GET['image_src']);

    $queryString = "DELETE FROM " . TABLE_PREFIX . "photo_images WHERE src = '" . $_GET['image_src'] . "';";
    if (!$result = mysql_query($queryString))
        if (mysql_errno())
            echo "MySql Error (" . mysql_errno() . "): " . mysql_error();
}
//
// Pridanie obrázka
if ($_POST['UploadBtn'] == 'Pridať') {
    // pocet odoslanych priloh
    $count = count($_FILES['image_file']['name']);

    require_once("../shared/classes/class.image.php");
    $image = new abeautifulsite\SimpleImage();
    try {

        // prechod a upload odoslanych priloh
        for ($i = 0; $i < $count; $i++) {

            if (sizeof($_FILES['image_file']['tmp_name'][$i]) > 0) {

                $file_name = explode('.', $_FILES['image_file']['name'][$i]);
                $ext = pathinfo($_FILES['image_file']["name"][$i], PATHINFO_EXTENSION);
                $new_name = User::randomGenerator() . '-' . String::SEOFriendlyText($file_name[0]) . '.' . $ext; //time() . '-' . String::SEOFriendlyText($file_name[0]) . '.' . $ext;
                $thumb_dm = explode(',', THUMBS_DIMENSIONS);
                $preview_dm = explode(',', PREVIEW_DIMENSIONS);
                // original
                $image->load($_FILES['image_file']['tmp_name'][$i])->save("../photos/original/" . $new_name);
                if (GALLERY_TRANSFORM_TYPE == 'adaptive_resize') { // adaptive_resize (w,h), best_fit (w,h), fit_to_width (w), fit_to_height (h)
                    $image->load($_FILES['image_file']['tmp_name'][$i])->adaptive_resize($preview_dm[0], $preview_dm[1])->save("../photos/preview/" . $new_name);
                    $image->load($_FILES['image_file']['tmp_name'][$i])->adaptive_resize($thumb_dm[0], $thumb_dm[1])->save("../photos/thumbnail/" . $new_name);
                } elseif (GALLERY_TRANSFORM_TYPE == 'best_fit') {
                    $image->load($_FILES['image_file']['tmp_name'][$i])->best_fit($preview_dm[0], $preview_dm[1])->save("../photos/preview/" . $new_name);
                    $image->load($_FILES['image_file']['tmp_name'][$i])->best_fit($thumb_dm[0], $thumb_dm[1])->save("../photos/thumbnail/" . $new_name);
                } elseif (GALLERY_TRANSFORM_TYPE == 'fit_to_width') {
                    $image->load($_FILES['image_file']['tmp_name'][$i])->fit_to_width($preview_dm[0])->save("../photos/preview/" . $new_name);
                    $image->load($_FILES['image_file']['tmp_name'][$i])->fit_to_width($thumb_dm[0])->save("../photos/thumbnail/" . $new_name);
                } elseif (GALLERY_TRANSFORM_TYPE == 'fit_to_height') {
                    $image->load($_FILES['image_file']['tmp_name'][$i])->fit_to_height($preview_dm[0])->save("../photos/preview/" . $new_name);
                    $image->load($_FILES['image_file']['tmp_name'][$i])->fit_to_height($thumb_dm[0])->save("../photos/thumbnail/" . $new_name);
                }

                $queryString = mysql_query("INSERT INTO " . TABLE_PREFIX . "photo_images (photo_article_id, name, description, src, image_type, owner, date, sorter)
                            VALUES ('" . $_POST["photo_article_id"] . "','" . $_POST["name"] . "', '" . $_POST["description"] . "', '" . $new_name . "', '" . $ext . "', '" . $_POST["owner"] . "', NOW(), '" . $_POST["sorter"] . "');");
            }
        }
    } catch (Exception $e) {
        $_SESSION['_message'] = '<strong class="red">' . $e->getMessage() . '</strong>';
        die();
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    header("Location:index.php?module=article_gallery&photo_article_id=" . $_REQUEST['photo_article_id']);
    exit;
}
//
// Úprava popisu
if ($_POST["UploadBtn"] == "Upraviť") {

    $queryString = "UPDATE " . TABLE_PREFIX . "photo_images SET name = '" . $_POST["name"] . "', description = '" . $_POST["description"] . "', photo_article_id = '" . $_POST["photo_article_id"] . "' WHERE photo_images_id = '" . $_POST["_id"] . "' LIMIT 1;";
    if (!$Result = mysql_query($queryString)) {
        if (mysql_errno())
            die("MySql Error (" . mysql_errno() . "): " . mysql_error());
    }else {
        header("Location:index.php?module=article_gallery&photo_images_id=" . $_POST['_id']);
        exit;
    }
}
//
// Úprava poradia
if (isset($_POST["photos"]) and sizeof($_POST["photos"]) > 0) {
    $i = 0;
    foreach ($_POST["photos"] as $key => $val) {
        $queryString = "UPDATE " . TABLE_PREFIX . "photo_images SET sorter = '" . $i . "' WHERE photo_images_id = '" . $val . "';";
        if (!$Result = mysql_query($queryString)) {
            if (mysql_errno())
                echo "MySql Error (" . mysql_errno() . "): " . mysql_error();
        }
        $i++;
    }
}
//
// Výber údajov do formulára
if (is_numeric($_GET["photo_images_id"])) {
    $queryString = "SELECT * FROM " . TABLE_PREFIX . "photo_images WHERE 1 AND photo_images_id = '" . $_GET["photo_images_id"] . "';";
    if ($ResultT = mysql_query($queryString)) {
        if (mysql_num_rows($ResultT) == 1) {
            $RowT = mysql_fetch_assoc($ResultT);
            $photo_article_id = $RowT['photo_article_id'];
        }
    } else {
        echo "MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />";
    }
}
$images_pline = 3;
?>
<div id="leftMenu">
    <h2>Správa fotogalérií</h2>
    <p>Ako prvé vytvorte fotogalériu v štruktúre portálu (prepojenie s modulom Fotogaléria). Následne sa objaví v zozname kategórií naľavo. </p>
    <p>Súbory do galérie môžete nahrávať každý osobitne alebo hromadne - označíte si viacero súborov naraz.</p>
    <div id="submenu">
        <a href="index.php?module=gallery&amp;action=insert" id="addNew">Galéria</a>
    </div>
</div>
<div id="moduleContent">
    <script type="text/javascript">
        function redirectFotogaleriaSetup(value) {
            location.href = "index.php?module=article_gallery&photo_article_id=" + value;
        }
    </script>
    <?
    if (isset($_SESSION['_message'])) {
        echo message($_SESSION['_message']);
        unset($_SESSION['_message']);
    }
    ?>
    <h1>Galéria k článkom</h1>
    <table border="0" cellspacing="0" cellpadding="2" class="tablelist" summary="">
        <tr>
            <th>&nbsp;</th>
        </tr>
    </table>
    <form action="" method="post" enctype="multipart/form-data" id="formular">
        <table border="0" cellpadding="0" cellspacing="0" class="tableform gallery">
            <tbody>
                <tr class="half">
                    <td>
                        <strong>Kategória:</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <select name="photo_article_id" id="photo_article_id" onChange="redirectFotogaleriaSetup(this.value);">
                            <?php
                            //if($              user->isAdmin())
                            echo return_combobox('SELECT a.article_id AS id, CONCAT("[", ac.sk_name,"] ", a.sk_name) AS name FROM ' . TABLE_PREFIX . 'article AS a LEFT JOIN ' . TABLE_PREFIX . 'article_category AS ac USING(article_category_id) WHERE 1 AND a.article_category_id!="0" ORDER BY a._date DESC', $photo_article_id);
                            //else
                            //	echo return_combobox("SELECT pc.photo_article_id AS id, pc.sk_name as name FROM " . TABLE_PREFIX . "user_rights left join ".TABLE_PREFIX."menu as pc using (photo_article_id) where 1 and user_id = '" . $_SESSION['user_id'] . "' and photo_article_id !='' and photo_article_id !='0' and photo_article_id is not null and module_id = '4';", $category_id));
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Súbor:</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input name="image_file[]" type="file" id="image_file" multiple />
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Názov:</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="text" name="name" id="name" style="width: 200px;" value="<?= $RowT["name"] ?>" />
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Popis fotografie:</strong>
                    </td>
                </tr>
                <tr>
                    <td class="editor">
                        <textarea id="ckeditor" class="ckeditor" name="description"><?= $RowT['description'] ?></textarea>
                        <input type="hidden" name="_id" id="_id" value="<?= $RowT["photo_images_id"] ?>" />
                        <input name="UploadBtn" id="UploadBtn" type="submit" value="<?= (!is_numeric($_GET["photo_images_id"])) ? 'Pridať' : 'Upraviť' ?>" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <form method="post" enctype="multipart/form-data" action="">
        <table border="0" cellpadding="0" cellspacing="0" class="tableform gallery order">
            <tbody>
                <tr class="half">
                    <td colspan="2">
                        <strong>Zoradenie</strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <select size="5" multiple="multiple" name="photos[]">
                            <?php
                            $queryString = "select pi.*, a.sk_name AS cat_name FROM " . TABLE_PREFIX . "photo_images AS pi
                                        LEFT JOIN " . TABLE_PREFIX . "article AS a ON photo_article_id=a.article_id
                                        WHERE 1 AND photo_article_id = '" . $photo_article_id . "' ORDER BY sorter ASC;";
                            if ($Result = mysql_query($queryString)) {
                                while ($Row = mysql_fetch_assoc($Result)) {
                                    $name = explode('-', $Row["src"]);
                                    array_shift($name);
                                    echo '<option value="' . $Row["photo_images_id"] . '">' . implode('-', $name) . ' [' . $Row["cat_name"] . ']</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                    <td class="btns">
                        <input type="button" onclick="javascript:moveOptionUp(this.form['photos[]']);" value="Hore" style="width: 56px;float:left;">
                        <div class="clear"></div>
                        <input type="button" onclick="javascript:moveOptionDown(this.form['photos[]']);" value="Dole" style="width: 56px;float:left;">
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" value="Zoradiť" onclick="javascript:selectAllOptions(this.form['photos[]']);" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <?php
        $sql_str = "SELECT CEIL(COUNT(photo_images_id) / " . $images_pline . ") AS count FROM " . TABLE_PREFIX . "photo_images WHERE 1 AND photo_article_id = '" . $photo_article_id . "';";
        $result = @mysql_query($sql_str);
        if ($result != 0) {
            if (mysql_num_rows($result) > 0) {
                $line = mysql_fetch_object($result);
                if ($line->count > 0) {
                    for ($i = 0; $i < $line->count; $i++) {
                        $sql_str = "SELECT * FROM " . TABLE_PREFIX . "photo_images WHERE 1 AND photo_article_id = '" . $photo_article_id . "' order by sorter asc LIMIT " . ($i * $images_pline) . "," . $images_pline . ";";
                        $result2 = @mysql_query($sql_str);
                        if ($result2 != 0) {
                            $num_rows = mysql_num_rows($result2);
                            if ($num_rows > 0) {
                                $j = 0;
                                echo "<TR>\n";
                                while ($line2 = mysql_fetch_object($result2)) {
                                    $dlink = "<br>[<a href=\"javascript:ConfirmBoxAc('Naozaj zmazať fotografiu?', 'index.php?module=article_gallery&action=delete&amp;image_src=" . $line2->src . "&amp;photo_article_id=" . $photo_article_id . "', '');\">ZMAZAŤ</a>]";
                                    $ilink = '<a href="index.php?module=article_gallery&photo_images_id=' . $line2->photo_images_id . '"><img src="' . ROOTDIR . '/photos/thumbnail/' . $line2->src . '" border="0"></a>';
                                    $j++;
                                    if (($j - 1) == $num_rows) {
                                        echo "<TD COLSPAN=" . ($images_pline - $num_rows) + ($images_pline - ($num_rows - 1)) . " WIDTH=\"80\" HEIGHT=\"60\" alidn=\"center\" valign=\"top\">\n";
                                        echo "	  " . $ilink . "<br>" . reset(explode(".", $line2->src)) . "\n" . $dlink;
                                        echo "</TD>\n";
                                    } else {
                                        $name = explode('-', $line2->src);
                                        array_shift($name);
                                        echo "<TD WIDTH=\"80\" HEIGHT=\"60\" align=\"center\" valign=\"top\">" . $ilink . "<br><a href=\"index.php?module=article_gallery&photo_images_id=" . $line2->photo_images_id . "\"><strong>" . implode('-', $name) . "</strong></a>" . $dlink . "</TD>";
                                    }
                                    if (($j) < $num_rows) {
                                        echo "<TD>&nbsp;</TD>";
                                    }
                                }
                                echo "</TR>\n";
                                echo "<tr><td colspan=\"8\">&nbsp;</td></tr>";
                            }
                        } else
                            echo mysql_error();
                        @mysql_free_result($result2);
                    }
                }
            }
        } else
            echo mysql_error();
        @mysql_free_result($result);
        ?>
    </table>
    <!--
    <table width="100%" border="0" cellspacing="0" cellpadding="2">
        <tr>
            <td>
                <form action="" method="post" enctype="multipart/form-data" id="formular">
    <?php
    if (is_numeric($_GET["photo_images_id"])) {
        $queryString = "SELECT * FROM " . TABLE_PREFIX . "photo_images WHERE 1 AND photo_images_id = '" . $_GET["photo_images_id"] . "';";
        if ($ResultT = mysql_query($queryString)) {
            if (mysql_num_rows($ResultT) == 1) {
                $RowT = mysql_fetch_assoc($ResultT);
                $photo_article_id = $RowT['photo_article_id'];
            }
        } else {
            echo "MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />";
        }
    }
    ?>
                    <table width="100%" border="0" cellpadding="0" cellspacing="0" id="tableform">
                        <tr>
                            <td valign="top" style="width: 121px;">
                                <strong>Kategória:</strong><br />
                                <select name="photo_article_id" id="photo_article_id" onChange="redirectFotogaleriaSetup(this.value);">
    <?php
//if($              user->isAdmin())
    echo return_combobox('SELECT a.article_id AS id, CONCAT("[", ac.sk_name,"] ", a.sk_name) AS name FROM ' . TABLE_PREFIX . 'article AS a LEFT JOIN ' . TABLE_PREFIX . 'article_category AS ac USING(article_category_id) WHERE 1 AND a.article_category_id!="0" ORDER BY a._date DESC', $photo_article_id);
//else
//	echo return_combobox("SELECT pc.photo_article_id AS id, pc.sk_name as name FROM " . TABLE_PREFIX . "user_rights left join ".TABLE_PREFIX."menu as pc using (photo_article_id) where 1 and user_id = '" . $_SESSION['user_id'] . "' and photo_article_id !='' and photo_article_id !='0' and photo_article_id is not null and module_id = '4';", $category_id));
    ?>
                                </select>
                                <strong><br />Súbor:</strong><br />
                                <input name="image_file[]" type="file" id="image_file" multiple />
                                <br />
                                <strong>Názov:</strong><br />
                                <input type="text" name="name" id="name" style="width: 200px;" value="<?= $RowT["name"] ?>" />
                                <br />
                                <strong>Popis fotografie:</strong><br />
                                <div class="w-420">
                                    <textarea id="ckeditor" class="ckeditor" name="description"><?= $RowT['description'] ?></textarea>
                                </div>
                                <input type="hidden" name="_id" id="_id" value="<?= $RowT["photo_images_id"] ?>" />
                                <input name="UploadBtn" id="UploadBtn" type="submit" value="<?= (!is_numeric($_GET["photo_images_id"])) ? 'Pridať' : 'Upraviť' ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0">
    <?php
    $sql_str = "SELECT CEIL(COUNT(photo_images_id) / " . $images_pline . ") AS count FROM " . TABLE_PREFIX . "photo_images WHERE 1 AND photo_article_id = '" . $photo_article_id . "';";
    $result = @mysql_query($sql_str);
    if ($result != 0) {
        if (mysql_num_rows($result) > 0) {
            $line = mysql_fetch_object($result);
            if ($line->count > 0) {
                for ($i = 0; $i < $line->count; $i++) {
                    $sql_str = "SELECT * FROM " . TABLE_PREFIX . "photo_images WHERE 1 AND photo_article_id = '" . $photo_article_id . "' order by sorter asc LIMIT " . ($i * $images_pline) . "," . $images_pline . ";";
                    $result2 = @mysql_query($sql_str);
                    if ($result2 != 0) {
                        $num_rows = mysql_num_rows($result2);
                        if ($num_rows > 0) {
                            $j = 0;
                            echo "<TR>\n";
                            while ($line2 = mysql_fetch_object($result2)) {
                                $dlink = "<br>[<a href=\"javascript:ConfirmBoxAc('Naozaj zmazať fotografiu?', 'index.php?module=article_gallery&action=delete&amp;image_src=" . $line2->src . "&amp;photo_article_id=" . $photo_article_id . "', '');\">ZMAZAŤ</a>]";
                                $ilink = '<a href="index.php?module=article_gallery&photo_images_id=' . $line2->photo_images_id . '"><img src="' . ROOTDIR . '/photos/thumbnail/' . $line2->src . '" border="0"></a>';
                                $j++;
                                if (($j - 1) == $num_rows) {
                                    echo "<TD COLSPAN=" . ($images_pline - $num_rows) + ($images_pline - ($num_rows - 1)) . " WIDTH=\"80\" HEIGHT=\"60\" alidn=\"center\" valign=\"top\">\n";
                                    echo "	  " . $ilink . "<br>" . reset(explode(".", $line2->src)) . "\n" . $dlink;
                                    echo "</TD>\n";
                                } else {
                                    $name = explode('-', $line2->src);
                                    array_shift($name);
                                    echo "<TD WIDTH=\"80\" HEIGHT=\"60\" align=\"center\" valign=\"top\">" . $ilink . "<br><a href=\"index.php?module=article_gallery&photo_images_id=" . $line2->photo_images_id . "\"><strong>" . implode('-', $name) . "</strong></a>" . $dlink . "</TD>";
                                }
                                if (($j) < $num_rows) {
                                    echo "<TD>&nbsp;</TD>";
                                }
                            }
                            echo "</TR>\n";
                            echo "<tr><td colspan=\"8\">&nbsp;</td></tr>";
                        }
                    } else
                        echo mysql_error();
                    @mysql_free_result($result2);
                }
            }
        }
    } else
        echo mysql_error();
    @mysql_free_result($result);
    ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <form method="post" enctype="multipart/form-data" action="">
                    <table summary="" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                        <tr>
                            <td style="width: 423px;">
                                <select size="5" multiple="multiple" style="width: 100%; height: 156px;" name="photos[]">
    <?php
    echo $queryString = "select pi.*, m.sk_name AS cat_name from " . TABLE_PREFIX . "photo_images AS pi
                                                    LEFT JOIN " . TABLE_PREFIX . "menu AS m USING(photo_article_id)
                                                    where 1 and photo_article_id = '" . $photo_article_id . "' order by sorter asc;";
    if ($Result = mysql_query($queryString)) {
        while ($Row = mysql_fetch_assoc($Result)) {
            $name = explode('-', $Row["src"]);
            array_shift($name);
            echo '<option value="' . $Row["photo_images_id"] . '">' . implode('-', $name) . ' [' . $Row["cat_name"] . ']</option>';
        }
    }
    ?>
                                </select>
                            </td>
                            <td style="padding: 0 0 0 20px;">
                                <input type="button" onclick="javascript:moveOptionUp(this.form['photos[]']);" value="Hore" style="width: 56px;float:left;">
                                <div class="clear" style="height: 3px;"></div>
                                <input type="button" onclick="javascript:moveOptionDown(this.form['photos[]']);" value="Dole" style="width: 56px;float:left;">
                            </td>
                        </tr>
                        <tr>
                            <td align="center"><input type="submit" value="Zoradiť" onclick="javascript:selectAllOptions(this.form['photos[]']);" /></td>
                            <td></td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
    </table>
    -->
    <table border="0" cellspacing="0" cellpadding="2" class="tablelist" summary="">
        <tr>
            <th>&nbsp;</th>
        </tr>
    </table>
</div>
<script type="text/javascript">
    CKEDITOR.replace('ckeditor', {
        customConfig: 'basic-config.js'
    });
</script>