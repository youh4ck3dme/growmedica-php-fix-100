<div id="leftMenu">
    <h2>Pomocné texty</h2>
    <p>Zoznam rôznych pomocných textov zo stránky. Ich editácia je možná aj priamo na stránke prostredníctvom odkazu Editovať obsah, ktorý sa nachádza pod obsahom.</p>
</div>
<div id="moduleContent">
    <?php
    switch ($_GET['action']) {
        case "insert":
        //	Not implemented
        break;

        case "update":
            if (is_numeric($_GET['content_id'])) {
                $defaultLang = $cLanguage->getLanguageCodes();
                $defaultLang = $defaultLang[0];
                //var_dump($_POST);
                if (isset($_POST) && !empty($_POST)) {
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        $queryInclude .= "`" . strtolower($val) . "_content` = '" . addslashes($_POST[$val . '_content']) . "', ";
                    }

                    $queryString = "UPDATE " . TABLE_PREFIX . "content
                                    SET
                                        " . rtrim($queryInclude, ', ') . "
                                    WHERE 1	AND content_id='" . $_GET['content_id'] . "';";

                    $ResultA = mysql_query($queryString);
                    if ($ResultA) {
                        Message::setMessage('Statický obsah bol úspešne upravený. ', 0);
                        makeLog("Uprava statickeho obsahu", $_POST[sk_content]);
                    } else {
                        Message::setMessage('Statický obsah nebol upravený. ', 2);
                        print(mysql_error());
                    }
                    @mysql_free_result($ResultA);
                }

                $queryString = "SELECT * FROM " . TABLE_PREFIX . "content
				WHERE 1 AND content_id='" . $_GET['content_id'] . "';";

                $ResultA = mysql_query($queryString);
                if ($ResultA) {
                    if (mysql_num_rows($ResultA) == 1) {
                        $RowA = mysql_fetch_assoc($ResultA);
                    }
                } else {
                    print(mysql_error());
                }
                @mysql_free_result($ResultA);
            }
            print '
					<h1>Úprav statického obsahu</h1>
					<form name="form1" id="form1" method="post" enctype="multipart/form-data" action="">
					<table width="762" border="0" cellspacing="0" cellpadding="2" class="tableform">
						<tr>
							<th colspan="3">&nbsp;</th>
						</tr>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td style="width: 96px;">Názov</td>
							<td style="width: 26px;">&nbsp;</td>
							<td><input type="text" name="label" class="textbox1" value="' . $RowA['label'] . '" /><br />
<em>(názov je automaticky generovaný systémom a nieje možné ho zmeniť)</em></td>
						</tr>
						<!--
						<tr>
							<td>Názov<br /> </td>
							<td></td>
							<td><input type="text" name="action_name" class="textbox1" value="' . $RowA['action_name'] . '" /><br />
<em>(s diakritikou)</em></td>
						</tr>
						-->
				';
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                print '
							<tr>
								<td valign="top">Popis <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
								<td>&nbsp;</td>
								<td><textarea class="ckeditor" name="' . strtolower($val) . '_content">' . html_entity_decode($RowA[strtolower($val) . '_content'], ENT_QUOTES, "UTF-8") . '</textarea></td>';
                print '</tr>';
            }
            print '
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
						<tr>
							<td colspan="2">&nbsp;</td>
							<td align="left"><input type="submit" class="button" value="Uložiť" /></td>
						</tr>
						<tr>
							<td colspan="3">&nbsp;</td>
						</tr>
					</table>
					</form>
				';
            break;

        /*case "delete":
            if (is_numeric($_GET['content_id'])) {
                $queryString = "delete from " . TABLE_PREFIX . "content where 1 and content_id = '" . $_GET['content_id'] . "';";
                $ResultA = mysql_query($queryString);
                if ($ResultA) {
                    header("Location:index.php?module=static-content");
                    exit;
                } else {
                    die(mysql_error());
                }
                @mysql_free_result($ResultA);
            }

            break;*/
        default:
            $queryString = "select * from " . TABLE_PREFIX . "content where 1 order by content_id asc;";
            $ResultA = mysql_query($queryString);
            if ($ResultA) {
                print '
					<h1>OBSAH PORTÁLU</h1>
					<div id="submenu"><a href="index.php?module=static-content&amp;action=insert"></a></div>
					<table border="0" cellspacing="0" cellpadding="2" class="tablelist" summary="">';
                print '
						<tr>
							<th>Názov sekcie</th>
							<th>&nbsp;</th>
						</tr>
				';
                $_parny = false;
                while ($RowA = mysql_fetch_assoc($ResultA)) {
                    print '
						<tr class="' . ((!$_parny) ? "style1" : "style2") . '">
							<td><a href="index.php?module=static-content&amp;action=update&amp;content_id=' . $RowA['content_id'] . '">' . $RowA['label'] . '</a></td>
							<td style="text-align: center; width: 116px;">
								<a href="index.php?module=static-content&amp;action=update&amp;content_id=' . $RowA['content_id'] . '">Upraviť</a>'.
								/*<a href="index.php?module=static-content&amp;action=delete&amp;content_id=' . $RowA['content_id'] . '">Zmazať</a>*/  '
							</td>
						</tr>
					';
                    $_parny = !$_parny;
                }
                print '
						<tr>
							<th colspan="2">&nbsp;</th>
						</tr>
					</table>';
            } else {
                print(mysql_error());
            }
            @mysql_free_result($ResultA);
    }
    ?>
</div>