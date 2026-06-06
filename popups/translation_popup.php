<?
require_once("../shared/config.inc.php");

if (!$user->isAdmin()) {
    header("Location: ../index.php");
    exit;
}

$refresh = false;
switch ($_GET['action']) {


    case "update":
        if (is_numeric($_GET['translation_id'])) {
            //
            //
            // UPDATE
            if (isset($_POST['action']) AND $_POST['action'] == 'update') {
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude .= "`desc_" . strtolower($val) . "` = '" . html_entity_decode($_POST['desc_' . $val], ENT_QUOTES, "UTF-8") . "', ";
                }
                $queryInclude = substr($queryInclude, 0, -2);
                $query = 'UPDATE ' . TABLE_PREFIX . 'translation SET ' . $queryInclude . ' WHERE translation_id="' . mysql_real_escape_string($_GET['translation_id']) . '";';
                $result = mysql_query($query);
                if ($result) {
                    $refresh = true;
                    makeLog("Uprava prekladu", $_POST['desc_sk']);
                } else {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
                @mysql_free_result($ResultA);
            }
            //
            //
            // SELECT
            $query = 'SELECT * FROM ' . TABLE_PREFIX . 'translation WHERE translation_id="' . $_GET['translation_id'] . '";';
            $result = mysql_query($query);
            if ($result) {
                if (mysql_num_rows($result) == 1) {
                    $row = mysql_fetch_object($result);
                }
            } else {
                var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
            }
            @mysql_free_result($ResultA);
        }
        ?>

        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>SixAdmin translate iFrame</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <script type="text/javascript" src="../js/jquery/jquery-1.9.1.min.js"></script>
                <script type="text/javascript" src="../js/bootstrap/js/bootstrap.min.js"></script>
                <script type="text/javascript" src="../js/bootbox/bootbox.min.js"></script>
                <script type="text/javascript">
                    var rootdir = '<?= ROOTDIR; ?>';
                </script>
                <script type="text/javascript" src="../js/functions-admin.js"></script>
                <link rel="stylesheet" href="../js/bootstrap/css/bootstrap.css" />
                <?php if ($refresh) { ?>
                    <script type="text/javascript">
                    window.opener.parent.document.location.reload(true);
                    </script>
                <?php } ?>
            </head>
            <body>
                <div>
                    <form action="" method="post" id="translation">
                        <div class="col-md-12">
                            <h4>Upraviť preklad "<?= $row->label_name; ?>"</h4>
                        </div>
                        <div class="col-md-12">
                            <?
                            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                ?>
                                <div class="form-group">
                                    <label for="desc">Preklad <sup><?= $val; ?></sup>:</label>
                                    <input class="form-control" name="desc_<?= $val; ?>" type="text" id="desc" value="<?= $row->{'desc_' . $val}; ?>" />
                                </div>
                                <?
                            }
                            ?>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-primary" type="submit" name="submit">Upraviť</button>
                            <input type="hidden" name="action" value="update" />
                        </div>
                    </form>
                </div>
                <?
                break;
        }
        ?>
