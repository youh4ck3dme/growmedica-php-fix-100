<script type="text/javascript">
    function enableContent(obj) {
        var e = document.getElementById('content-editable-zone');
        if (e) {
            e.style.display = (obj == 1) ? 'none' : ((navigator.userAgent.indexOf('MSIE') > -1) ? 'block' : 'table-row');
        }
    }
</script>
<div id="leftMenu">V tejto sekcii sa spravuje niektoré nastavenia stránky.
    <div id="submenu"></div>
</div>
<div id="moduleContent">
    <?php
    switch ($_GET['action']) {
        default:
            if ($_POST) {

                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude .= 'general_meta_title_' . strtolower($val) . '="' . $_POST['general_meta_title_' . $val] . '", general_meta_keywords_' . strtolower($val) . '="' . $_POST['general_meta_keywords_' . $val] . '", general_meta_description_' . strtolower($val) . '="' . $_POST['general_meta_description_' . $val] . '", ';
                }

                $query= 'UPDATE ' . TABLE_PREFIX . 'settings SET ' . $queryInclude . 'google_analytics="' . $_POST['google_analytics'] . '" WHERE 1;';

                $result = mysql_query($query);
                if (!$result) {
                    print(mysql_error());
                }
            }

            //	zistime si udaje do formulara
            $query = "SELECT * FROM " . TABLE_PREFIX . "settings WHERE 1;";
            $result = mysql_query($query);
            if ($result) {
                $meta = mysql_fetch_assoc($result);
            } else {
                print(mysql_error());
            }

            print '
                <h1>Nastavenia</h1>
				<form method="post" action="">
				<table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">
					<tr>
						<th colspan="3">&nbsp;</th>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
                                        ';
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                print '
                                        <tr>
						<td>META title <sup>(' . $val . ')</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="general_meta_title_' . $val . '" value="' . $meta['general_meta_title_' . $val] . '" /></td>
					</tr>
                                        <tr>
						<td>META keywords <sup>(' . $val . ')</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="general_meta_keywords_' . $val . '" value="' . $meta['general_meta_keywords_' . $val] . '" /></td>
					</tr>
                                        <tr>
						<td>META description <sup>(' . $val . ')</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="general_meta_description_' . $val . '" value="' . $meta['general_meta_description_' . $val] . '" /></td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
                                        ';
            }
            print '
                                        <tr>
						<td>Kód Google Analytics</td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="google_analytics" value="' . $meta['google_analytics'] . '" /></td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="left"><input class="button" type="submit" value="Upraviť" /></td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				</table>
				</form>
			';
    }
    ?>
</div>