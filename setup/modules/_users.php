<div id="leftMenu">
    <h2>Užívatelia</h2>
    <p>Pridávanie, odoberanie a správa užívateľských účtov. </p>
    <p>Možnosť zmeny hesla.</p>
    <div id="submenu"><a href="./index.php?module=users&amp;action=insert" class="addNew">Pridať <br />
            užívateľa</a><a href="./index.php?module=users" class="edit">Zoznam <br />
            užívateľov</a></div>
</div>
<div id="moduleContent">
    <?php
    switch ($_GET['action']) {
        case "insert":
            if (isset($_POST['fullname'])) {
                $queryString = "insert into " . TABLE_PREFIX . "user (`admin`,`editor`, `fullname`, `pwd`, `mail`, `comment`, `active`, `newsletter`) values ('" . ((isset($_POST['admin'])) ? '1' : '0') . "', '" . ((isset($_POST['editor'])) ? '1' : '0') . "', '" . $_POST['fullname'] . "', md5('" . $_POST['pwd'] . "'), '" . $_POST['mail'] . "', '" . $_POST['comment'] . "', '" . ((isset($_POST['active'])) ? 1 : 0) . "', '" . ((isset($_POST['newsletter'])) ? 1 : 0) . "');";
                $ResultR = mysql_query($queryString);
                if ($ResultR) {
                    $insertIdUser = mysql_insert_id();

                    Message::setMessage('Uživateľ bol úspešne pridaný. ', 0);
                    header("Location:index.php?module=users");
                    exit;
                } else {
                    Message::setMessage('Užívateľ nebol pridaný. ', 0);
                    print(mysql_error());
                }
            }

            print '
				<h1>Pridanie nového užívateľa</h1>
				<form method="post" action="" enctype="multipart/form-data">
				<table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">
					<tr>
						<th colspan="3">&nbsp;</th>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td>Celé meno</td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="fullname" value="' . $_POST['fullname'] . '" /></td>
					</tr>
					<tr>
						<td>Email</td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="email" name="mail" value="' . $_POST['mail'] . '" /></td>
					</tr>
					<tr>
						<td>Heslo</td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="password" name="pwd" value="' . $_POST['pwd'] . '" /></td>
					</tr>
					<tr>
						<td>Aktívny užívateľ</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="active" value="1"' . (($_POST['active']) ? ' checked="checked"' : '') . ' /></td>
					</tr>
					<tr>
						<td>Užívateľ je administrátorom</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="admin" value="1"' . (($_POST['admin']) ? ' checked="checked"' : '') . ' /></td>
					</tr>
					<tr>
						<td>Užívateľ je editor</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="editor" value="1"' . (($_POST['editor']) ? ' checked="checked"' : '') . ' /></td>
					</tr>
					<tr>
						<td>Newsletter</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="newsletter" value="1"' . (($_POST['newsletter']) ? ' checked="checked"' : '') . ' /></td>
					</tr>
					<tr>
						<td>Poznámka</td>
						<td>&nbsp;</td>
						<td><textarea class="textbox1" name="comment">' . $_POST['comment'] . '</textarea></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="right"><input type="submit" value="Pridať užívateľa" /></td>
					</tr>
				</table>
				</form>
			';
            break;
        case "update":
            if (isset($_POST['mail'])) {
                //	zmenime  udaje   v   databaze
                echo $queryString = "update " . TABLE_PREFIX . "user set `admin` = '" . ((isset($_POST['admin'])) ? '1' : '0') . "',`editor` = '" . ((isset($_POST['editor'])) ? '1' : '0') . "', `active`='" . ((isset($_POST['active'])) ? 1 : 0) . "'" . (!empty($_POST['pwd']) ? ", `pwd`=md5('" . $_POST['pwd'] . "')" : '') . ", `mail`='" . $_POST['mail'] . "', `comment`='" . $_POST['comment'] . "', `newsletter`='" . ((isset($_POST['newsletter'])) ? '1' : '0') . "' where 1 and `user_id` = '" . $_GET['user_id'] . "';";
                $ResultS = mysql_query($queryString);
                if ($ResultS) {

                    Message::setMessage('Uživateľ bol úspešne upravený. ', 0);
                    header("Location:index.php?module=users");
                    exit;
                } else {
                    Message::setMessage('Uživateľ nebol upravený. ', 0);
                    print(mysql_error());
                }
            }

            //	zistime si udaje do formulara
            $queryString = "select * from " . TABLE_PREFIX . "user where 1 and user_id = '" . $_GET['user_id'] . "';";
            $ResultR = mysql_query($queryString);
            if ($ResultR) {
                if (mysql_num_rows($ResultR) == 1) {
                    $_POST = mysql_fetch_assoc($ResultR);
                }
            } else {
                print(mysql_error());
            }

            print '
				<h1>Upravenie nastavení užívateľa</h1>
				<form method="post" action="" enctype="multipart/form-data">
				<table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">';
            /* 	<tr>
              <td style="width:150px">Celé meno</td>
              <td>&nbsp;</td>
              <td><input class="textbox1" type="text" name="fullname" value="' . $_POST['fullname'] . '" /></td>
              </tr> */
            print '
					<tr>
						<th colspan="3">&nbsp;</th>
					</tr>
					<tr>
						<td colspan="3">&nbsp;</td>
					</tr>
					<tr>
						<td>Email</td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="email" name="mail" value="' . $_POST['mail'] . '" /></td>
					</tr>
					<tr>
						<td>Heslo</td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="password" name="pwd" /></td>
					</tr>
					<tr>
						<td>Aktívny užívateľ</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="active" value="1"' . (($_POST['active'] == 1) ? ' checked="checked"' : '') . ' /></td>
					</tr>
					<tr>
						<td>Užívateľ je administrátorom</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="admin" value="1"' . (($_POST['admin'] == 1) ? ' checked="checked"' : '') . ' /></td>
					</tr>
					<tr>
						<td>Užívateľ je editor</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="editor" value="1"' . (($_POST['editor'] == 1) ? ' checked="checked"' : '') . ' /></td>
					</tr>
					<tr>
						<td>Newsletter</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="newsletter" value="1"' . (($_POST['newsletter'] == 1) ? ' checked="checked"' : '') . ' /></td>
					</tr>
					<tr>
						<td>Poznámka</td>
						<td>&nbsp;</td>
						<td><textarea class="textbox1" name="comment">' . $_POST['comment'] . '</textarea></td>
					</tr>
					<tr>	<td>Meno a priezvisko</td>	<td>&nbsp;</td>	<td>' . $_POST['meno'] . ' ' . $_POST['priezvisko'] . '</td>	</tr>
					<tr>	<td>Telefonne číslo</td>	<td>&nbsp;</td>	<td>' . $_POST['pevna_linka'] . '</td>	</tr>
					<tr>	<td>Ulica</td>	<td>&nbsp;</td>	<td>' . $_POST['ulica_cislo'] . '</td>	</tr>
					<tr>	<td>PSČ</td>	<td>&nbsp;</td>	<td>' . $_POST['psc'] . '</td>	</tr>
					<tr>	<td>Mesto</td>	<td>&nbsp;</td>	<td>' . $_POST['mesto'] . '</td>	</tr>
					<tr>	<td>Krajina</td>	<td>&nbsp;</td>	<td>' . $_POST['krajina'] . '</td>	</tr>
					<tr>
						<td colspan="2"></td>
						<td align="right"><input type="submit" value="Upraviť užívateľa" /></td>
					</tr>
				</table>
				</form>
			';
            break;

        case "delete":
            if (is_numeric($_GET['user_id'])) {
                $queryString = "delete from " . TABLE_PREFIX . "user where 1 and user_id = '" . $_GET['user_id'] . "';";
                $ResultR = mysql_query($queryString);
                if ($ResultR) {

                    Message::setMessage('Užívateľ bol úspešne odstránený. ', 0);
                    header("Location:index.php?module=users");
                    exit;
                } else {
                    Message::setMessage('Uživateľ nebol odstránený. ', 0);
                    print(mysql_error());
                }
            }
            break;

        default:
            //	zobrazime zoznam uzivatelov
            print '
				<h1>Zoznam užívateľov</h1>
				<table summary="" border="0" cellspacing="0" cellpadding="2" class="tablelist">
					<tr>
						<th>ID</th>
						<th>Prihlasovacie meno</th>
						<th>Admin</th>
						<th>Editor</th>
						<th>Newsletter</th>
						<th>Stav konta</th>
						<th></th>
					</tr>
			';

            $queryString = "select u.* from " . TABLE_PREFIX . "user as u where 1;";
            $ResultQ = mysql_query($queryString);
            if ($ResultQ) {
                $_parny = false;
                while ($RowEsQ = mysql_fetch_assoc($ResultQ)) {
                    print '
					<tr class="' . ((!$_parny) ? 'style1' : 'style2') . '">
						<td>' . $RowEsQ['user_id'] . '</td>
						<td><a href="./index.php?module=' . $_GET['module'] . '&amp;action=update&amp;user_id=' . $RowEsQ['user_id'] . '">' . (!empty($RowEsQ['mail']) ? $RowEsQ['mail'] : 'bez registracie: ' . $RowEsQ['username']) . '</a></td>
						<td>' . (($RowEsQ['admin'] == 1) ? "áno" : "nie") . '</td>
						<td>' . (($RowEsQ['editor'] == 1) ? "áno" : "nie") . '</td>
						<td>' . (($RowEsQ['newsletter'] == 1) ? "aktívny" : "neaktívny") . '</td>
						<td>' . (($RowEsQ['active'] == 1) ? "aktívny" : "neaktívny") . '</td>
						<td align="right">
							<a href="./index.php?module=' . $_GET['module'] . '&amp;action=update&amp;user_id=' . $RowEsQ['user_id'] . '">Editovať</a>
							<a href="javascript:;" onclick="javascript:ConfirmBoxAc(\'Naozaj si želáte odstrániť tohto užívateľa?\', \'./index.php?module=' . $_GET['module'] . '&amp;action=delete&amp;user_id=' . $RowEsQ['user_id'] . '\', \'\');">Zmazať</a>
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
    }
    ?></div>