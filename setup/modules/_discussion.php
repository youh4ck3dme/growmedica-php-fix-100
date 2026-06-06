<?php

//	skontroluje ci existuje tabulka diskusia
//	a ak nie, tak ju vytvori
Model_discussion::checkIfTableExist();

switch ($_REQUEST['action']) {
    case "delete":
        if (is_numeric($_REQUEST['prispevok_id'])) {
            $queryString = "delete from " . TABLE_PREFIX . "diskusia where prispevok_id = '" . $_REQUEST['prispevok_id'] . "';";
            $ResultR = mysql_query($queryString);
            if ($ResultR) {

                header("Location:index.php?module=discussion");
                exit;
            } else {
                print(mysql_error());
            }
        }
        break;
    default:
        echo '<div id="leftMenu">Zoznam príspevkov v diskusiách pod článkami</div>';
        echo '<div id="moduleContent">';

        //	zobrazime zoznam uzivatelov
        print '
				<h1>Zoznam príspevkov</h1>
				<table summary="" border="0" cellspacing="0" cellpadding="2" class="tablelist">
					<tr>
						<th>ID</th>
						<th>Článok</th>
						<th>Diskutér</th>
						<th>Text</th>
						<th>IP</th>
						<th>Dátum</th>
						<th></th>
					</tr>
			';

        $queryString = 'SELECT d.*, DATE_FORMAT(d.datetime_prispevku, "%e.%m. %Y, %k:%i:%s") AS datetime_prispevku, a.sk_name FROM ' . TABLE_PREFIX . 'diskusia AS d JOIN ' . TABLE_PREFIX . 'article AS a USING (article_id) WHERE 1 ORDER BY datetime_prispevku DESC';
        $ResultQ = mysql_query($queryString);
        if ($ResultQ) {
            $_parny = false;
            while ($RowEsQ = mysql_fetch_assoc($ResultQ)) {
                print '
					<tr class="' . ((!$_parny) ? 'style1' : 'style2') . '">
						<td>' . $RowEsQ['prispevok_id'] . '</td>
						<td>' . $RowEsQ['sk_name'] . '</td>
						<td>' . $RowEsQ['diskuter_meno'] . ' (' . $RowEsQ['diskuter_mail'] . ')</td>
						<td>' . $RowEsQ['text_prispevku'] . '</td>
						<td>' . $RowEsQ['diskuter_ip'] . '</td>
						<td>' . $RowEsQ['datetime_prispevku'] . '</td>
						<td align="right">
							<a href="javascript:;" onclick="javascript:ConfirmBoxAc(\'Naozaj si želáte odstrániť tento príspevok?\', \'./index.php?module=' . $_REQUEST['module'] . '&amp;action=delete&amp;prispevok_id=' . $RowEsQ['prispevok_id'] . '\', \'\');">Zmazať</a>
						</td>
					</tr>
					';
                $_parny = !$_parny;
            }
        } else {
            print(mysql_error());
        }
        mysql_free_result($ResultQ);
        print '
					<tr>
						<th colspan="7">&nbsp;</th>
					</tr>
				</table>
			';

        echo '</div>';
}
?>