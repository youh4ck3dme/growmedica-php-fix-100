<?
require_once('../../shared/classes/class.googleTranslate.php');
require_once('../../shared/config.inc.php');

function googleLanguageCodes($code) {
    $search = array('cz');
    $replace = array('cs');
    return str_replace($search, $replace, $code);
}
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8">
        <title>GočaTranslator v.1</title>
        <style type="text/css">
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            input,
            button {
                padding: 4px 8px;
            }
            textarea {
                width: 100%;
                height: 150px;
            }
            .wrapper {
                width: 980px;
                margin: 20px auto;
            }
            #messages-container > div {
                color: #fff;
                text-align: center;
                font-height: 18px;
                padding: 15px;
            }
            #messages-container > div.success {
                background: #00B000;
            }
            #messages-container > div.error {
                background: #db0000;
            }
            .wrapper table {
                width: 100%;
                border-collapse: collapse;
            }
            .wrapper table th,
            .wrapper table td {
                padding: 5px;
            }
            .wrapper table th {
                background: #ccc;
                text-align: left;
            }
            .wrapper table td {
            }
            .wrapper table td a {
                width: 100%;
                padding: 40px 80px;
                display: block;
                text-align: center;
                text-decoration: none;
                font-size: 24px;
            }
            .wrapper table td input {
                width: 100%;
            }
            .wrapper form button {
                width: 100%;
                margin: 0 0 20px;
                padding: 40px 80px;
                font-size: 24px;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <?
            switch ($_GET['action']) {
                case 'translation':
                    if ($_POST) {
                        $error = false;
                        foreach ($_POST as $key => $value) {
                            $id = str_replace('item_', '', $key);
                            foreach ($value as $code => $translation) {
                                $query .= 'desc_' . $code . '="' . addslashes($translation) . '", ';
                            }
                            $query = 'UPDATE ' . TABLE_PREFIX . 'translation SET ' . rtrim($query, ', ') . ' WHERE translation_id="' . $id . '";';
                            $result = mysql_query($query);
                            if (!$result) {
                                $error = true;
                            }
                            unset($query);
                        }
                        if (!$result) {
                            Message::setMessage('Preklady neboli uložené. Chyba nastala pri update "' . $query . '".', 2);
                            header('Location: ./mass-translation.php');
                            exit;
                        } else {
                            Message::setMessage('Preklady boli úspešne uložené.', 0);
                            header('Location: ./mass-translation.php');
                            exit;
                        }
                    }

                    $tr = new GoogleTranslate();
                    $tr->setLangFrom('sk');

                    $query = 'SELECT * FROM ' . TABLE_PREFIX . 'translation;';
                    $result = mysql_query($query);
                    ?>
                    <form method="post">
                        <table>
                            <?
                            if ($result) {
                                $i = '0';
                                while ($row = mysql_fetch_object($result)) {
                                    $i++;
                                    ?>
                                    <tr>
                                        <th colspan="2"><?= $row->label_name; ?>: <?= $row->desc_sk; ?></th>
                                    </tr>
                                    <?
                                    foreach ($cLanguage->getLanguageCodes() as $code) {
                                        if ($code != 'sk') { //empty($row->{'desc_' . $code}) AND
                                            ?>
                                            <tr>
                                                <td><?= $code; ?></td>
                                                <td><input type="text" name="item_<?= $row->translation_id; ?>[<?= $code; ?>]" value="<?= $tr->setLangTo(googleLanguageCodes($code))->translate($row->desc_sk); ?>" /></td>
                                            </tr>
                                            <?
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <?
                                }
                            } else {
                                ?>
                                <tr>
                                    <th colspan="2">Chyba!!! Treba opraviť!</th>
                                </tr>
                                <?
                            }
                            ?>
                        </table>
                        <button type="submit">Uložiť preklady</button>
                        <table>
                            <tr>
                                <td colspan="2"><a href="./mass-translation.php">Domov</a></th>
                            </tr>
                        </table>
                    </form>
                    <?
                    break;
                case 'static-content':
                    if ($_POST) {
                        $error = false;
                        foreach ($_POST as $key => $value) {
                            $id = str_replace('item_', '', $key);
                            foreach ($value as $code => $translation) {
                                $query .= $code . '_content="' . addslashes($translation) . '", ';
                            }
                            $query = 'UPDATE ' . TABLE_PREFIX . 'content SET ' . rtrim($query, ', ') . ' WHERE content_id="' . $id . '";';
                            $result = mysql_query($query);
                            if (!$result) {
                                $error = true;
                            }
                            unset($query);
                        }
                        if ($error) {
                            Message::setMessage('Statický obsah nebol uložený. Chyba nastala pri update "' . $query . '".', 2);
                            header('Location: ./mass-translation.php');
                            exit;
                        } else {
                            Message::setMessage('Statický obsah bol úspešne uložený.', 0);
                            header('Location: ./mass-translation.php');
                            exit;
                        }
                    }

                    $tr = new GoogleTranslate();
                    $tr->setLangFrom('sk');

                    $query = 'SELECT * FROM ' . TABLE_PREFIX . 'content;';
                    $result = mysql_query($query);
                    ?>
                    <form method="post">
                        <table>
                            <?
                            if ($result) {
                                $i = '0';
                                while ($row = mysql_fetch_object($result)) {
                                    $i++;
                                    ?>
                                    <tr>
                                        <th colspan="2"><?= $row->label; ?>: <?= $row->sk_content; ?></th>
                                    </tr>
                                    <?
                                    foreach ($cLanguage->getLanguageCodes() as $code) {
                                        if ($code != 'sk') { //empty($row->{'desc_' . $code}) AND
                                            ?>
                                            <tr>
                                                <td><?= $code; ?></td>
                                                <td><textarea name="item_<?= $row->content_id; ?>[<?= $code; ?>]"><?= $tr->setLangTo(googleLanguageCodes($code))->translate($row->sk_content); ?></textarea></td>
                                            </tr>
                                            <?
                                        }
                                    }
                                    ?>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                    <?
                                }
                            } else {
                                ?>
                                <tr>
                                    <th colspan="2">Chyba!!! Treba opraviť!</th>
                                </tr>
                                <?
                            }
                            ?>
                        </table>
                        <button type="submit">Uložiť statický obsah</button>
                        <table>
                            <tr>
                                <td colspan="2"><a href="./mass-translation.php">Domov</a></th>
                            </tr>
                        </table>
                    </form>
                    <?
                    break;
                default:
                    Message::getMessage();
                    ?>
                    <table>
                        <tr>
                            <td><a href="?action=translation">Preklady</a></th>
                        </tr>
                        <tr>
                            <td><a href="?action=static-content">Pomocné texty</a></th>
                        </tr>
                    </table>
                <?
            }
            ?>
        </div>
    </body>
</html>



