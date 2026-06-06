<?php
require_once("../shared/config.inc.php");

if (!$user->isAdmin()) {
    header("Location: ../index.php");
    exit;
}

$refresh = false;
switch ($_GET['action']) {
    case "update":
        if (is_numeric($_GET['content_id'])) {
            //
            //
            // UPDATE
            if (isset($_POST['action']) AND $_POST['action'] == 'update') {
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude .= strtolower($val) . '_content="' . mysql_real_escape_string(html_entity_decode($_POST[$val . '_content'], ENT_QUOTES, "UTF-8")) . '", ';
                }
                $queryInclude = substr($queryInclude, 0, -2);
                $query = 'UPDATE ' . TABLE_PREFIX . 'content SET ' . $queryInclude . ' WHERE content_id="' . mysql_real_escape_string($_GET['content_id']) . '";';
                $result = mysql_query($query);
                if ($result) {
                    $refresh = true;
                    makeLog("Uprava statického obsahu", $_POST['sk_content']);
                } else {
                    var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                    return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
                }
            }
            //
            //
            // SELECT
            $query = 'SELECT * FROM ' . TABLE_PREFIX . 'content WHERE content_id="' . mysql_real_escape_string($_GET['content_id']) . '";';
            $result = mysql_query($query);
            if ($result) {
                if (mysql_num_rows($result) == 1) {
                    $row = mysql_fetch_object($result);
                } else {
                    die('Zadané ID nebolo nájdené');
                }
            } else {
                var_deb(mysql_errno() . ': ' . mysql_error(), 'Mysql error');
                return 'MySql Error (' . mysql_errno() . '): ' . mysql_error();
            }
            mysql_free_result($result);
        } else {
            die('Chyba: Prosím zadajte ID!');
        }
        ?>
        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>SixAdmin - úprava statického obsahu</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <script type="text/javascript" src="../js/jquery/jquery-1.9.1.min.js"></script>
                <script type="text/javascript" src="../js/bootstrap/js/bootstrap.min.js"></script>
                <script type="text/javascript" src="../js/bootbox/bootbox.min.js"></script>
                <script type="text/javascript">
                    var rootdir = '<?= ROOTDIR; ?>';
                </script>
                <script type="text/javascript" src="../js/functions-admin.js"></script>
                <link rel="stylesheet" href="../js/bootstrap/css/bootstrap.css" />
                <script type="text/javascript" src="../js/ckeditor/ckeditor.js"></script>
                <?
                if ($refresh) {
                    ?>
                    <script type="text/javascript">
                    window.opener.parent.document.location.reload(true);
                    </script>
                    <?
                }
                ?>
            </head>
            <body>
                <br />
                <div>
                    <form method="post" action="">
                        <div class="col-md-12">
                            <ul class="nav nav-tabs">
                                <?
                                $first = true;
                                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                    ?>
                                    <li id="link_lang_<?= $val; ?>"<?= (($first) ? '  class="active"' : ''); ?>><a href="#" onclick="javascript:getLangTab('<?= $val; ?>');
                                                        return false;"><?= $key; ?></a></li>
                                        <?
                                        $first = false;
                                    }
                                    ?>
                            </ul>
                            <div id="editor-tabs">
                                <?
                                $first = true;
                                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                                    ?>
                                    <div id="tab_lang_<?= String::SEOFriendlyText($val); ?>"<?= (($first) ? ' class="active"' : ' style="display: none;"'); ?>>
                                        <h4>Upraviť statický obsah "<?= $row->label; ?>" <?= ' <sup>' . $val . '</sup>'; ?>:</h4>
                                        <textarea class="ckeditor" name="<?= $val; ?>_content"><?= $row->{$val . '_content'}; ?></textarea>
                                    </div>
                                    <?
                                    $first = false;
                                }
                                ?>
                            </div>
                        </div>
                        <br />
                        <div class="col-md-12">
                            <div class="form-group">
                                <?
                                require_once("foto.php");
                                require_once("docs.php");
                                ?>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit" name="submit">Uložiť</button>
                                <input type="hidden" name="action" value="update" />
                            </div>
                        </div>
                    </form>
                </div>
            <?
        }
        ?>
    </body>
</html>