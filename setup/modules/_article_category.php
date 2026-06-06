<div id="leftMenu">
    <h2>Články</h2>
    <p>V tejto sekcii sa spravuje štruktúra stránky a jednotlivé podstránky.</p>
    <div id="submenu">
        <a href="index.php?module=article&amp;action=insert" class="addNew">Pridať <br /> článok</a>
        <a href="index.php?module=article_gallery&amp;action=insert" class="addNew">Galéria k<br /> článkom</a>
        <a href="index.php?module=article_category" class="detail">Kategórie<br /> článkov</a>
        <a href="index.php?module=article_category&amp;action=insert" class="addNew">Pridať <br /> kategóriu článok</a>
    </div>
</div>
<div id="moduleContent">
    <?php
    switch ($_GET['action']) {
        case "insert":
            $defaultLang = $cLanguage->getLanguageCodes();
            $defaultLang = $defaultLang[0];
            if (isset($_POST[$defaultLang . '_name'])) {
                //	zmenime  udaje   v   databaze
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {

                    $queryInclude1 .= '`' . strtolower($val) . '_name`, `' . strtolower($val) . '_name_seo`, ';

                    $queryInclude2 .= '"' . $_POST[strtolower($val) . '_name'] . '", "' . ReturnSEOFriendlyUrl($_POST[strtolower($val) . '_name'], $_GET['child_of'], strtolower($val)) . '", ';
                }
                $queryString = 'INSERT INTO ' . TABLE_PREFIX . 'article_category (' . rtrim($queryInclude1, ', ') . ') VALUES (' . rtrim($queryInclude2, ', ') . ');';
                if (!$Result = mysql_query($queryString)) {
                    Message::setMessage('Kategória nebola vložená.', 2);
                    if (mysql_errno()) {
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
                    }
                } else {
                    Message::setMessage('Kategória bola úspešne vložená.', 0);
                    makeLog("Vlozenie kategórie", $_POST[sk_name]);
                    header("Location:index.php?module=article_category");
                    exit;
                }
            }
            ?>
            <div id="submenu">&nbsp;</div>
            <form method="post" action="" enctype="multipart/form-data">
                <table summary="" class="tableform" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                        <th colspan="3">Pridanie kategórie</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <?php
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        ?>
                        <tr>
                            <td>Názov kategórie <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;"><?= $val; ?></sup></td>
                            <td>&nbsp;</td>
                            <td><input name="<?= strtolower($val); ?>_name" type="text" class="textbox1" id="name"  value="<?= $_POST[strtolower($val) . '_name']; ?>" /></td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td align="left"><input type="submit" class="button" value="Pridať" /></td>
                    </tr>
                </table>
            </form>
            <?php
            break;
        case "update":
            $defaultLang = $cLanguage->getLanguageCodes();
            $defaultLang = $defaultLang[0];
            if (isset($_POST[$defaultLang . '_name'])) {
                //	zmenime  udaje   v   databaze
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude .= '`' . strtolower($val) . '_name` = "' . html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8") . '", ';
                    $queryInclude .= '`' . strtolower($val) . '_name_seo` = "' . ReturnSEOFriendlyUrl(html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8"), $_GET['child_of'], strtolower($val)) . '", ';
                }
                $queryString = 'UPDATE ' . TABLE_PREFIX . 'article_category SET
				  ' . rtrim($queryInclude, ', ') . '
				  WHERE 1 AND
				  article_category_id="' . $_GET['article_category_id'] . '";';

                if (!$Result = mysql_query($queryString)) {
                    Message::setMessage('Kategória nebol upravený.', 2);
                    if (mysql_errno()) {
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
                    }
                } else {
                    Message::setMessage('Kategória bola úspešne upravená.', 0);
                    makeLog("Uprava kategórie", $_POST[sk_name]);
                    header("Location: index.php?module=article_category");
                    exit;
                }
            }

            if (is_numeric($_GET['article_category_id'])) {
                $queryString = 'SELECT * FROM ' . TABLE_PREFIX . 'article_category WHERE 1 AND article_category_id="' . $_GET['article_category_id'] . '";';
                if (!$Result = mysql_query($queryString)) {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
                }else {
                    if (mysql_num_rows($Result) == 1)
                        $Row = mysql_fetch_assoc($Result);
                }
            }
            ?>
            <div id="submenu">&nbsp;</div>
            <form method="post" enctype="multipart/form-data" action="">
                <table summary="" class="tableform" border="0" cellpadding="2" cellspacing="0">
                    <tr>
                        <th colspan="3">Editácia kategórie</th>
                    </tr>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <?
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        ?>
                        <tr>
                            <td>Názov kategórie <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;"><?= $val; ?></sup></td>
                            <td>&nbsp;</td>
                            <td><input type="text" class="textbox1" id="name"  name="<?= strtolower($val); ?>_name" value="<?= $Row[strtolower($val) . '_name']; ?>" /></td>
                        </tr>
                        <?
                    }
                    ?>
                    <tr>
                        <td colspan="3">&nbsp;</td>
                    </tr>
                    <tr>
                        <td colspan="2">&nbsp;</td>
                        <td align="left"><input type="submit" class="button" value="Uložiť" /></td>
                    </tr>
                </table>
            </form>
            <?php
            break;
        case "delete":
            $queryString = 'DELETE FROM ' . TABLE_PREFIX . 'article_category WHERE 1 AND article_category_id="' . $_GET['article_category_id'] . '";';
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno())
                    print("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
            }else {
                header("Location:index.php?module=article_category");
                exit;
            }

            break;
        default:
            ?>
            <h1>Zoznam kategórii článkov</h1>
            <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablelist item-list">
                <tr>
                    <th>&nbsp;</th>
                    <th clospan="5">Názov kategórie</th>
                    <th>&nbsp;</th>
                </tr>
                <?php
                $query = 'SELECT * FROM ' . TABLE_PREFIX . 'article_category WHERE 1;';
                if ($result = mysql_query($query)) {
                    $_parny = false;
                    while ($row = mysql_fetch_assoc($result)) {
                        ?>
                        <tr class="<?= ((/* $Row['_checked']=='0' */1 == 2) ? 'style4' : ((!$_parny) ? 'style1' : 'style2')); ?>">
                            <td></td>
                            <td clospan="5"><a href="./index.php?module=article_category&amp;action=update&amp;article_category_id=<?= $row['article_category_id']; ?>"><?= $row['sk_name']; ?></a></td>
                            <td class="actions">
                                <a href="./index.php?module=article_category&amp;action=update&amp;article_category_id=<?= $row['article_category_id']; ?>">Editovať</a>
                                <a href="javascript:;" onclick="javascript:ConfirmBoxAc('Naozaj si želáte odstrániť tento článok?', './index.php?module=article_category&amp;action=delete&amp;article_category_id=<?= $row['article_category_id']; ?>', '');">Zmazať</a>
                            </td>
                        </tr>
                        <?
                        $_parny = !$_parny;
                    }
                }
                ?>
                <tr>
                    <th colspan="8">&nbsp;</th>
                </tr>


            </table>
        <?php
    }
    ?>
</div>