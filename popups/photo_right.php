<?
require_once("../shared/config.inc.php");

if (!$user->isAdmin()) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
} else {
    $sql_str = "SELECT MAX(photo_category_id) AS id FROM " . TABLE_PREFIX . "photo_category;";
    $result = @mysql_query($sql_str);
    if ($result != 0) {
        if (mysql_num_rows($result) > 0) {
            $line = mysql_fetch_object($result);
            $category_id = $line->id;
        } else {
            $category_id = 0;
        }
        mysql_free_result($result);
    } else {
        echo mysql_error();
    }
}

if ($_GET['action'] == 'delete') {
    unlink('../photos/thumbnail/' . $_GET['image-src']);
    unlink('../photos/preview/' . $_GET['image-src']);
    unlink('../photos/original/' . $_GET['image-src']);

    $queryString = "DELETE FROM " . TABLE_PREFIX . "photo_images WHERE src='" . $_GET['image-src'] . "';";
    if (!$result = mysql_query($queryString))
        if (mysql_errno())
            echo 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
}

if ($_GET['action'] == 'upload') {

    // pocet odoslanych priloh
    $count = count($_FILES['image_file']['name']);
    $image = new abeautifulsite\SimpleImage();

    try {

        // prechod a upload odoslanych priloh
        for ($i = 0; $i < $count; $i++) {

            //print $_FILES['image_file']['name'][$i];
            if (sizeof($_FILES['image_file']['tmp_name'][$i]) > 0) {

                $file_name = explode('.', $_FILES['image_file']['name'][$i]);
                $ext = pathinfo($_FILES['image_file']["name"][$i], PATHINFO_EXTENSION);
                $new_name = time() . '-' . String::SEOFriendlyText($file_name[0]) . '.' . $ext;

                if (GALLERY_TRANSFORM_TYPE == 'adaptive_resize') { // adaptive_resize (w,h), best_fit (w,h), fit_to_width (w), fit_to_height (h)
                    $thumb_dm = explode(',', THUMBS_DIMENSIONS);
                    $preview_dm = explode(',', PREVIEW_DIMENSIONS);
                    if (!$image->load($_FILES['image_file']['tmp_name'][$i])->save("../photos/original/" . $new_name)->adaptive_resize($preview_dm[0], $preview_dm[1])->save("../photos/preview/" . $new_name)->adaptive_resize($thumb_dm[0], $thumb_dm[1])->save("../photos/thumbnail/" . $new_name)) {
                        $_SESSION['_message'] = '<strong class="red">Obrázok nebol nahraný! Skúste znova</strong>';
                        break;
                    }
                } elseif (GALLERY_TRANSFORM_TYPE == 'best_fit') {
                    $thumb_dm = explode(',', THUMBS_DIMENSIONS);
                    $preview_dm = explode(',', PREVIEW_DIMENSIONS);
                    if (!$image->load($_FILES['image_file']['tmp_name'][$i])->save("../photos/original/" . $new_name)->best_fit($preview_dm[0], $preview_dm[1])->save("../photos/preview/" . $new_name)->best_fit($thumb_dm[0], $thumb_dm[1])->save("../photos/thumbnail/" . $new_name)) {
                        $_SESSION['_message'] = '<strong class="red">Obrázok nebol nahraný! Skúste znova</strong>';
                        break;
                    }
                } elseif (GALLERY_TRANSFORM_TYPE == 'fit_to_width') {
                    if (!$image->load($_FILES['image_file']['tmp_name'][$i])->save("../photos/original/" . $new_name)->fit_to_width(PREVIEW_DIMENSIONS)->save("../photos/preview/" . $new_name)->fit_to_width(THUMBS_DIMENSIONS)->save("../photos/thumbnail/" . $new_name)) {
                        $_SESSION['_message'] = '<strong class="red">Obrázok nebol nahraný! Skúste znova</strong>';
                        break;
                    }
                } elseif (GALLERY_TRANSFORM_TYPE == 'fit_to_height') {
                    if (!$image->load($_FILES['image_file']['tmp_name'][$i])->save("../photos/original/" . $new_name)->fit_to_height(PREVIEW_DIMENSIONS)->save("../photos/preview/" . $new_name)->fit_to_height(THUMBS_DIMENSIONS)->save("../photos/thumbnail/" . $new_name)) {
                        $_SESSION['_message'] = '<strong class="red">Obrázok nebol nahraný! Skúste znova</strong>';
                        break;
                    }
                }

                $queryString = mysql_query("INSERT INTO " . TABLE_PREFIX . "photo_images (photo_category_id, menu_id, name, description, src, image_type, owner, date, sorter)
                            VALUES ('" . $_POST["category_id"] . "', '" . $_POST["menu_id"] . "','" . $_POST["name"] . "', '" . $_POST["description"] . "', '" . $new_name . "', '" . $ext . "', '" . $_POST["owner"] . "', NOW(), '" . $_POST["sorter"] . "');");
            }
        }
    } catch (Exception $e) {
        $_SESSION['_message'] = '<strong class="red">' . $e->getMessage() . '</strong>';
    }
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>SixAdmin iFrame</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <script type="text/javascript" src="../js/jquery/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="../js/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="../js/bootbox/bootbox.min.js"></script>
        <script type="text/javascript">
            var rootdir = '<?= ROOTDIR; ?>';
        </script>
        <script type="text/javascript" src="../js/functions-admin.js"></script>
        <link rel="stylesheet" href="../js/bootstrap/css/bootstrap.css" />
    </head>
    <body>
        <div>
            <form action="?action=upload" method="post" enctype="multipart/form-data" target="photo_right" id="formular" onsubmit="return Over(this);">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category_id">Kategória:</label>
                        <select class="form-control" name="category_id" id="category_id">
                            <?= return_combobox("SELECT photo_category_id AS id, name AS name FROM " . TABLE_PREFIX . "photo_category;", $category_id); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category_id">Súbor:</label>
                        <input class="form-control" name="image_file[]" type="file" id="image_file" multiple />
                    </div>
                </div>
                <div class="col-md-12">
                    <button class="btn btn-primary" type="submit" name="submit">Uploadovať</button>
                    <br />
                    <br />
                </div>
            </form>
            <div class="col-md-12">
                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#sectionA">Klikateľný náhľad</a></li>
                    <li><a data-toggle="tab" href="#sectionB">Malý, neklikateľný náhľad</a></li>
                    <li><a data-toggle="tab" href="#sectionC">Veľký, neklikateľný</a></li>
                </ul>
                <div class="tab-content">
                    <div id="sectionA" class="tab-pane fade in active">
                        <h3>Klikateľný náhľad</h3>
                        <?
                        $query = "SELECT *, name AS image_name, src AS image_src FROM " . TABLE_PREFIX . "photo_images
                              WHERE 1 AND photo_category_id='" . $category_id . "'
                              ORDER BY photo_images_id DESC;";
                        $result = mysql_query($query);
                        if ($result != 0) {
                            if (mysql_num_rows($result) != 0) {
                                echo '<div class="row">';
                                while ($row = mysql_fetch_object($result)) {
                                    echo '<div class="col-xs-2 col-md-2">';
                                    //echo '<a href="#" class="thumbnail" onClick="javascript:openPopupWindow(\'image-processor\', \'' . ROOTDIR . '/popups/popup.php?action=display&image-path=thumbnail/' . $row->image_src . '&popup=1\', 800, 600);return false;">';
                                    echo '<a href="#" class="thumbnail" onClick="javascript:insertImageToCkeditor(\'' . ROOTDIR . '/photos/thumbnail/' . $row->image_src . '\', \'' . ROOTDIR . '/photos/original/' . $row->image_src . '\', \'' . SEO_TITLE . '\', \'fancybox\');return false;">';
                                    echo '<img src="../photos/thumbnail/' . $row->image_src . '" alt="' . $row->image_src . '">';
                                    echo '</a>';
                                    echo '<div class="caption">';
                                    echo '<p><a href="javascript:confirmWindow(\'Naozaj chcete odstrániť tento obrázok?\', \'photo_right.php?action=delete&image-src=' . $row->image_src . '&category_id=' . $category_id . '\', \'\');" class="btn btn-primary" role="button">Zmazať</a></p>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                echo '</div>';
                                mysql_data_seek($result, 0);
                            } else {
                                echo '<p>Vybraná kategória neobsahuje žiaden obrázok</p>';
                            }
                        } else {
                            echo mysql_error();
                        }
                        ?>
                    </div>
                    <div id="sectionB" class="tab-pane fade">
                        <h3>Malý, neklikateľný náhľad</h3>
                        <?
                        if ($result != 0) {
                            if (mysql_num_rows($result) != 0) {
                                echo '<div class="row">';
                                while ($row = mysql_fetch_object($result)) {
                                    echo '<div class="col-xs-2 col-md-2">';
                                    //echo '<a href="#" class="thumbnail" onClick="javascript:openPopupWindow(\'image-processor\', \'' . ROOTDIR . '/popups/popup.php?action=display&image-path=thumbnail/' . $row->image_src . '&popup=2\', 800, 600);return false;">';
                                    echo '<a href="#" class="thumbnail" onClick="javascript:insertImageToCkeditor(\'' . ROOTDIR . '/photos/thumbnail/' . $row->image_src . '\', \'\', \'' . SEO_TITLE . '\', \'\');return false;">';
                                    echo '<img src="../photos/thumbnail/' . $row->image_src . '" alt="' . $row->image_src . '">';
                                    echo '</a>';
                                    echo '<div class="caption">';
                                    echo '<p><a href="javascript:confirmWindow(\'Are you sure y;ou want to delete this picture?\', \'photo_right.php?action=delete&image-src=' . $row->image_src . '&category_id=' . $category_id . '\', \'\');" class="btn btn-primary" role="button">Zmazať</a></p>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                echo '</div>';
                                mysql_data_seek($result, 0);
                            } else {
                                echo '<p>Vybraná kategória neobsahuje žiaden obrázok</p>';
                            }
                        } else {
                            echo mysql_error();
                        }
                        ?>
                    </div>
                    <div id="sectionC" class="tab-pane fade">
                        <h3>Veľký, neklikateľný</h3>
                        <?
                        if ($result != 0) {
                            if (mysql_num_rows($result) != 0) {
                                echo '<div class="row">';
                                while ($row = mysql_fetch_object($result)) {
                                    echo '<div class="col-xs-2 col-md-2">';
                                    //echo '<a href="#" class="thumbnail" onClick="javascript:openPopupWindow(\'image-processor\', \'' . ROOTDIR . '/popups/popup.php?action=display&image-path=thumbnail/' . $row->image_src . '&popup=3\', 800, 600);return false;">';
                                    echo '<a href="#" class="thumbnail" onClick="javascript:insertImageToCkeditor(\'' . ROOTDIR . '/photos/original/' . $row->image_src . '\', \'\', \'' . SEO_TITLE . '\', \'\');return false;">';
                                    echo '<img src="../photos/thumbnail/' . $row->image_src . '" alt="' . $row->image_src . '">';
                                    echo '</a>';
                                    echo '<div class="caption">';
                                    echo '<p><a href="javascript:confirmWindow(\'Are you sure y;ou want to delete this picture?\', \'photo_right.php?action=delete&image-src=' . $row->image_src . '&category_id=' . $category_id . '\', \'\');" class="btn btn-primary" role="button">Zmazať</a></p>';
                                    echo '</div>';
                                    echo '</div>';
                                }
                                echo '</div>';
                                mysql_free_result($result);
                            } else {
                                echo '<p>Vybraná kategória neobsahuje žiaden obrázok</p>';
                            }
                        } else {
                            echo mysql_error();
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
