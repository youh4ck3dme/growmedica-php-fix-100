<?
require_once("../shared/config.inc.php");

if (!$user->isAdmin()) {
    header("Location: ../index.php");
    exit;
}

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
} else {
    $sql_str = "SELECT MAX(docs_category_id) AS id FROM " . TABLE_PREFIX . "docs_category;";
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

function findIconExt($ext) {
    $dok_pole = array(
        "avi.gif" => array("avi", "mpg", "mpeg", "asf"),
        "img.gif" => array("bmp", "jpg", "jpeg", "gif"),
        "pdf.gif" => array("pdf"),
        "pps.gif" => array("ppt", "pps"),
        "word.gif" => array("doc", "txt", "rtf"),
        "xls.gif" => array("xls"),
        "zip.gif" => array("zip", "rar")
    );

    while (list($key, $val) = each($dok_pole))
        while (list($key2, $polozka) = each($val))
            if ($polozka == $ext)
                return $key;
    return "file.gif";
}

function findExtension($str) {
    $c = 0;
    $ex = "";
    $ext = array();
    for ($x = strlen($str); $x >= 0; $x--) {
        if ($str[$x] == ".")
            break;
        else {
            $ext[$c] = $str[$x];
            $c++;
        }
    }
    $ext = array_reverse($ext);
    foreach ($ext as $ch)
        $ex.=$ch;
    return $ex;
}

function FormatSize($fsize) {
    $str = "[ " . round($fsize, 2) . " B ]";
    if ($fsize >= 1024) {
        $fsize = $fsize / 1024;
        $str = "[ " . round($fsize, 2) . " KB ]";
    }
    if ($fsize >= (1024)) {
        $fsize = $fsize / 1024;
        $str = "[ " . round($fsize, 2) . " MB ]";
    }

    return $str;
}

/*
  function ISO88592bezDiakritiky($buf) {
  $ISO_8859 = array(225, 232, 239, 233, 237, 229, 242, 243, 185, 187, 250, 193, 200, 207, 201, 205, 197, 210, 211, 169, 171, 218, 190, 174, 181, 253);
  $Bez_diak = array(97, 99, 100, 101, 105, 108, 110, 111, 115, 116, 117, 97, 99, 100, 101, 105, 108, 110, 111, 115, 116, 117, 122, 122, 108, 121);
  $Windows_1250 = array(225, 232, 239, 233, 237, 229, 242, 243, 154, 157, 250, 193, 200, 207, 201, 205, 197, 210, 211, 138, 141, 218, 158, 142, 190);

  for ($x = 0; $x < strlen($buf); $x++) {
  for ($c = 0; $c < count($ISO_8859); $c++) {
  if ($ISO_8859[$c] == ord($buf[$x])) {
  $buf[$x] = chr($Bez_diak[$c]);
  }
  }
  }

  return substr($buf, 0, 255);
  }
 */

switch ($_GET['action']) {
    case "upload":
        if ($_FILES['file']) {
            $ext = findExtension($_FILES['file']["name"]);
            $filename_bezdkr = String::SEOFriendlyText(reset(explode('.', $_FILES['file']["name"]))) . '.' . $ext;
            if (!file_exists("../docs/$filename_bezdkr")) {
                move_uploaded_file($_FILES['file']["tmp_name"], "../docs/$filename_bezdkr");
                chmod("../docs/$filename_bezdkr", 0666);
                $query = 'INSERT INTO ' . TABLE_PREFIX . 'docs_files VALUES ("", "' . $filename_bezdkr . '", "' . $_GET['category_id'] . '", unix_timestamp(now()));';
                mysql_query($query);
                session_start();
                if ($_SESSION['reload'] == 1) {
                    $_SESSION['reload'] = 0;
                    $_GET['action'] = '';
                    header("Refresh: 0;");
                    exit;
                }
            } else {
                if ($_SESSION['reload'] == 1) {
                    $_SESSION['reload'] = 0;
                    $_GET['action'] = '';
                header("Refresh: 0;");
                exit;}
            }
        } else {
            if ($_SESSION['reload'] == 1) {
                $_SESSION['reload'] = 0;
                $_GET['action'] = '';
            header("Refresh: 0;");
            exit;}
        }
        break;

    case "delete":
        $sql = 'SELECT file_name as name, id FROM ' . TABLE_PREFIX . 'docs_files WHERE id="' . $_GET['id'] . '"';
        if ($result = mysql_query($sql)) {
            if (mysql_num_rows($result) == 1) {
                $row = mysql_fetch_object($result);
                unlink("../docs/" . $row->name);
                if (mysql_query('DELETE FROM ' . TABLE_PREFIX . 'docs_files WHERE id=' . $_GET['id'] . ' LIMIT 1;')) {
                    header("Refresh: 0;");
                    exit;
                }
            }
        } else {
            if (mysql_errno())
                print(mysql_error());
        }
        break;
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
            <form action="?action=upload" method="post" enctype="multipart/form-data" target="docs_right" id="formular">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category_id">Kategória:</label>
                        <select class="form-control" name="category_id" id="category_id">
                            <?= return_combobox("SELECT docs_category_id AS id, category_name AS name FROM " . TABLE_PREFIX . "docs_category;", $category_id); ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="file">Súbor:</label>
                        <input class="form-control" name="file" type="file" id="file" />
                    </div>
                </div>
                <div class="col-md-3">
                    Požadovaný dokument si označte myškou a potom ho jednoduchým ťahaním presuňte do hlavného dialógového okna na požadovanú pozíciu.
                    <br />
                    <br />
                </div>
                <div class="col-md-12">
                    <button class="btn btn-primary" type="submit" name="submit">Uploadovať</button>
                    <br />
                    <br />
                </div>
                <div class="col-md-12">
                    <ul class="nav nav-tabs">
                        <li role="presentation" class="active"><a href="#">Nahrané súbory</a></li>
                    </ul>
                    <br />
                    <ul class="list-group">
                        <?
                        session_start();
                        $_SESSION['reload'] = 1;
                        $query = 'SELECT file_name as name, id, datum, docs_category_id FROM ' . TABLE_PREFIX . 'docs_files WHERE docs_category_id="' . $_GET['category_id'] . '"';
                        $result = mysql_query($query);

                        if (mysql_num_rows($result) > 0) {
                            while ($row = mysql_fetch_object($result)) {
                                $icon_fname = findIconExt(findExtension($row->name));
                                ?>
                                <li class="list-group-item">
                                    <img border="0" src="<?= ROOTDIR . '/images/icons/' . $icon_fname; ?>" align="absmiddle" style="width: 12px; height: 12px;" alt="<?= SEO_TITLE; ?>">
                                        <a href="<?= ROOTDIR . '/docs/' . $row->name ?>" target="_blank"><?= $row->name . ' ' . FormatSize(filesize("../docs/$row->name")); ?></a>
                                        <a class="btn btn-danger" href="?action=delete&docs_category_id=<?= $row->docs_category_id . '&id=' . $row->id ?>" target="_self"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Odstrániť</a>
                                        <span class="btn btn-info"><span class="glyphicon glyphicon-time" aria-hidden="true"></span> Nahrané: <?= date("j. n. Y", $row->datum); ?></span>
                                </li>
                                <?
                            }
                        } else
                            echo "<h4>Nenájdené žiadne súbory</h4>"; // end je sekcia, nie su subory
                        ?>
                    </ul>
                </div>
            </form>
        </div>
    </body>
</html>
