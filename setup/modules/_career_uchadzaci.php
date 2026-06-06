<div id="leftMenu">
    <p>Zoznam uchádzačov o zamestnanie, ktorí vyplnili dotazník v sekcii kariéra. Po kliknutí na detail sa zobrazí kompletný vyplnený formulár.</p>
</div>
<div id="moduleContent">
    <?php
    switch ($_GET['action']) {
        case "delete":
            $queryString = "delete from " . TABLE_PREFIX . "kariera_uchadzaci where 1 and id_uchadzac = '" . $_GET['id_uchadzac'] . "';";
            if (!$Result = mysql_query($queryString)) {
                if (mysql_errno())
                    print("MySql Error (" . mysql_errno() . "): "
                            . mysql_error() . "<br />");
            }else {
                header("Location:index.php?module=career_uchadzaci");
                exit;
            }

            break;

        case "detail":

            if (is_numeric($_GET['id_uchadzac'])) {
                $queryString = "select *, date_format(datum_ulozenia, '%d.%m.%Y') as datum from " . TABLE_PREFIX . "kariera_uchadzaci where 1 and id_uchadzac = '" . $_GET['id_uchadzac'] . "';";
                if (!$Result = mysql_query($queryString)) {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): "
                                . mysql_error() . "<br />");
                }else {
                    if (mysql_num_rows($Result) == 1)
                        $Row = mysql_fetch_assoc($Result);
                }
            }
            ?>
            <h1>Detail uchádzača o zamestnanie</h1>
            <br />
            <table>
                <tbody>
                    <tr><td><?= $Row['fullname'] ?></td></tr>
                    <tr><td><?= $Row['datum'] ?></td></tr>
                    <tr><td><?= $Row['telefon'] ?></td></tr>
                    <tr><td><?= $Row['email'] ?></td></tr>
                    <tr><td><?= nl2br($Row['text']) ?></td></tr>
                </tbody>
            </table>

            <?php
            break;
        default:
            ?>
            <h1>Zoznam uchádzačov</h1>

            <table summary="" border="0" cellpadding="2" cellspacing="0" class="tablelist">
                <tr>
                    <th>&nbsp;</th>
                    <th>Meno, priezvisko, titul</th>
                    <th>Dátum pridania</th>

                    <th>Telefón</th>
                    <th>Email</th>
                    <th>&nbsp;</th>
                </tr>
                <?php
                $queryString = "select *, date_format(datum_ulozenia, '%d.%m.%Y') as datum from " . TABLE_PREFIX . "kariera_uchadzaci order by datum_ulozenia desc;";
                //echo $queryString;
                if ($Result = mysql_query($queryString)) {
                    $_parny = false;
                    while ($Row = mysql_fetch_assoc($Result)) {
                        print '
				<tr class="' . ((/* $Row['_checked']=='0' */1 == 2) ? 'style4' : ((!$_parny) ? 'style1' : 'style2')) . '">
					<td></td>
					<td>' . $Row['fullname'] . '</td>
					<td>' . $Row['datum'] . '</td>
				    <td>' . $Row['telefon'] . '</td>
				    <td>' . $Row['email'] . '</td>
					<td style="text-align: right; padding-right: 13px;">
						<a href="./index.php?module=career_uchadzaci&amp;action=detail&amp;id_uchadzac=' . $Row['id_uchadzac'] . '">Prezrieť</a>
						<a href="javascript:;" onclick="javascript:ConfirmBoxAc(\'Naozaj si želáte odstrániť tohto uchádzača?\', \'./index.php?module=career_uchadzaci&amp;action=delete&amp;id_uchadzac=' . $Row['id_uchadzac'] . '\', \'\');">Zmazať</a>
					</td>
				</tr>
					';
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