<div id="leftMenu">
    <h2>Záhlavia</h2>
    <p>Pridávanie, odoberanie a správa záhlaví. Pozor, záhlavia k jednotlivým podstránkam sa nastavujú v sekcii <a href="./index.php?module=menu">ŠTRUKTÚRA</a>.</p>
    <div id="submenu">
        <a href="./index.php?module=slideshow&action=insert" class="addNew">Pridať nový<br />
            obrázok záhlavia</a>
    </div>
</div>
<div id="moduleContent">
    <?php
    switch ($_GET['action']) {
        case "insert":
            if (isset($_POST['sk_name']) and $_FILES['src']["name"] <> '') {
                // nahravanie obrázka
                try {
                    $img = new abeautifulsite\SimpleImage();

                    $file_name = explode('.', $_FILES['src']['name']);
                    $ext = pathinfo($_FILES['src']["name"], PATHINFO_EXTENSION);
                    $slide_name = time() . '-' . String::SEOFriendlyText($file_name[0]) . '.' . $ext;
                    if (!$img->load($_FILES['src']["tmp_name"])->save('../photos/slideshow/' . $slide_name)) {
                        $_SESSION['_message'] = 'Slide nebol nahraný! Prosím skúste znova.';
                        header('Location: ' . $_SERVER['HTTP_REFERER']);
                        exit;
                    }
                } catch (Exception $e) {
                    $_SESSION['_message'] = '<strong class="red">' . $e->getMessage() . '</strong>';
                    header('Location: ' . $_SERVER['HTTP_REFERER']);
                    exit;
                }
                // príprava query
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude1 .= "`" . strtolower($val) . "_name`, `" . strtolower($val) . "_popis`, ";
                    $queryInclude2 .= "'" . html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8") . "', '" . html_entity_decode($_POST[strtolower($val) . '_popis'], ENT_QUOTES, "UTF-8") . "', ";
                }

                // vkladanie slidu do DB
                $queryString = "INSERT INTO " . TABLE_PREFIX . "menu_slideshow (" . $queryInclude1 . "`src`, `link`, `extlink`) VALUES (" . $queryInclude2 . "," . $slide_name . ", " . $_POST['link_to'] . ", " . $_POST['extlink'] . ");";
                $ResultR = mysql_query($queryString);
                if ($ResultR) {
                    $menu_slideshow_id = mysql_insert_id();
                    Message::setMessage('Slide bol úspešne pridaný. ', 0);
                } else {
                    if (mysql_errno())
                        echo "MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />";
                    Message::setMessage('Slide nebol pridaný. ', 2);
                }
            }

            //	zistime si udaje do formulara
            $queryString = "SELECT * FROM " . TABLE_PREFIX . "menu_slideshow WHERE 1 AND menu_slideshow_id = '" . $menu_slideshow_id . "';";
            $ResultR = mysql_query($queryString);
            if ($ResultR) {
                if (mysql_num_rows($ResultR) == 1) {
                    $_POST = mysql_fetch_assoc($ResultR);
                }
            } else {
                echo mysql_error();
            }

            if (isset($_SESSION['_message'])) {
                echo message($_SESSION['_message']);
                unset($_SESSION['_message']);
            }

            echo '<h1>Pridanie slidu</h1>
                <form method="post" action="" enctype="multipart/form-data">
				<table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">
					<tr>
						<th colspan="3">&nbsp;</th>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				';
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                echo '
					<tr>
						<td>Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup> *</td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="' . strtolower($val) . '_name" value="' . $_POST[strtolower($val) . '_name'] . '" /></td>
					</tr>
					<tr>
						<td>Popis <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><textarea class="textbox1" name="' . strtolower($val) . '_popis">' . $_POST[strtolower($val) . '_popis'] . '</textarea></td>
					</tr>
				';
            }
            echo '
					<tr>
						<td>Presmerovať do</td>
						<td>&nbsp;</td>
						<td><select name="link_to">';
            echo Menu::print_tree_combobox(NULL, $_POST['link']);

            echo '</select></td>
					</tr>';
            echo '<tr>
						<td>Linka <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="extlink" value="' . $_POST['extlink'] . '" /></td>
					</tr>
					<tr>
						<td>Obrázok * </td>
						<td>&nbsp;</td>
						<td><input name="src" type="file" /></td>
					</tr>
					<tr>
						<td colspan="3">* potrebné údaje</td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="left"><input class="button" type="submit" value="Pridať" /></td>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
				</table>
				</form>
			';
            break;

        case "update":
            if (isset($_POST['sk_name'])) {

                if ($_FILES['src']["name"] <> '') {
                    // nahravanie obrázka
                    try {
                        $img = new abeautifulsite\SimpleImage();

                        $file_name = explode('.', $_FILES['src']['name']);
                        $ext = pathinfo($_FILES['src']["name"], PATHINFO_EXTENSION);
                        $slide_name = time() . '-' . String::SEOFriendlyText($file_name[0]) . '.' . $ext;
                        if (!$img->load($_FILES['src']["tmp_name"])->save('../photos/slideshow/' . $slide_name)) {
                            $_SESSION['_message'] = 'Slide nebol nahraný! Prosím skúste znova.';
                            header('Location: ' . $_SERVER['HTTP_REFERER']);
                            exit;
                        }
                        /*                        $img = new SimpleImage();
                          $path = $_FILES['src']["name"];
                          $ext = pathinfo($path, PATHINFO_EXTENSION);
                          $slide_name = time() . randomString(5) . '.' . $ext;
                          if (!$img->load($_FILES['src']["tmp_name"])->adaptive_resize($idm[0], $idm[1])->save('../photos/slideshow/' . $slide_name)) {
                          $_SESSION['_message'] = 'Slide nebol nahraný! Prosím skúste znova.';
                          header('Location: ' . $_SERVER['HTTP_REFERER']);
                          exit;
                          } */
                    } catch (Exception $e) {
                        $_SESSION['_message'] = '<strong class="red">' . $e->getMessage() . '</strong>';
                        header('Location: ' . $_SERVER['HTTP_REFERER']);
                        exit;
                    }
                }

                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude .= "`" . strtolower($val) . "_name` = '" . html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8") . "',";
                    $queryInclude .= "`" . strtolower($val) . "_popis` = '" . html_entity_decode($_POST[strtolower($val) . '_popis'], ENT_QUOTES, "UTF-8") . "',";
                }

                $queryInclude = substr($queryInclude, 0, -1);

                $queryString = "UPDATE `" . TABLE_PREFIX . "menu_slideshow` SET " . $queryInclude . (isset($slide_name) ? ', src="' . $slide_name . '"' : '') . ", link = " . $_POST['link_to'] . " WHERE 1 AND `menu_slideshow_id` = '" . $_GET['menu_slideshow_id'] . "';";

                $ResultS = mysql_query($queryString);
                if ($ResultS) {
                    Message::setMessage('Slide bol úspešne upravený. ', 0);
                } else {
                    Message::setMessage('Obrázok nebol upravený. ', 2);
                    echo mysql_error();
                }
            }

            //	zistime si udaje do formulara
            $queryString = "SELECT * FROM " . TABLE_PREFIX . "menu_slideshow WHERE 1 AND menu_slideshow_id='" . $_GET['menu_slideshow_id'] . "';";
            $ResultR = mysql_query($queryString);
            if ($ResultR) {
                if (mysql_num_rows($ResultR) == 1) {
                    $_POST = mysql_fetch_assoc($ResultR);
                }
            } else {
                echo mysql_error();
            }

            if (isset($_SESSION['_message'])) {
                echo message($_SESSION['_message']);
                unset($_SESSION['_message']);
            }

            echo '<h1>Úprava slidu</h1>
                <form method="post" action="" enctype="multipart/form-data">
                    <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">
                        <tr>
                            <th colspan="3">&nbsp;</th>
                        </tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>';
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                echo '
                        <tr>
                            <td>Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . ' </sup> *</td>
                            <td>&nbsp;</td>
                            <td><input class="textbox1" type="text" name="' . strtolower($val) . '_name" value="' . $_POST[strtolower($val) . '_name'] . '" /></td>
                        </tr>
                        <tr>
                            <td>Popis <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . ' </sup></td>
                            <td>&nbsp;</td>
                            <td><textarea class="textbox1" name="' . strtolower($val) . '_popis">' . $_POST[strtolower($val) . '_popis'] . ' </textarea></td>
                        </tr>
                ';
            }
            echo '
                        <tr>
                            <td>Presmerovať do</td>
                            <td>&nbsp;</td>
                            <td>
                                <select name="link_to">';
            echo Menu::print_tree_combobox(NULL, $_POST[' link']);

            echo '              </select>
                            </td>
                        </tr>';
            echo '
                        <tr>
                            <td></td>
                            <td>&nbsp;</td>
                            <td>';
            if ($_POST['src'] != NULL) {
                echo '<a href="index.php?module=slideshow&action=remove-picture&menu_slideshow_id=' . $_POST['menu_slideshow_id'] . '&src=' . $_POST['src'] . '">Odstrániť obrázok</a>';
            } else {
                echo '<input type="file" name="src" />';
            }
            echo '          </td>
                        </tr>
                        <tr>
                            <td colspan="3">* potrebné údaje</td>
                        </tr>
                        <tr>
                            <td colspan="2"></td>
                            <td align="left"><input class="button" type="submit" value="Upraviť" /></td>
                        </tr>
			<tr>
                            <td colspan="3">&nbsp;</td>
			</tr>
                    </table>
                </form>';
            break;

        case "remove-picture":
            echo $queryString = "UPDATE " . TABLE_PREFIX . "menu_slideshow SET src=NULL WHERE menu_slideshow_id='" . $_GET['menu_slideshow_id'] . "'";
            $ResultA = mysql_query($queryString);
            if ($ResultA) {
                unlink('../photos/slideshow/' . $_GET['src']);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                echo mysql_error();
            }
        case "delete":
            $queryString = "DELETE FROM " . TABLE_PREFIX . "menu_slideshow WHERE menu_slideshow_id='" . $_GET['menu_slideshow_id'] . "' LIMIT 1;";
            $ResultA = mysql_query($queryString);
            if ($ResultA) {
                unlink('../photos/slideshow/' . $_GET['src']);
                header('Location: ' . $_SERVER['HTTP_REFERER']);
                exit;
            } else {
                echo mysql_error();
            }
        default:
            $queryString = "select * from " . TABLE_PREFIX . "menu_slideshow where 1 and menu_slideshow_id!='31' and menu_slideshow_id!='32' order by sk_name;";
            $ResultA = mysql_query($queryString);
            if ($ResultA) {
                if (isset($_SESSION['_message'])) {
                    echo message($_SESSION['_message']);
                    unset($_SESSION['_message']);
                }
                echo '
                    <h1>Slideshow</h1>
                        <table border="0" cellspacing="0" cellpadding="2" class="tablelist" summary="">';
                echo '
                            <tr>
                                <th>Názov</th>
                                <th>Obrázok</th>
                                <th>&nbsp;</th>
                            </tr>
                ';
                $_parny = false;
                while ($RowA = mysql_fetch_assoc($ResultA)) {
                    echo '  <tr class="' . ((!$_parny) ? "style1" : "style2") . '">
                                <td' . ($RowA['src'] == NULL ? ' class="red strong"' : '') . '>' . $RowA['sk_name'] . '</td>
                                <td><a class="fancybox-image" href="' . ROOTDIR . '/photos/slideshow/' . $RowA['src'] . '">' . ($RowA['src'] != NULL ? $RowA['src'] : '<a class="red strong" href="index.php?module=slideshow&amp;action=update&amp;menu_slideshow_id=' . $RowA['menu_slideshow_id'] . '">Nahrať nový slide</a>') . '</a></td>
                                <td>
                                    <a href="index.php?module=slideshow&amp;action=update&amp;menu_slideshow_id=' . $RowA['menu_slideshow_id'] . '">Upraviť</a>
                                    <a href="index.php?module=slideshow&amp;action=delete&amp;menu_slideshow_id=' . $RowA['menu_slideshow_id'] . '&amp;src=' . $RowA['src'] . '">Odstrániť</a>
                                </td>
                            </tr>';
                    $_parny = !$_parny;
                }
                echo '
                            <tr>
                                <th colspan="3">&nbsp;</th>
                        </tr>
                    </table>';
            } else {
                echo mysql_error();
            }
            @mysql_free_result($ResultA);
    }
    ?>
</div>
