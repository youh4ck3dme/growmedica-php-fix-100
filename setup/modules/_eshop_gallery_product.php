<?
require_once('../shared/classes/class.eshop.php');
Installator::checkIfTableExist();
?>

<div id="leftMenu">
    <h2>Fotogáleria produktov</h2>
    <p>V tejto sekcii môžte spravovať fotografie produktov.</p>
    <ul class="side-menu">
        <li><a<?= ($_GET['module'] == 'eshop_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_product">Produkty</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_gallery_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_gallery_product&amp;eshop=1">Fotogaléria produktov</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_orders' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_orders&amp;eshop=1">Objednávky</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_manufacturers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_manufacturers&amp;eshop=1">Výrobcovia</a></li>
        <li><a<?= ($_GET['module'] == 'eshop_delivery_payment' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_delivery_payment&amp;eshop=1">Platba &amp; doručenie</a></li>
    </ul>
    <div id="submenu"></div>
</div>

<div id="moduleContent">
    <?
    if (isset($_POST)) {
        $count = count($_FILES['image_file']['name']);

        require_once('../shared/classes/class.image.php');

        $thumbs_dimensions = explode(',', PRODUCT_THUMBS_DIMENSIONS);
        $preview_dimensions = explode(',', PREVIEW_DIMENSIONS);
        $original_dimensions = explode(',', ORIGINAL_DIMENSIONS);

        $img = new abeautifulsite\SimpleImage();
        $background = new abeautifulsite\SimpleImage(null, $thumbs_dimensions[0], $thumbs_dimensions[1], '#fff');

        // prechod a upload odoslanych priloh
        for ($i = 0; $i < $count; $i++) {
            //print $_FILES['image_file']['name'][$i];
            $img = $img->load($_FILES['image_file']['tmp_name'][$i]);
            $u_image = $img->get_original_info();
            if (is_array($u_image) AND ! empty($u_image)) { // main_image
                $extension = '.' . pathinfo($_FILES['image_file']['name'][$i], PATHINFO_EXTENSION);
                preg_match('/image/', $u_image['mime'], $type_match);
                if ($type_match[0] == 'image') {
                    $name = sha1(time() . rand(0, 999)) . '-' . rand(0, 9999) . $extension;
                    try {
                        if ($img->get_height() >= $original_dimensions[1]) {
                            $original = $img->fit_to_height($original_dimensions[1])->save('../photos/original/' . $name);
                        } elseif ($img->get_width() >= $original_dimensions[0]) {
                            $original = $img->fit_to_width($original_dimensions[0])->save('../photos/original/' . $name);
                        } else {
                            $original = $img->save('../photos/original/' . $name);
                        }
                        $thumbnail = $img->load($_FILES['image_file']['tmp_name'][$i])->best_fit($thumbs_dimensions[0], $thumbs_dimensions[1]);
                        $background->overlay($thumbnail)->save('../photos/thumbnail/' . $name);
                        $preview = $img->load($_FILES['image_file']['tmp_name'][$i])->best_fit($preview_dimensions[0], $preview_dimensions[1])->save('../photos/preview/' . $name);

                        //	vlozime zaznam do databazy
                        $queryString = "INSERT INTO " . TABLE_PREFIX . "_photo_images
                                            (photo_category_id, menu_id, name, description, src, image_type, owner, date, sorter)
                                        VALUES
                                            ('" . $_POST['category_id'] . "', '0','', '', '" . $name . "', '" . $extension . "', '', NOW(), '0');";
                        mysql_query($queryString);
                    } catch (Exception $e) {
                        Message::setMessage('Chyba nahrávania obrázka: ', $e->getMessage(), 2);
                    }
                }
            } else {
                Message::setMessage('Chyba nahrávania obrázka ', $_FILES['image_file']['name'], 2);
            }
        }
    }


    if ($_GET['action'] == 'delete') {
        unlink('../photos/thumbnail/' . $_GET['image_src']);
        unlink('../photos/preview/' . $_GET['image_src']);
        unlink('../photos/original/' . $_GET['image_src']);

        Message::setMessage('Obrázok bol úspešne odstránený.', 0);
        $queryString = "DELETE FROM " . TABLE_PREFIX . "_photo_images WHERE src = '" . $_GET['image_src'] . "';";
        mysql_query($queryString);
    }
    ?>
    <script type="text/javascript" src="jscript/confirmbox.js"></script>
    <script type="text/javascript">
<!--

        function MM_findObj(n, d) { //v4.01
            var p, i, x;
            if (!d)
                d = document;
            if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
                d = parent.frames[n.substring(p + 1)].document;
                n = n.substring(0, p);
            }
            if (!(x = d[n]) && d.all)
                x = d.all[n];
            for (i = 0; !x && i < d.forms.length; i++)
                x = d.forms[i][n];
            for (i = 0; !x && d.layers && i < d.layers.length; i++)
                x = MM_findObj(n, d.layers[i].document);
            if (!x && d.getElementById)
                x = d.getElementById(n);
            return x;
        }

        function MM_validateForm() { //v4.0
            var i, p, q, nm, test, num, min, max, errors = '', args = MM_validateForm.arguments;
            for (i = 0; i < (args.length - 2); i += 3) {
                test = args[i + 2];
                val = MM_findObj(args[i]);
                if (val) {
                    nm = val.name;
                    if ((val = val.value) != "") {
                        if (test.indexOf('isEmail') != -1) {
                            p = val.indexOf('@');
                            if (p < 1 || p == (val.length - 1))
                                errors += '- ' + nm + ' must contain an e-mail address.\n';
                        } else if (test != 'R') {
                            num = parseFloat(val);
                            if (isNaN(val))
                                errors += '- ' + nm + ' must contain a number.\n';
                            if (test.indexOf('inRange') != -1) {
                                p = test.indexOf(':');
                                min = test.substring(8, p);
                                max = test.substring(p + 1);
                                if (num < min || max < num)
                                    errors += '- ' + nm + ' must contain a number between ' + min + ' and ' + max + '.\n';
                            }
                        }
                    } else if (test.charAt(0) == 'R')
                        errors += '- ' + nm + ' is required.\n';
                }
            }
            if (errors)
                alert('The following error(s) occurred:\n' + errors);
            document.MM_returnValue = (errors == '');
        }

        function sortGallery(itemId)
        {
            var w = window.open('../setup/modules/_eshop_gallery_product_sort.php?category_id=' + itemId, 'gallery', 'width=400,height=500');
            if (w) {
                w.focus();
            }
        }

        //-->
    </script>

    <?
    $sql_str = "SELECT product_id AS id, sk_name AS name FROM " . TABLE_PREFIX . "product order by product_id;";
    $sql_result = mysql_query($sql_str);
    if ($sql_str) {
        print '
					<h1>Fotogaléria</h1>
					<table border="0" cellspacing="0" cellpadding="2" class="tablelist" summary="">';
        print '
						<tr>
							<th>Názov</th>
							<th>Obrázok</th>
							<th>&nbsp;</th>
						</tr>
				';
        $_parny = false;
        while ($row = mysql_fetch_object($sql_result)) {
            print '
						<tr class="' . ((!$_parny) ? "style1" : "style2") . '">
							<td><a href="./index.php?module=eshop_gallery_product&photo_category_id=' . $row->id . '">[<span style="color: red;">' . $row->id . '</span>] ' . $row->name . '</strong></a> </td>
							<td>&nbsp;</td>
							<td>
							&nbsp;</td>
						</tr>
					';
            $_parny = !$_parny;
        }
        print '
						<tr>
							<th colspan="3">&nbsp;</th>
						</tr>
					</table>';
    } else {
        print(mysql_error());
    }
    @mysql_free_result($ResultA);
    ?>
    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <form action="" method="post" enctype="multipart/form-data" id="formular">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <span class="text-label"><strong>Kategória:</strong></span><br />
                                <select name="category_id" id="category_id">
                                    <?php
                                    print(return_combobox("SELECT product_id AS id, sk_name AS name FROM " . TABLE_PREFIX . "product;", $_REQUEST['photo_category_id']));
                                    ?>
                                </select>
                            </td>
                            <td>
                                <span class="text-label"><strong>Súbor:</strong></span><strong> </strong><br />
                                <input name="image_file[]" type="file" id="image_file" multiple="multiple" />
                                <input name="submit" type="submit" value="Upload" />
                            </td>
                            <td>

                            </td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                            <td colspan="3"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                    <?php
                                    $images_pline = 3;

                                    $sql_str = "SELECT CEIL(COUNT(photo_images_id) / " . $images_pline . ") AS count FROM " . TABLE_PREFIX . "_photo_images WHERE 1 AND photo_category_id = '" . $_REQUEST['photo_category_id'] . " ORDER BY sorter ASC';";
                                    $result = @mysql_query($sql_str);
                                    if ($result != 0) {
                                        if (mysql_num_rows($result) > 0) {
                                            $line = mysql_fetch_object($result);
                                            if ($line->count > 0) {
                                                for ($i = 0; $i < $line->count; $i++) {
                                                    $sql_str = "SELECT *, name as image_name, src as image_src FROM " . TABLE_PREFIX . "_photo_images WHERE 1 AND photo_category_id = '" . $_REQUEST['photo_category_id'] . "' ORDER BY sorter ASC LIMIT " . ($i * $images_pline) . "," . $images_pline . ";";
                                                    $result2 = @mysql_query($sql_str);
                                                    if ($result2 != 0) {
                                                        $num_rows = mysql_num_rows($result2);
                                                        if ($num_rows > 0) {
                                                            $j = 0;
                                                            print "<TR>\n";
                                                            while ($line2 = mysql_fetch_object($result2)) {
                                                                $dlink = "<br>[<a href=\"javascript:ConfirmBoxAc('Are you sure you want to delete this picture?', 'index.php?module=eshop_gallery_product&action=delete&image_src=" . $line2->image_src . "&photo_category_id=" . $_REQUEST['photo_category_id'] . "', '');\">DELETE</a>]";
                                                                $ilink = "<img src=\"" . ROOTDIR . "/photos/thumbnail/" . $line2->image_src . "\" border=\"0\" onClick=\"javascript:OpenImage('photos/thumbnail/" . $line2->image_src . "');\" style='cursor:pointer'>";
                                                                $j++;
                                                                if (($j - 1) == $num_rows) {
                                                                    print "<TD COLSPAN=" . ($images_pline - $num_rows) + ($images_pline - ($num_rows - 1)) . " WIDTH=\"80\" HEIGHT=\"60\" alidn=\"center\" valign=\"top\">\n";
                                                                    print "	  " . $ilink . "<br>" . reset(explode(".", $line2->image_name)) . "\n" . $dlink;
                                                                    print "</TD>\n";
                                                                } else {
                                                                    print "<TD WIDTH=\"80\" HEIGHT=\"60\" align=\"center\" valign=\"top\">" . $ilink . "<br>" . reset(explode(".", $line2->image_name)) . $dlink . "</TD>";
                                                                }
                                                                if (($j) < $num_rows) {
                                                                    print "<TD>&nbsp;</TD>";
                                                                }
                                                            }
                                                            print "</TR>\n";
                                                            print "<tr><td colspan=\"8\">&nbsp;</td></tr>";
                                                        }
                                                    } else
                                                        print mysql_error();

                                                    @mysql_free_result($result2);
                                                }
                                            }
                                        }
                                    } else
                                        print mysql_error();

                                    @mysql_free_result($result);
                                    ?>
                                </table>
                            </td>
                        </tr>
                    </table>
                </form>
            </td>
        </tr>
    </table>

    <div class="both">

    </div>

</div>
</div>