<?
require_once('../shared/classes/class.eshop.php');
Installator::checkIfTableExist();
?>

<div id="leftMenu">
    <h2>Produkty</h2>
    <p>V tejto sekcii môžte spravovať všetky vaše produkty.</p>
    <div id="submenu">
        <a href="./index.php?module=eshop_product_content&amp;eshop=1&amp;category_id=<?= $_GET['category_id'] ?> " class="addNew">Pridať nový<br />  produkt</a>
        <a href="./index.php?module=eshop_product&amp;eshop=1&amp;display=all" id="detail">Zobraziť všetky <br /> produkty</a>
        <a href="./index.php?module=eshop_product&amp;eshop=1&amp;display=special" id="detail">Zobraziť akciové <br /> produkty</a>
    </div>
</div>
<?
if (!$user->isAdmin()) {
    exit;
}
?>
<script>
    function confirmAction(message, abort_action, ok_action) {
        var msg = confirm(message);
        if (!msg) {
            if (abort_action == '') {
                //return false;
                this.location;
            } else {
                this.location = abort_action;
            }
        } else {
            document.location.href = ok_action;
        }
    }

    function product(itemId)
    {
        var w = window.open('modules/_eshop_product_sort.php?child_id=' + itemId, 'product', 'width=400,height=500');
        if (w) {
            w.focus();
        }
    }
</script>

<?
if (isset($_REQUEST['delete']) && $_REQUEST['delete'] == 1) {
    $query = 'DELETE FROM ' . TABLE_PREFIX . 'product WHERE product_id = ' . (int) $_REQUEST['product_id'];
    mysql_query($query);

    $query = 'DELETE FROM ' . TABLE_PREFIX . 'product_color WHERE product_id = ' . (int) $_REQUEST['product_id'];
    mysql_query($query);

    $query = 'DELETE FROM ' . TABLE_PREFIX . 'product_files WHERE product_id = ' . (int) $_REQUEST['product_id'];
    mysql_query($query);

    $query = 'DELETE FROM ' . TABLE_PREFIX . 'product_menu WHERE product_id = ' . (int) $_REQUEST['product_id'];
    mysql_query($query);

    $query = 'DELETE FROM ' . TABLE_PREFIX . 'product_type WHERE product_id = ' . (int) $_REQUEST['product_id'];
    mysql_query($query);
}


