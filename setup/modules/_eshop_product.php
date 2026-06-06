<?
require_once('../shared/classes/class.eshop.php');
Installator::checkIfTableExist();
?>
<div id="leftMenu">
    <? include('_eshop-left-menu.php'); ?>
    <div id="submenu">
        <a href="./index.php?module=eshop_product_content&action=insert<?= ($_GET['category_id'] != "" ? '&amp;category_id=' . $_GET['category_id'] : '') ?>" class="addNew">Pridať nový<br />  produkt</a>
        <a href="./index.php?module=eshop_product" class="detail">Zobraziť všetky <br /> produkty</a>
        <a href="./index.php?module=eshop_product&amp;display=special" class="detail">Zobraziť akciové <br /> produkty</a>
    </div>
</div>
<?
if (!$user->isAdmin()) {
    exit;
}

if (isset($_REQUEST['delete']) && $_REQUEST['delete'] == 1) {


    /*
      $query = 'SELECT product_id, image_src FROM ' . TABLE_PREFIX . 'product WHERE 1 AND image_src="" OR image_src IS NULL;';
      $result = mysql_query($query);
      $i = 0;
      while ($row = mysql_fetch_object($result)) {
      $i++;
      if (!empty($row->image_src)) {
      unlink('../photos/thumbnail/' . $row->image_src);
      unlink('../photos/preview/' . $row->image_src);
      unlink('../photos/original/' . $row->image_src);
      }

      $query1 = 'SELECT src FROM ' . TABLE_PREFIX . '_photo_images WHERE 1 AND photo_category_id="' . $row->product_id . '"';
      $result1 = mysql_query($query1);
      if ($result1) {
      while ($row1 = mysql_fetch_object($result1)) {
      unlink('../photos/thumbnail/' . $row1->src);
      unlink('../photos/preview/' . $row1->src);
      unlink('../photos/original/' . $row1->src);
      }
      }

      $query2 = 'DELETE FROM ' . TABLE_PREFIX . 'product WHERE product_id ="' . $row->product_id . '"';
      mysql_query($query2);

      $query3 = 'DELETE FROM ' . TABLE_PREFIX . 'product_color WHERE product_id ="' . $row->product_id . '"';
      mysql_query($query3);

      $query4 = 'DELETE FROM ' . TABLE_PREFIX . 'product_files WHERE product_id ="' . $row->product_id . '"';
      mysql_query($query4);

      $query5 = 'DELETE FROM ' . TABLE_PREFIX . 'product_menu WHERE product_id ="' . $row->product_id . '"';
      mysql_query($query5);

      $query6 = 'DELETE FROM ' . TABLE_PREFIX . 'product_type WHERE product_id ="' . $row->product_id . '"';
      mysql_query($query6);

      $query7 = 'DELETE FROM ' . TABLE_PREFIX . '_photo_images WHERE photo_category_id ="' . $row->product_id . '"';
      mysql_query($query7);
      }
      echo mysql_error();
      die('--');
     */

    $query = 'SELECT src FROM ' . TABLE_PREFIX . '_photo_images WHERE 1 AND photo_category_id="' . $_REQUEST['product_id'] . '"';
    $result = mysql_query($query);
    if ($result) {
        while ($row = mysql_fetch_object($result)) {
            unlink('../photos/thumbnail/' . $row->src);
            unlink('../photos/preview/' . $row->src);
            unlink('../photos/original/' . $row->src);
        }
    }
    $query = 'SELECT image_src FROM ' . TABLE_PREFIX . 'product WHERE 1 AND product_id="' . $_REQUEST['product_id'] . '"';
    $result = mysql_query($query);
    if ($result) {
        $row = mysql_fetch_object($result);
        if (!empty($row->image_src)) {
            unlink('../photos/thumbnail/' . $row->image_src);
            unlink('../photos/preview/' . $row->image_src);
            unlink('../photos/original/' . $row->image_src);
        }
    }

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

    $query = 'DELETE FROM ' . TABLE_PREFIX . '_photo_images WHERE photo_category_id = ' . (int) $_REQUEST['product_id'];
    mysql_query($query);

    Message::setMessage('Produkt bol úspešne odstránený.', 0);
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    //header('Location: index.php?module=eshop_product');
    exit;
}
if ($_GET['limit']) {
    $_SESSION['userPrefs']['admin_items_per_page'] = $_GET['limit'];
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (!isset($_SESSION['userPrefs']['admin_items_per_page']))
    $_SESSION['userPrefs']['admin_items_per_page'] = '100';

$obj_catalogue = new Catalogue;
?>
<div id="moduleContent">
    <h1 class="float-left">Zoznam produktov</h1>
    <form class="search-shop" action="" method="get">
        <input type="text" name="q" size="42" value="<?= $_GET['q']; ?>" placeholder="Zadajte kód alebo názov produktu" />
        <input type="hidden" name="module" value="eshop_product" />
        <?
        if (is_numeric($_GET["category_id"])) {
            echo '<input type="hidden" name="category_id" value="' . $_GET["category_id"] . '" />';
        }
        ?>
        <?
        if (isset($_GET["q"])) {
            echo '<input type="button" onClick= "window.location=\'index.php?module=eshop_product' . (is_numeric($_GET["category_id"]) ? '&amp;category_id=' . $_GET["category_id"] : '') . '\'" value="Zrušiť vyhľadávanie" />';
        }
        ?>
        <input type="submit" value="Hľadať" />
    </form>
    <div class="limit-container">
        <div class="category-select">
            <select name="category_id" id="category_id" onchange="javascript:document.location = './index.php?module=eshop_product&amp;category_id=' + document.getElementById('category_id').value;">
                <option>Hlavná kategória</option>
                <? Menu::print_tree_combobox(ESHOP_MAIN_CATEGORY, $_GET['category_id'], 1); ?>
            </select>
        </div>
        <div class="limit">
            <a<?= ($_SESSION['userPrefs']['admin_items_per_page'] == '100' ? ' class="selected"' : ''); ?> href="index.php?module=eshop_product<?= (isset($_GET["display"]) ? '&amp;display=' . $_GET["display"] : '') . (is_numeric($_GET["category_id"]) ? '&amp;category_id=' . $_GET["category_id"] : ''); ?>&amp;limit=100">100</a>
            <a<?= ($_SESSION['userPrefs']['admin_items_per_page'] == '250' ? ' class="selected"' : ''); ?> href="index.php?module=eshop_product<?= (isset($_GET["display"]) ? '&amp;display=' . $_GET["display"] : '') . (is_numeric($_GET["category_id"]) ? '&amp;category_id=' . $_GET["category_id"] : ''); ?>&amp;limit=250">250</a>
            <a<?= ($_SESSION['userPrefs']['admin_items_per_page'] == '500' ? ' class="selected"' : ''); ?> href="index.php?module=eshop_product<?= (isset($_GET["display"]) ? '&amp;display=' . $_GET["display"] : '') . (is_numeric($_GET["category_id"]) ? '&amp;category_id=' . $_GET["category_id"] : ''); ?>&amp;limit=500">500</a>
        </div>
    </div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0" id="tableList" class="item-list">
        <tr>
            <th style="padding-left:5px">Id</th>
            <th>Kód</th>
            <th></th>
            <th>Názov produktu</th>
            <th style="text-align: center;">Cena bez DPH</th>
            <th style="text-align: center;">Cena s DPH <small style="line-height: 10px;"><br />(so zľavou)</small></th>
            <th>ks</th>
            <th class="narrow-1">&nbsp;</th>
            <th class="narrow-1">&nbsp;</th>
            <th>&nbsp;</th>
        </tr>
        <?
        if (isset($_GET['q']) && !empty($_GET['q'])) { // search
            $queryString = 'SELECT p.*, p.product_id, p.sk_name as name, p.sk_name_seo as name_seo, p.image_src, p.code_1, p.upravene, p.date as date, SUM(pt.pocet) AS pocet FROM `' . TABLE_PREFIX . 'product` AS p ';

            $queryString .= 'LEFT JOIN ' . TABLE_PREFIX . 'product_menu AS pm USING (product_id) ';
            $queryString .= 'LEFT JOIN ' . TABLE_PREFIX . 'product_type AS pt USING (product_id) ';
            $queryString .= 'WHERE 1 ';

            if (is_numeric($_GET["category_id"]))
                $queryString .= 'AND pm.menu_id = "' . $_GET["category_id"] . '" ';

            $queryString .= 'AND LOWER(p.sk_name) LIKE "%' . strtolower($_GET['q']) . '%" or code_1 LIKE "%' . strtolower($_GET['q']) . '%" ';

            $queryString .= 'GROUP BY p.product_id ORDER BY p.sorter ASC, p.product_id ASC';
        } else {
            if (is_numeric($_GET["category_id"])) { // action
                $queryString = 'SELECT DISTINCT p.*, pm.product_id, pm.menu_id AS menu, p.sk_name as name, p.sk_name_seo as name_seo, SUM(pt.pocet) AS pocet FROM ' . TABLE_PREFIX . 'product AS p ';
            } else { // all
                $queryString = 'SELECT DISTINCT p.*, p.product_id, p.sk_name as name, p.sk_name_seo as name_seo, p.price, p.price_old, p.image_src, p.code_1, p.available, p.date, SUM(pt.pocet) AS pocet FROM ' . TABLE_PREFIX . 'product as p ';
            }
            $queryString .= 'LEFT JOIN ' . TABLE_PREFIX . 'product_type AS pt USING (product_id) ';

            if (is_numeric($_GET["category_id"])) { // category
                $queryString .= 'LEFT JOIN ' . TABLE_PREFIX . 'product_menu AS pm USING (product_id) ';
            }

            if ($_GET["display"] == "special")
                $queryString .= ' WHERE (p.price_old > 0 AND p.price_old > p.price) GROUP BY p.product_id ORDER BY p.sorter ASC, p.product_id ASC'; //  " AND ((p.price_old > 0 AND p.price_old > p.price) OR percentage_discount!='0')";

            if (is_numeric($_GET["category_id"]))
                $queryString .= 'WHERE pm.menu_id = "' . $_GET["category_id"] . '" GROUP BY p.product_id ORDER BY p.sorter ASC, p.product_id ASC';

            //if (!is_numeric($_GET["category_id"]) and ! isset($_GET["display"]))
            //    $queryString .= " AND (p.price_old > 0 AND p.price_old > p.price) GROUP BY p.product_id ORDER BY p.product_id DESC';


            if ($_GET["display"] != "special" AND ! is_numeric($_GET["category_id"])) { // all
                $queryString .= 'WHERE 1 AND deleted="0" GROUP BY p.product_id ORDER BY p.sorter ASC, p.product_id ASC';
            }
        }

        function hasCategory($product_id = null) {
            if ($product_id == null)
                return false;

            $query = 'SELECT COUNT(menu_id) AS total FROM ' . TABLE_PREFIX . 'product_menu WHERE product_id="' . $product_id . '" GROUP BY product_id;';
            $result = mysql_query($query);
            $row = mysql_fetch_object($result);
            $count = $row->total;
            if ($row->total == '')
                $count = '0';
            return $count;
        }

        tabulator1($queryString, true);
        $products_count = mysql_num_rows(mysql_query($queryString));
        $re1 = mysql_query($queryString . $limit);
        if ($re1) {
            $parny = false;
            while ($line = @mysql_fetch_object($re1)) {
                print '<tr class="' . ((!$_parny) ? "style1" : "style2") . '">';
                ?>
                <td><?= $line->product_id; ?></td>
                <td><?= (!empty($line->image_src) ? '<img src="../photos/thumbnail/' . $line->image_src . '" width="20" alt="' . $line->name . '" />' : '---'); ?></td>
                <td class="code"><?= $line->code_1; ?></td>
                <td class="title"><a href="./index.php?module=eshop_product_content&amp;action=update&amp;product_id=<?= $line->product_id . (!empty($line->menu_id) ? '&amp;category_id=' . $line->menu_id : ''); ?>"><?= $line->name; ?></a></td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format(($line->price / VAT_COEFFICIENT), 2, '.', ' ') ?>&nbsp;&euro;</td>
                <td style="text-align: right; white-space: nowrap;"><?= number_format($line->price, 2, '.', ' '); ?>&nbsp;&euro;</td>
                <td style="text-align: right; <?= ($line->pocet > 0 ? 'color: green;' : ($line->available == 1 ? 'color: red;' : '')); ?>"><?= $line->pocet; ?></td>
                <td style="text-align: center;"><?= ($line->available == 1) ? '<span class="status active">zobrazený</span>' : '<span class="status inactive">nezobrazený</span>'; ?></td>
                <td>
                    <?
                    echo ($line->manufacturer_id == '0') ? '<span class="status inactive">bez výrobcu</span>' : '';
                    echo (strtotime(date("Y-m-d H:i")) < strtotime($line->date . NEW_PRODUCT_LENGTH)) ? '<span class="status new">nový produkt</span>' : '';
                    echo (hasCategory($line->product_id) == '0') ? '<span class="status inactive">nezaradený produkt</span>' : '';
                    echo (empty($line->image_src) OR $line->image_src == '0') ? '<span class="status img-missing">bez obrázka</span>' : '';

                    echo ($line->recommend == '1') ? '<span class="status recommended">odporúčaný</span>' : '';
                    echo ($line->action == '1') ? '<span class="status action">akcia</span>' : '';
                    echo ($line->sale == '1') ? '<span class="status sale">výpredaj</span>' : '';
                    echo ($line->novelty == '1') ? '<span class="status novelty" style="background-color: #4A6D22;">novinka</span>' : '';
                    echo ($line->delivery_time == '1') ? '<span class="status delivery_time">skladom</span>' : '';
                    ?>
                </td>
                <td class="actions">
                    <?= (!empty($_GET['category_id']) ? '<a href="javascript:;" onclick=\'javascript:product("' . $_GET['category_id'] . '");\'>Zoradiť</a>' : ''); ?>
                    <a href="./index.php?module=eshop_product_content&amp;action=gallery&amp;product_id=<?= $line->product_id; ?>">Galéria</a>
                    <a href="./index.php?module=eshop_product_content&amp;action=update&amp;product_id=<?= $line->product_id . (!empty($line->menu_id) ? '&amp;category_id=' . $line->menu_id : ''); ?>">Editovať</a>
                    <a href="javascript:confirmAction('Naozaj chcete zmazať produkt <?= $line->name; ?>?', '', './index.php?module=eshop_product&amp;delete=1&amp;product_id=<?= $line->product_id . (!empty($line->menu_id) ? '&amp;category_id=' . $line->menu_id : ''); ?>');">Odstrániť</a>
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

    <div class="product-count">
        <p>Produktov: <strong><?= $products_count; ?></strong></p>
    </div>
    <?= pagination($queryString, 'index.php?module=' . $_GET['module'] . (isset($_GET['eshop']) ? '&amp;eshop=' . $_GET['eshop'] : '') . (isset($_GET['eshop']) ? '&amp;display=' . $_GET['display'] : ''), $_GET['page'], true); ?>
    <div class="clear"></div>
    <div class="category-select bottom">
        <label>
            <span>Zoznam kategórií</span>
            <select name="category_id" id="category_id2" onchange="javascript:document.location = './index.php?module=eshop_product_content&amp;category_id=' + document.getElementById('category_id2').value;">
                <option>Hlavná kategória</option>
                <? Menu::print_tree_combobox(ESHOP_MAIN_CATEGORY, $_GET['category_id'], 1); ?>
            </select>
        </label>
    </div>
    <div class="clear"></div>
</div>