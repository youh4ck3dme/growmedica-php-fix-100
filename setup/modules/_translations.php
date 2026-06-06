<div id="leftMenu">
    <h2>Preklady</h2>
    <p>V tejto sekcii je možné meniť preklady rôznych pomocných textov - napr. z formulárov, odkazov a pod. Taktiež môžete meniť znenie textov v slovenskom znení.</p>

</div>
<div id="moduleContent">
    <?php
    switch ($_GET['action']) {
        case "update":
            if (is_numeric($_GET['translation_id'])) {
                $defaultLang = $cLanguage->getLanguageCodes();
                $defaultLang=array_values($defaultLang);
                $defaultLang = $defaultLang[0];

                if (isset($_POST['desc_' . $defaultLang])) {
                    $queryInclude = array();
                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        $queryInclude[] = "`desc_" . strtolower($val) . "` = '" . $_POST['desc_' . $val] . "'";
                    }

                    $queryString = "
						update
							`" . TABLE_PREFIX . "translation`
						set
							" . implode(", ", $queryInclude) . "
						where
							1
						and
							`translation_id` = '" . $_GET['translation_id'] . "';";

                    $ResultA = mysql_query($queryString);
                    if ($ResultA) {
                        Message::setMessage('Preklad bol úspešne upravený.', 0);
                    } else {
                        Message::setMessage('Preklad nebol upravený.', 2);
                        print(mysql_error());
                    }
                    @mysql_free_result($ResultA);
                }

                $queryString = "
					select
						`t`.*
					from
						`" . TABLE_PREFIX . "translation` as `t`
					where
						1
					and
						`t`.`translation_id` = '" . $_GET['translation_id'] . "';";

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
					<h1>Úprava prekladu</h1>
					<form name="form1" id="form1" method="post" enctype="multipart/form-data" action="">
					<table width="762" border="0" cellspacing="0" cellpadding="2" class="tableform">
						<tr>
							<th colspan="3">&nbsp;</th>
						</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
						<tr>
							<td style="width: 96px;">Originálny text</td>
							<td style="width: 26px;">&nbsp;</td>
							<td><input type="text" name="label_name" class="textbox1" value="' . $RowA['label_name'] . '" /><br />
<em>(názov je automaticky generovaný systémom a nieje možné ho zmeniť)</em></td>
						</tr>
				';
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                print '
							<tr>
								<td valign="top">Preklad <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
								<td>&nbsp;</td>
								<td><textarea class="textbox1" style="border: solid 1px #ccc;" name="desc_' . strtolower($val) . '" id="desc_' . strtolower($val) . '">' . $RowA['desc_' . strtolower($val)] . '</textarea>

								</td>
						</tr>
				';
            }
            print '
						<tr>
							<td colspan="2">&nbsp;</td>
							<td align="left"><input type="submit" class="button" value="Uložiť" /></td>
						</tr>
					</table>
					</form>
				';
            break;

        case "delete":

        default:
            $queryString = "select * from " . TABLE_PREFIX . "translation where 1 order by label_name asc;";
            $ResultA = mysql_query($queryString);
            if ($ResultA) {
                print '
					<h1>PREKLADOVÝ MODUL</h1>
					<div id="submenu"><a href="index.php?module=static-content&amp;action=insert"></a></div>
					<table border="0" cellspacing="0" cellpadding="2" class="tablelist" summary="">';
                print '
						<tr>
							<th>Originálny text</th>
							<th>&nbsp;</th>
						</tr>
				';
                $_parny = false;
                while ($RowA = mysql_fetch_assoc($ResultA)) {
                    print '
						<tr class="' . ((!$_parny) ? "style1" : "style2") . '">
							<td><a href="index.php?module=translations&amp;action=update&amp;translation_id=' . $RowA['translation_id'] . '">' . truncate($RowA['label_name'], '100', ' ') . '</a></td>
							<td style="text-align: center; width: 48px;">
								<a href="index.php?module=translations&amp;action=update&amp;translation_id=' . $RowA['translation_id'] . '">Preložiť</a>
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