$obj_categories = new Categories;
$obj_categories->find_subcategories(ESHOP_MAIN_CATEGORY);
$navmenu = $obj_categories->get_categories();
?>
<div id="moduleContent">
    <form method="post" enctype="multipart/form-data" name="pridat_uzivatela" id="pridat_uzivatela" onsubmit="return overForm(this);">
        <h1>Zoznam produktov</h1>
        <form action="" method="post">
            <input style="float:right;" type="text" name="search_string" size="42"/>
            <input type="submit" name="odoslat" value="Hľadať" />
        </form>
        <table summary="" border="0" cellspacing="0" cellpadding="2" class="tablelist">
            <tr>
                <td><table border="0" align="right" cellpadding="0" cellspacing="0">

                        <p>Vybrať z kategórie:</p> <select style="float:left;"name="category_id" id="category_id" onchange="javascript:document.location = './index.php?module=eshop_product&eshop=1&category_id=' + document.getElementById('category_id').value;">
                            <option value="">Neuvedené</option>
                            <?
                            foreach ($navmenu as $row) {
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </table>
                </td>
            </tr>
            <tr>
                <td><?
                    if (isset($_POST) && !empty($_POST)) {
                        $queryString = "SELECT m.*, m.product_id as id, sk_name as name, sk_name_seo as name_seo, image_src, upravene FROM `" . TABLE_PREFIX . "product` AS m WHERE 1 and LOWER(m.sk_name) LIKE '%" . strtolower($_REQUEST['search_string']) . "%' or code_1 LIKE '%" . strtolower($_REQUEST['search_string']) . "%';";

                        if ($error1 == true and $error2 == true and $error3 == true and $error4 == true) {
                            print "<i><b>" . $cTranslator->getTranslation("Je mi ľúto, nenašli sa žiadné záznamy vyhovujúce zadaným kritériám.") . "</b></i>";
                        }
                    } else {
                        if ($_GET["display"] == "all" || is_numeric($_GET["category_id"]) || (!is_numeric($_GET["category_id"]) and ! isset($_GET["display"])))
                            $queryString = "select DISTINCT pm.product_id,p.* from " . TABLE_PREFIX . "product as p left join " . TABLE_PREFIX . "product_menu as pm using (product_id) where 1 and deleted = '0'";

                        if ($_GET["display"] == "special" || is_numeric($_GET["category_id"]) || (!is_numeric($_GET["category_id"]) and ! isset($_GET["display"])))
                            $queryString = "select DISTINCT pm.product_id,p.* from " . TABLE_PREFIX . "product as p left join " . TABLE_PREFIX . "product_menu as pm using (product_id) where 1 and deleted = '0'";

                        if ($_GET["display"] == "special")
                            $queryString .= " and (p.price_old > 0 and p.price_old > p.price)";

                        if (is_numeric($_GET["category_id"]))
                            $queryString .= " and pm.menu_id = '" . $_GET["category_id"] . "' group by p.product_id order by p.sorter asc, p.product_id asc";

                        if (!is_numeric($_GET["category_id"]) and ! isset($_GET["display"]))
                            $queryString .= " and (p.price_old > 0 and p.price_old > p.price) group by p.product_id order by p.product_id desc";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <td><table width="100%" border="0" cellpadding="0" cellspacing="0" id="tableList">
                        <tr>
                            <th style="padding-left:5px">Id</th>
                            <th>Názov produktu</th>
                            <th>Cena (EUR)</th>
                            <th>Produkt je možné kúpiť</th>
                            <th>&nbsp;</th>
                        </tr>
                        <?php
                        $sql = "SELECT * FROM " . TABLE_PREFIX . "product WHERE 1 AND menu_id = '" . $_GET['category_id'] . "' order by name asc;";
                        //echo $queryString;
                        $re1 = @mysql_query($queryString . $limit);
                        if ($re1) {
                            $parny = false;
                            while ($line = @mysql_fetch_object($re1)) {
                                ?>
                                <tr class="<?= ((!$_parny) ? "style1" : "style2"); ?>">
                                    <td style="padding-left:5px"><?= $line->product_id ?></td>
                                    <td><?= $line->sk_name ?> [ <?= $line->en_name ?> ]</td>
                                    <td style="padding-left:5px"><?= $line->price ?></td>
                                    <td style="padding-left:40px"><?= ($line->available == 1) ? "áno " : "nie" ?>                    </td>
                                    <td>
                                        <a href='javascript:;' onclick='javascript:product(" <?= $_GET['category_id'] ?> ");'>zoradiť</a>
                                        <a href="./index.php?module=eshop_product_content&amp;eshop=1&amp;product_id=<?= $line->product_id ?>&amp;category_id=<?= $line->menu_id ?>"><img src="../images/admin/eshop_admin/koliesko_edit.gif" alt="Editovať produkt" width="17" height="17" border="0" /></a> <a href="javascript:confirmAction('Naozaj zmaza produkt?', '', './index.php?module=eshop_product&amp;eshop=1&amp;delete=1&amp;category_id=<?= $_GET['category_id'] ?>&amp;product_id=<?= $line->product_id ?>');"><img src="../images/admin/eshop_admin/koliesko_delete.gif" alt="Zmazať produkt" width="17" height="17" border="0" /></a>
                                    </td>
                                </tr>
                                <?php
                                $_parny = !$_parny;
                            }
                        } else {
                            print mysql_error();
                        }
                        @mysql_free_result($re1);
                        ?>
                    </table>
                </td>
            </tr>
            <tr>
                <td><?php
                    //	print(_tabulator($sql));
                    ?>
                </td>
            </tr>
            <tr>
                <td align="right"><table border="0" align="right" cellpadding="0" cellspacing="0">
                        <tr>
                            <td style="white-space: nowrap;">Zoznam kategórií</td>
                            <td>&nbsp;</td>
                            <td><select name="category_id" id="category_id2" onchange="javascript:document.location = './index.php?module=eshop_product_content&eshop=1&category_id=' + document.getElementById('category_id2').value;">
                                    <option value="">Neuvedené</option>
                                    <?
                                    foreach ($navmenu as $row) {
                                        echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                    </table></td>
            </tr>
            <tr>
                <td align="right">
                    <a href="./index.php?module=eshop_product_content&amp;eshop=1&amp;category_id=<?= $_GET['category_id'] ?>" style="color:green;">
                        <img src="../images/admin/eshop_admin/file_ikona.gif" width="16" height="16" border="0" align="absmiddle" />Pridať nový produkt do vybranej kategórie</a>
                </td>
            </tr>
            <tr><th>&nbsp;</th></tr>
        </table>
    </form>
</div>