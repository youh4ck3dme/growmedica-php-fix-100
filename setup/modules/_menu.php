<script type="text/javascript">
    function enableContent(obj) {
        var e = document.getElementById('content-editable-zone');
        if (e) {
            e.style.display = (obj == 1) ? 'none' : ((navigator.userAgent.indexOf('MSIE') > -1) ? 'block' : 'table-row');
        }
    }
</script>
<div id="leftMenu">V tejto sekcii sa spravuje štruktúra stránky a jednotlivé podstránky.
    <div id="submenu"><a href="./index.php?module=menu&action=slideshow_active" class="slideshow">Aktivovať slideshow všade</a><br />
        Automaticky aktivuje slideshow pre každú podstránku bez nutnosti ručného povolenia<a href="./index.php?module=menu&action=slideshow_assign" class="slideshow">Priradiť všetky slideshow</a>
        Automaticky priradí každej podstránke slideshow obsahujúcu všetky nahraté obrázky</div>
</div>
<div id="moduleContent">
    <?php
    switch ($_GET['action']) {
        case "slideshow_active":
            Database::updateRows(TABLE_PREFIX . 'menu', array('slideshow' => '"1"'), '1', true);
            header("Location:index.php?module=menu");
            break;

        case "slideshow_assign":
            Database::deleteRows(TABLE_PREFIX . 'menu_slideshow_prepojenie', '1');
            $podstranky = Database::getRows('SELECT menu_id FROM ' . TABLE_PREFIX . 'menu WHERE slideshow = "1"');
            $slidy = Database::getRows('SELECT menu_slideshow_id FROM ' . TABLE_PREFIX . 'menu_slideshow WHERE 1');

            foreach ($podstranky as $podstranka) {
                foreach ($slidy as $slide) {
                    Database::insertRow(TABLE_PREFIX . 'menu_slideshow_prepojenie', array('menu_slideshow_id' => $slide->menu_slideshow_id, 'menu_id' => $podstranka->menu_id));
                }
            }
            header("Location:index.php?module=menu");
            break;

        case "insert":
            $defaultLang = $cLanguage->getLanguageCodes();
            $defaultLang = $defaultLang[0];

            if (isset($_POST) && !empty($_POST)) {
                //	zmenime  udaje   v   databaze
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude1 .= "`" . strtolower($val) . "_content`, `" . strtolower($val) . "_name_seo`, `" . strtolower($val) . "_name`, `" . strtolower($val) . "_page_title`, `" . strtolower($val) . "_description`, `" . strtolower($val) . "_keywords`, ";
                    $queryInclude2 .= "
					'" . addslashes(str_replace(array('"photos/', '"../../../photos/', '"../../../../photos/'), '"' . ROOTDIR . '/photos/', (str_replace(array('"docs/', '"../../../docs/', '"../../../../docs/'), '"' . ROOTDIR . '/docs/', html_entity_decode($_POST[strtolower($val) . '_content'], ENT_QUOTES, "UTF-8"))))) . "',
					'" . ReturnSEOFriendlyUrl(html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8"), $_POST['child_of'], strtolower($val)) . "',
					'" . html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8") . "',
					'" . html_entity_decode($_POST[strtolower($val) . '_title'], ENT_QUOTES, "UTF-8") . "',
					'" . html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8") . "',
					'" . html_entity_decode($_POST[strtolower($val) . '_keywords'], ENT_QUOTES, "UTF-8") . "', ";
                }

                //html_entity_decode(, ENT_QUOTES, "UTF-8")

                $queryString = "insert into " . TABLE_PREFIX . "menu (" . $queryInclude1 . "`child_of`, `private`, `module_id`, `redirect_to`, `slideshow`,heureka_category_name) values (" . $queryInclude2 . "" . ((!is_numeric($_POST['child_of'])) ? 'null' : $_POST['child_of']) . ", '" . ((isset($_POST['private'])) ? 1 : 0) . "', '" . $_POST['module_id'] . "', " . ((!is_numeric($_POST["redirect_to"]) or $_POST["redirect_to"] == 1) ? 'null' : $_POST["redirect_to"]) . ", '" . ((isset($_POST['slideshow'])) ? 1 : 0) . "','".mysql_real_escape_string($_POST['heureka_category_name'])."');";
                $ResultR = mysql_query($queryString);
                if ($ResultR) {

                    $menu_id = mysql_insert_id();

                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        check_seolinks($menu_id, strtolower($val));
                    }

                    right_menu_id($menu_id);

                    $left_menu_id = array();
                    $insert_menu_id = $menu_id;
                    left_menu_id($menu_id);

                    parent_id_right_menu_id($menu_id);

                    Message::setMessage('Stránka bola úspešne pridaná. ', 0);
                    //header("Location:index.php?module=menu");
                    //exit;
                } else {
                    if (mysql_errno())
                        print("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
                    Message::setMessage('Stránka nebola pridaná. ', 2);
                }
            }

            //	zistime si udaje do formulara
            $queryString = "select * from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $menu_id . "';";
            $ResultR = mysql_query($queryString);
            if ($ResultR) {
                if (mysql_num_rows($ResultR) == 1) {
                    $_POST = mysql_fetch_assoc($ResultR);
                }
            } else {
                print(mysql_error());
            }

            print '<h1>Pridanie položky v menu</h1>
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
                print '
					<tr>
						<td>Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" id="_name" name="' . strtolower($val) . '_name" value="' . $_POST[strtolower($val) . '_name'] . '" /></td>
					</tr>
				';
            }
                print '
					<tr>
						<td>Heureka kategória </td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" id="_name" name="heureka_category_name" value="' . $_POST['heureka_category_name'] . '" /></td>
					</tr>
				';

            print '
					<tr>
						<td>Nadradená položka</td>
						<td>&nbsp;</td>
						<td><select style="width: 256px;" name="child_of">
								<option value=""></option>';
            Menu::print_tree_combobox(NULL, $_GET['child_of']);
            print '			</select>
						</td>
					</tr>
					<tr>
						<td>Presmerovať odkaz do</td>
						<td>&nbsp;</td>
						<td><select style="width: 256px;" name="redirect_to">
								<option value="">Nepresmerovaný</option>';
            Menu::print_tree_combobox(NULL, $_GET['redirect_to']);
            print '			</select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="private" value="1"' . (($_POST['private'] == 1) ? ' checked="checked"' : '') . ' />nezobrazovať v menu</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="slideshow" value="1"' . (($_POST['slideshow'] == 1) ? ' checked="checked"' : '') . ' /> aktivovať slideshow</td>
					</tr>
					<tr>
						<td>Prepojiť s modulom</td>
						<td>&nbsp;</td>
						<td><select style="width: 256px;" name="module_id">' . return_combobox("select module_id as id, name as name from " . TABLE_PREFIX . "module where 1;", $_POST['module_id']) . '
							</select></td>
					</tr>
					';
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                print '
					<tr>
						<td valign="top">Obsah <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><textarea class="ckeditor" name="' . strtolower($val) . '_content">' . html_entity_decode($_POST[strtolower($val) . '_content'], ENT_QUOTES, "UTF-8") . '</textarea></td>
					</tr>';
                print '
					<tr>
						<td>META názov <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" id="_title" name="' . $val . '_title" value="' . $_POST[$val . '_page_title'] . '" /></td>
					</tr>
					<tr>
						<td>META popis <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="' . $val . '_description" value="' . $_POST[$val . '_description'] . '" /></td>
					</tr>
					<tr>
						<td>META kľúčové slová <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="' . $val . '_keywords" value="' . $_POST[$val . '_keywords'] . '" /></td>
					</tr>
			';
            }
            print '
					<tr>
						<td colspan="3">
			';
            include("../popups/foto.php");
            include("../popups/docs.php");
            print '		</td>
					</tr>
					<tr style="display: none;">
						<td colspan="3" style="text-align: left;"><strong>META INFORMÁCIE</strong></td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="left"><input class="button" type="submit" value="Upraviť" /></td>
					</tr>
				</table>
				</form>
			';
            break;

        case "update":
            $defaultLang = $cLanguage->getLanguageCodes();
            $defaultLang = $defaultLang[0];

            if (isset($_POST) && !empty($_POST)) {
                //	zmenime  udaje   v   databaze
                foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                    $queryInclude .= "`" . strtolower($val) . "_content` = '" . addslashes(str_replace(array('"photos/', '"../../../photos/', '"../../../../photos/'), '"' . ROOTDIR . '/photos/', (str_replace(array('"docs/', '"../../../docs/', '"../../../../docs/'), '"' . ROOTDIR . '/docs/', html_entity_decode($_POST[strtolower($val) . '_content'], ENT_QUOTES, "UTF-8"))))) . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_page_title` = '" . html_entity_decode($_POST[strtolower($val) . '_title'], ENT_QUOTES, "UTF-8") . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_description` = '" . html_entity_decode($_POST[strtolower($val) . '_description'], ENT_QUOTES, "UTF-8") . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_keywords` = '" . html_entity_decode($_POST[strtolower($val) . '_keywords'], ENT_QUOTES, "UTF-8") . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_name` = '" . html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8") . "', ";
                    $queryInclude .= "`" . strtolower($val) . "_name_seo` = '" . ReturnSEOFriendlyUrl(html_entity_decode($_POST[strtolower($val) . '_name'], ENT_QUOTES, "UTF-8"), $_POST['child_of'], strtolower($val)) . "', ";
                }

                $queryString = "
					update
						`" . TABLE_PREFIX . "menu`
					set
						`private` = '" . ((isset($_POST['private'])) ? 1 : 0) . "',
						`heureka_category_name` = '" .mysql_real_escape_string($_POST['heureka_category_name'])."',
						`child_of` = '" . ((!is_numeric($_POST['child_of'])) ? 'null' : $_POST['child_of']) . "',
						`redirect_to` = '" . ((!is_numeric($_POST["redirect_to"]) or $_POST["redirect_to"] == 1) ? 'null' : $_POST["redirect_to"]) . "',
						`slideshow` = '" . ((isset($_POST['slideshow'])) ? 1 : 0) . "',
						" . $queryInclude . "
						`module_id` = '" . $_POST['module_id'] . "'
					where
						1
					and
						`menu_id` = '" . $_GET['menu_id'] . "';";

                $ResultS = mysql_query($queryString);
                if ($ResultS) {

                    foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                        check_seolinks($_GET['menu_id'], strtolower($val));
                    }

                    $menu_id = $_GET['menu_id'];

                    right_menu_id($menu_id);

                    $left_menu_id = array();
                    $insert_menu_id = $menu_id;
                    left_menu_id($menu_id);

                    Message::setMessage('Stránka bola úspešne upravená. ', 0);
                    //	header("Location:index.php?module=menu");
                    //	exit;
                } else {
                    print(mysql_error());
                    Message::setMessage('Stránka nebola upravená. ', 2);
                }
            }

            //	zistime si udaje do formulara
            $queryString = "select * from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $_GET['menu_id'] . "';";
            $ResultR = mysql_query($queryString);
            if ($ResultR) {
                if (mysql_num_rows($ResultR) == 1) {
                    $_POST = mysql_fetch_assoc($ResultR);
                }
            } else {
                print(mysql_error());
            }

            print '
				<h1>Upravenie položky v menu</h1>
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
                print '
					<tr>
						<td width="15%">Názov <sup style="font-size: 12px; text-transform: uppercase; font-weight: bold;">' . $val . '</sup></td>
						<td width="5%">&nbsp;</td>
						<td width="80%"><input class="textbox1" type="text" name="' . strtolower($val) . '_name" value="' . $_POST[strtolower($val) . '_name'] . '" /></td>
					</tr>
				';
            }
                print '
					<tr>
						<td width="15%">Heureka kategória  </td>
						<td width="5%">&nbsp;</td>
						<td width="80%"><input class="textbox1" type="text" name="heureka_category_name" value="' . $_POST['heureka_category_name'] . '" /></td>
					</tr>
				';

            print '
					<tr>
						<td>Nadradená položka</td>
						<td>&nbsp;</td>
						<td><select style="width: 256px;" name="child_of">
								<option value=""></option>';
            Menu::print_tree_combobox(NULL, $_GET['child_of']);
            print '			</select>
						</td>
					</tr>
					<tr>
						<td>Presmerovať odkaz do</td>
						<td>&nbsp;</td>
						<td><select style="width: 256px;" name="redirect_to">
								<option value="">Nepresmerovaný</option>';
            Menu::print_tree_combobox(NULL, $_POST['redirect_to']);
            print '			</select>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="private" value="1"' . (($_POST['private'] == 1) ? ' checked="checked"' : '') . ' />nezobrazovať v menu</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td>&nbsp;</td>
						<td><input type="checkbox" name="slideshow" value="1"' . (($_POST['slideshow'] == 1) ? ' checked="checked"' : '') . ' /> aktivovať slideshow</td>
					</tr>
					<tr>
						<td>Prepojiť s modulom</td>
						<td>&nbsp;</td>
						<td><select style="width: 256px;" name="module_id">' . return_combobox("select module_id as id, name as name from " . TABLE_PREFIX . "module where 1;", $_POST['module_id']) . '
							</select></td>
					</tr>
					';
            foreach ($cLanguage->getLanguageCodes() as $key => $val) {
                print '
					<tr>
						<td valign="top">Obsah <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><textarea class="ckeditor" name="' . strtolower($val) . '_content">' . html_entity_decode($_POST[strtolower($val) . '_content'], ENT_QUOTES, "UTF-8") . '</textarea></td>
					</tr>';
                print '
					<tr>
						<td>Titulka stránky <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="' . strtolower($val) . '_title" value="' . $_POST[strtolower($val) . '_page_title'] . '" /></td>
					</tr>
					<tr>
						<td>Popis stránky <sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="' . strtolower($val) . '_description" value="' . $_POST[strtolower($val) . '_description'] . '" /></td>
					</tr>
					<tr>
						<td>Kľúčové slová použité na stránke<sup style="font-size: 12px; font-weight: bold; text-transform: uppercase;">' . $val . '</sup></td>
						<td>&nbsp;</td>
						<td><input class="textbox1" type="text" name="' . strtolower($val) . '_keywords" value="' . $_POST[strtolower($val) . '_keywords'] . '" /></td>
					</tr>
			';
            }
            print '
					<tr>
						<td colspan="3">
			';
            include("../popups/foto.php");
            include("../popups/docs.php");
            print '		</td>
					</tr>
					<tr>
						<td colspan="2"></td>
						<td align="left"><input class="button" type="submit" value="Upraviť" /></td>
					</tr>
				</table>
				</form>
			';

            break;

        case "delete":
            if (is_numeric($_GET['menu_id']) and $_GET["menu_id"] > 0) {
                $queryString = "delete from " . TABLE_PREFIX . "menu where 1 and menu_id = '" . $_GET['menu_id'] . "';";
                $ResultR = mysql_query($queryString);
                if ($ResultR) {
                    Message::setMessage('Stránka bola úspešne odstránená. ', 0);
                    header("Location:index.php?module=menu");
                    exit;
                } else {
                    print(mysql_error());
                }
            }
            break;

        default:
            $level = -1;
            $parny = false;

            //	zobrazime zoznam uzivatelov
            print '
				<h1>Štruktúra portálu</h1>
				<table summary="" border="0" cellspacing="0" cellpadding="2" class="tablelist item-list">
					<tr>
						<th>Názov</th>
						<th>Zobrazuje sa</th>
						<th>Modul</th>
						<th>&nbsp;</th>
					</tr>
			';
            Menu::gettree_table(NULL);
            print '
					<tr>
						<th colspan="4">&nbsp;</th>
					</tr>
				</table>
			';
    }
    ?>
</div>
<div class="clear"></div>