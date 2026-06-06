<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_eshop.css" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
$js_file = '<script type="text/javascript" src="js/mod_step1_validation.js"></script>'; /* <script type="text/javascript" src="js/mod_xxc.js"></script> */
$MODULE_HEADER = $css_file . $js_file;
// definovanie potrebnych inline js action // uvadzat bez oznacenia <script>
$inline_js = ''; /* $(function() { $( "#datepicker" ).datepicker(); }); */
$MODULE_INLINE_JS = $inline_js;
// titulka (ak je prazdna, pouzije sa titulka zo struktury SQL)
$MODULE_TITLE = "";
// seo prvky daneho modulu
$MODULE_DESCRIPTION = "";
$MODULE_KEYWORDS = "";

// nacitanie obsahu modulu
ob_start();

// VYKONANIE AKCII
//if ($user->isAuthenticated()) {

switch ($navigateArrayUrlWithoutBase[0]) {
    case "cart":
    case "kosik":
        if ($products_count == '0') {
            //echo '<h1 class="cart">' . $cTranslator->getTranslation('Nákupný košík je prázdny', 0) . '</h1>';
            echo '<div class="page-title">';
            echo '<h1>' . $cTranslator->getTranslation('Nákupný košík je prázdny', 0) . '</h1>';
            echo '</div>';

            echo '<div class="cart-empty">';
            echo '<p>' . $cTranslator->getTranslation('Vo Vašom košíku nie sú žiadne položky.', 0) . '</p>';
            echo '<p>' . $cTranslator->getTranslation('Kliknite', 0) . ' <a href="' . ROOTDIR . '">' . $cTranslator->getTranslation('sem', 0) . '</a> ' . $cTranslator->getTranslation('pre pokračovanie v nákupe.', 0) . '</p>';
            echo '</div>';
        } else {
            echo '<div class="page-title title-buttons">';
            echo '<h1 class="cart">' . $cTranslator->getTranslation('Nákupný košík', 0) . '</h1>';
            if ($navigateArrayUrlWithoutBase[1] != 'step3') {
                echo '<button type="button" title="Pokračovať k pokladni" class="button btn-proceed-checkout btn-checkout" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '/kosik/step1\';">' . $cTranslator->getTranslation('Pokračovať k pokladni', 0) . '</button>';
            }
            echo '<div class="clear"></div>';
            echo '</div>';
            // PRED KAZDYM POUZITIM TREBA NAJPRV OBJEKT UNSERIALIZOVAT ZO SESSION
            // A PO ZMENE OBSAHU OPAT SERIALIZOVAT
            $obj_cart = unserialize($_SESSION['serialized_cart']);

            switch ($navigateArrayUrlWithoutBase[1]) {
                case 'increase_item':
                    $obj_cart->increase_item($navigateEnd);
                    $_SESSION['serialized_cart'] = serialize($obj_cart);
                    header('Location: ' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0]);
                    break;

                case 'decrease_item':
                    $obj_cart->decrease_item($navigateEnd);
                    $_SESSION['serialized_cart'] = serialize($obj_cart);
                    header('Location: ' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0]);
                    break;

                case 'delete_item':
                    $obj_cart->delete_item($navigateEnd);
                    $_SESSION['serialized_cart'] = serialize($obj_cart);
                    header('Location: ' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0]);
                    break;

                case 'step1':
                    if (!empty($_POST)) {
                        $obj_cart->submit_step1();
                    }

                    echo $obj_cart->show_cart_detail();

                    echo '<form method="post" action="" name="step1_form" id="step1_form" onsubmit="return step1_validate();">';

                    echo '<div id="dorucenie" class="left">';
                    echo '<h2>' . $cTranslator->getTranslation('Spôsob doručenia tovaru', 0) . '</h2>';

                    $delivery_types = $obj_cart->get_delivery_types();
                    foreach ($delivery_types as $delivery) {
                        if (!empty($_SESSION['doprava'])) {
                            if ($_SESSION['doprava'] == $delivery['delivery_type_id']) {
                                $checked = ' checked="checked"';
                            } else {
                                $checked = '';
                            }
                        } else {
                            if ($delivery['default_choice'] == 1) {
                                $checked = ' checked="checked"';
                            } else {
                                $checked = '';
                            }
                        }

                        echo '<label><input type="radio" name="doprava" value="' . $delivery['delivery_type_id'] . '"' . $checked . ' rel="' . $delivery['payment'] . '">&nbsp;' . $delivery['name'] . ' <small>(' . $delivery['price_eur'] . ' €)</small></label>';
                    }

                    echo '</div>';

                    echo '<div id="platba" class="left">';
                    echo '<h2>' . $cTranslator->getTranslation('Spôsob platby', 0) . '</h2>';

                    $payment_types = Cart::get_payment_types();
                    foreach ($payment_types as $payment) {
                        if (!empty($_SESSION['platba'])) {
                            if ($_SESSION['platba'] == $payment->payment_type_id) {
                                $checked = 'checked="checked"';
                            } else {
                                $checked = '';
                            }
                        } else {
                            if ($payment->default_choice == 1) {
                                $checked = 'checked="checked"';
                            } else {
                                $checked = '';
                            }
                        }

                        echo '<label id="payment_' . $payment->payment_type_id . '"><input type="radio" name="platba" value="' . $payment->payment_type_id . '" ' . $checked . '>&nbsp;' . $payment->name . '<br />';
                        if (!empty($payment->description)) {
                            echo '<small>' . nl2br($payment->description) . '</small>';
                        }
                        echo '</label>';
                    }

                    echo '</div>';
                    echo '<div class="both"></div>';

                    if ($user->isAuthenticated()) {
                        $query_user = 'SELECT * FROM ' . TABLE_PREFIX . 'user JOIN ' . TABLE_PREFIX . 'user_address_book USING(user_id) WHERE user_id = ' . $_SESSION['user_id'];
                        if ($result_user = mysql_query($query_user)) {
                            $row_user = mysql_fetch_object($result_user);
                        }
                    }

                    echo '<div class="page-title step-1">';
                    echo '<h2>' . $cTranslator->getTranslation('Objednávateľ', 0) . '</h2>';
                    echo '</div>';

                    echo '<table class="step1">';
                    echo '<tr><th colspan="2">' . $cTranslator->getTranslation('Fakturačná adresa', 0) . '</th></tr>';
                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Meno', 0) . ': <span>*</span></td><td><input type="text" name="fname" id="fname" value="' . (isset($row_user->fname) ? $row_user->fname : $_SESSION['fname']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Priezvisko', 0) . ': <span>*</span></td><td><input type="text" name="lname" id="lname" value="' . (isset($row_user->lname) ? $row_user->lname : $_SESSION['lname']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Adresa', 0) . ': <span>*</span></td><td><input type="text" name="address1" id="address1" value="' . (isset($row_user->address1) ? $row_user->address1 : $_SESSION['address1']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Mesto', 0) . ': <span>*</span></td><td><input type="text" name="city1" id="city1" value="' . (isset($row_user->city1) ? $row_user->city1 : $_SESSION['city1']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('PSČ', 0) . ': <span>*</span></td><td><input type="text" name="psc1" id="psc1" value="' . (isset($row_user->psc1) ? $row_user->psc1 : $_SESSION['psc1']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Štát', 0) . ': <span>*</span></td><td><input type="text" name="state1" id="state1" value="' . (isset($row_user->state1) ? $row_user->state1 : $_SESSION['state1']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Telefon', 0) . ': <span>*</span></td><td><input type="text" name="phone" id="phone" value="' . (isset($row_user->phone) ? $row_user->phone : $_SESSION['phone']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('E-mail', 0) . ': <span>*</span></td><td><input type="text" name="mail" id="mail" value="' . (isset($row_user->mail) ? $row_user->mail : $_SESSION['mail']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Názov spoločnosti', 0) . ':</td><td><input type="text" name="cname" id="cname" value="' . (isset($row_user->cname) ? $row_user->cname : $_SESSION['cname']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('IČO', 0) . ':</td><td><input type="text" name="ico" id="ico" value="' . (isset($row_user->ico) ? $row_user->ico : $_SESSION['ico']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('DIČ', 0) . ':</td><td><input type="text" name="dic" id="dic" value="' . (isset($row_user->dic) ? $row_user->dic : $_SESSION['dic']) . '" /></td></tr>';
                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><th colspan="2">' . $cTranslator->getTranslation('Adresa doručenia', 0) . '</th></tr>';
                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Adresa', 0) . ': </td><td><input type="text" name="address2" id="address2" value="' . (isset($row_user->address2) ? $row_user->address2 : $_SESSION['address2']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Štát', 0) . ': </td><td><input type="text" name="state2" id="state2" value="' . (isset($row_user->state2) ? $row_user->state2 : $_SESSION['state2']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Mesto', 0) . ': </td><td><input type="text" name="city2" id="city2" value="' . (isset($row_user->city2) ? $row_user->city2 : $_SESSION['city2']) . '" /></td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('PSČ', 0) . ': </td><td><input type="text" name="psc2" id="psc2" value="' . (isset($row_user->psc2) ? $row_user->psc2 : $_SESSION['psc2']) . '" /></td></tr>';
                    echo '<tr><td colspan="2">' . $cTranslator->getTranslation('* povinne udaje', 0) . '</td></tr>';
                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td colspan="2"><label><input type="checkbox" name="newsletter"' . (isset($row_user->newsletter) ? ' checked=""' : '') . ' />&nbsp;' . $cTranslator->getTranslation('Informujte ma o novinkách na stránke prostredníctvom emailu') . '</label></td></tr>';
                    echo '<tr><td colspan="2"><label><input type="checkbox" id="readTerms" name="readTerms" />&nbsp;' . $cTranslator->getTranslation('Týmto potvrdzujem, že som sa pri vytvorení konta oboznámil so spôsobom a podmienkami spracovania mojich osobných údajov na internetovom obchode') . '</label></td></tr>';
                    echo '<tr><td colspan="2"><a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(32) . '">' . $cTranslator->getTranslation('Obchodne podmienky', 0) . '</a></td></tr>';
                    echo '<tr><td colspan="2"><input type="submit" name="submit" class="right" value="' . $cTranslator->getTranslation('Objednať', 0) . '" /></td></tr>';
                    echo '</table>';
                    echo '</form>';
                    break;

                case 'step2':
                    echo $obj_cart->show_cart_detail();

                    echo '<table class="step1">';
                    echo '<tr><th colspan="2">' . $cTranslator->getTranslation('Fakturačná adresa', 0) . '</th></tr>';
                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Meno', 0) . ': </td><td>' . $_SESSION['fname'] . '</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Priezvisko', 0) . ': </td><td>' . $_SESSION['lname'] . '</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Adresa', 0) . ': </td><td>' . $_SESSION['address1'] . '</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Mesto', 0) . ': </td><td>' . $_SESSION['city1'] . '</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('PSČ', 0) . ': </td><td>' . $_SESSION['psc1'] . '</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Štát', 0) . ': </td><td>' . $_SESSION['state1'] . '</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('Telefón', 0) . ': </td><td>' . $_SESSION['phone'] . '</td></tr>';
                    echo '<tr><td>' . $cTranslator->getTranslation('E-mail', 0) . ': </td><td>' . $_SESSION['mail'] . '</td></tr>';
                    if (!empty($_SESSION['cname']))
                        echo '<tr><td>' . $cTranslator->getTranslation('Názov spoločnosti', 0) . ':</td><td>' . $_SESSION['cname'] . '</td></tr>';
                    if (!empty($_SESSION['ico']))
                        echo '<tr><td>' . $cTranslator->getTranslation('IČO', 0) . ':</td><td>' . $_SESSION['ico'] . '</td></tr>';
                    if (!empty($_SESSION['dic']))
                        echo '<tr><td>' . $cTranslator->getTranslation('DIČ', 0) . ':</td><td>' . $_SESSION['dic'] . '</td></tr>';
                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    echo '<tr><td colspan="2">&nbsp;</td></tr>';
                    if (!empty($_SESSION['address2']) AND ! empty($_SESSION['state2']) AND ! empty($_SESSION['city2']) AND ! empty($_SESSION['psc2'])) {
                        echo '<tr><th colspan="2">' . $cTranslator->getTranslation('Adresa doručenia', 0) . '</th></tr>';
                        echo '<tr><td colspan="2">&nbsp;</td></tr>';
                        echo '<tr><td>' . $cTranslator->getTranslation('Adresa', 0) . ': </td><td>' . $_SESSION['address2'] . '</td></tr>';
                        echo '<tr><td>' . $cTranslator->getTranslation('Štát', 0) . ': </td><td>' . $_SESSION['state2'] . '</td></tr>';
                        echo '<tr><td>' . $cTranslator->getTranslation('Mesto', 0) . ': </td><td>' . $_SESSION['city2'] . '</td></tr>';
                        echo '<tr><td>' . $cTranslator->getTranslation('PSČ', 0) . ': </td><td>' . $_SESSION['psc2'] . '</td></tr>';
                    }
                    echo '</table>';

                    $payment_action_query = 'SELECT payment_action FROM ' . TABLE_PREFIX . 'payment_type WHERE payment_type_id = ' . $_SESSION['platba'];
                    if ($payment_action_result = mysql_query($payment_action_query)) {
                        while ($payment_action_row = mysql_fetch_object($payment_action_result)) {
                            $_SESSION['platba_action'] = $payment_action_row->payment_action;

                            echo '<button type="button" title="' . $cTranslator->getTranslation('Zaplatiť', 0) . '" class="button btn-proceed-checkout btn-checkout right" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '/step3\';">' . $cTranslator->getTranslation('Zaplatiť', 0) . '</button>';
                        }
                    }
                    echo '<button type="button" title="' . $cTranslator->getTranslation('Vrátiť sa späť', 0) . '" class="button btn-proceed-checkout btn-checkout" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '/step1\';">' . $cTranslator->getTranslation('Vrátiť sa späť', 0) . '</button>';
                    //echo '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '/step1">' . $cTranslator->getTranslation('Vrátiť sa späť') . '</a>';

                    break;

                case 'step3':
                    switch ($_SESSION['platba_action']) {
                        case 'credit':
                            echo getContentByLabel('krok 3 - platba');

                            $obj_cart->submit_step2();
                            break;

                        case 'cash':
                            echo getContentByLabel('krok 3 - podakovanie za objednavku');

                            $obj_cart->submit_step2();
                            break;
                    }
                    break;

                default:
                    echo $obj_cart->show_cart_detail();

                    echo '<div class="both"></div><br/>';

                    echo '<button type="button" title="' . $cTranslator->getTranslation('Pokračovať k pokladni', 0) . '" class="button btn-proceed-checkout btn-checkout right" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '/kosik/step1\';">' . $cTranslator->getTranslation('Pokračovať k pokladni', 0) . '</button>';
                    echo '<button type="button" title="' . $cTranslator->getTranslation('Pokračovať v nákupe', 0) . '" class="button btn-proceed-checkout btn-checkout" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '\';">' . $cTranslator->getTranslation('Pokračovať v nákupe', 0) . '</button>';
                    echo '<div class="both"></div>';

                    break;
            }
        }

        break;
    case "produkt":

        $obj_product = new Product;
        $obj_product->set_dph_price_visibility(true);
        $rowP = $obj_product->get_product();

        $manufacturer = Product::get_manufacturer($rowP->manufacturer_id);
        $photogallery = $obj_product->get_product_photogallery();

        // PRED KAZDYM POUZITIM TREBA NAJPRV OBJEKT UNSERIALIZOVAT ZO SESSION
        // A PO ZMENE OBSAHU OPAT SERIALIZOVAT
        // KOSIK SI VEZME Z NASTAVENIA PRODUKTOV NASTAVENIE O ZOBRAZOVANI DPH
        $obj_cart = unserialize($_SESSION['serialized_cart']);
//        $obj_cart->set_dph_price_visibility($obj_product->get_dph_price_visibility());

        if (isset($_POST['product_id']) AND isset($_POST['price_item']) AND isset($_POST['amount'])) {
            $obj_cart->insert_item($_POST['product_id'], $_POST['color'], $_POST['amount'], $_POST['price_item']);
        }


        $_SESSION['serialized_cart'] = serialize($obj_cart);
        $obj_cart = unserialize($_SESSION['serialized_cart']);

        if (isset($_POST['product_id']) AND isset($_POST['price_item']) AND isset($_POST['amount'])) {
            header("refresh:0;url=" . ROOTDIR . '/' . $_GET['param']);
            exit;
        }

        $Row['sk_name'] = $rowP->name;

        echo '<div class="page-title list">';
        echo '<h1>' . $rowP->name . '</h1>';
        echo '</div>';
        echo '<div class="clear"></div>';
        ?>
        <span class="code"><?= $cTranslator->getTranslation('Kód tovaru:', 0); ?> <?= $rowP->code_1; ?></span>
        <div class="clear"></div>
        <div id="product_detail_description" class="left">
            <?= strip_tags($rowP->description, '<a>,<p>,<div>,<strong>,<table>,<span>,<br>,<br />'); ?>
        </div>
        <div id="product_detail_picture" class="right">
            <div class="img-container-outer">
                <?
                if (is_file("photos/preview/" . $rowP->image_src)) {
                    echo '<a href="photos/original/' . $rowP->image_src . '" class="product_detail_picture_picture" rel="gallery" title="' . $rowP->name . '">
			                	<img src="photos/thumbnail/' . $rowP->image_src . '" alt="' . $rowP->image_src . '" align="right"/>
			                </a>';
                } else {
                    print '<center>' . $cTranslator->getTranslation('Žiaden obrázok', 0) . '</center>';
                }
                ?>
            </div>
            <div class="both"></div>
            <?
            /*
              if ($user->isAuthenticated()) {
              if ($rowP->price_old > 0) {
              echo '<p class="price pwhv">' . $rowP->price_discount . ' &euro; bez DPH <span style="text-decoration:line-through;">' . $rowP->price_old . ' &euro;</span></p>';
              } else {
              echo '<p class="price pwhv">' . $rowP->price_discount . ' &euro; bez DPH</p>';
              }
              if ($obj_product->get_dph_price_visibility()) {
              if ($rowP->price_old > 0) {
              echo '<p class="price pwv">' . $rowP->price_discount_sdph . ' &euro; s DPH <span style="text-decoration:line-through;">' . $rowP->price_old . ' &euro;</span></p>';
              } else {
              echo '<p class="price pwv">' . $rowP->price_discount_sdph . ' &euro; s DPH</p>';
              }
              }
              } else {
             */
            //
            if ($rowP->price_old > 0) {
                echo '<p class="price pwhv">' . number_format($rowP->price, 2, '.', ' ') . ' &euro; bez DPH <span style="text-decoration:line-through;">' . number_format($rowP->price_old, 2, '.', ' ') . ' &euro;</span></p>';
            } else {
                echo '<p class="price pwhv">' . number_format($rowP->price, 2, '.', ' ') . ' &euro; bez DPH</p>';
            }
            if ($obj_product->get_dph_price_visibility()) {
                if ($rowP->price_old > 0) {
                    echo '<p class="price pwv">' . number_format(($rowP->price * VAT_COEFFICIENT), 2, '.', ' ') . ' &euro; s DPH <span style="text-decoration:line-through;">' . number_format(($rowP->price_old * VAT_COEFFICIENT), 2, '.', ' ') . ' &euro;</span></p>';
                } else {
                    echo '<p class="price pwv">' . number_format(($rowP->price * VAT_COEFFICIENT), 2, '.', ' ') . ' &euro; s DPH</p>';
                }
            }
            //}
            ?>
            <div class="both"></div>
            <?
            //if ($user->isAuthenticated()) {
            ?>
            <form method="post" class="product" style="overflow: hidden">
                <div id="amount_input">
                    <input name="product_id" type="hidden" value="<?= $navigateEnd; ?>" />
                    <input type="hidden" name="price_item" value="<?= ($obj_product->get_dph_price_visibility() ? number_format(($rowP->price * VAT_COEFFICIENT), 2, '.', ' ') : number_format($rowP->price, 2, '.', ' ')); ?>" />
                    <?
                    $colors = Product::get_product_colors($navigateEnd);
                    if (!empty($colors)) {
                        if ($colors[0]->univerzal == 0)
                            echo '<label for="color">' . $cTranslator->getTranslation('Vyberte si farbu:', 0) . '</label><select name="color">';
                        else
                            echo '<label for="color" class="hidden">' . $cTranslator->getTranslation('Vyberte si farbu:', 0) . '</label><select  class="hidden" name="color">';

                        foreach ($colors as $color) {
                            if ($color->univerzal == 1)
                                echo '<option value="' . $color->color_id . '">' . $cTranslator->getTranslation('Základná farba', 0) . '</option>';
                            else
                                echo '<option value="' . $color->color_id . '">' . $color->name . '</option>';
                        }
                    }
                    ?>
                    </select>
                    <label for="amount"><?= $cTranslator->getTranslation('Počet kusov:', 0); ?></label><input type="text" name="amount" value="1" />
                    <input class="add-to-cart" type="submit" title="<?= $cTranslator->getTranslation("Kúpiť", 0) ?>" value="<?= $cTranslator->getTranslation("Kúpiť", 0) ?>" />
                    <div class="both"></div>
                </div>
            </form>
            <div class="both"></div>
            <?
            //}
            ?>
        </div>
        <div class="both"></div>

        <div id="product_detail_photogallery">
            <?
            if (count($photogallery) > 0) {
                echo '<h2>' . $cTranslator->getTranslation("Fotogaleria produktu:", 0) . '</h2>';
                echo '<div id="fotogaleria left">';
                $i = 1;
                if (!empty($photogallery)) {
                    foreach ($photogallery as $rowPG) {
                        echo '<div class="foto left"><a href="photos/original/' . $rowPG->src . '" rel="gallery" title="' . $rowP->name . '"><img src="photos/thumbnail/' . $rowPG->src . '" alt="" class="product_detail_picture_picture img-polaroid" /></a></div>';
                        if ($i % 4 == 0)
                            echo '<div class="both"></div>';
                        $i++;
                    }
                }
                echo '</div>';
                echo '<div class="both"></div>';
            }
            ?>
            <div class="both"></div>
        </div>
        <?
        break;
    default:
        // 	VYTVORENIE objektu katalogu a generovanie katalogu
        $obj_catalogue = new Catalogue;
        if (isset($_POST['submit-sorting'])) {
            $_SESSION['userPrefs']['productsOnPage'] = $_POST['catalogue_limit'];
            $_SESSION['userPrefs']['orderBy'] = $_POST['sort_by'];
            $_SESSION['userPrefs']['orderSort'] = $_POST['dir'];
        }
        if (!isset($_SESSION['userPrefs']['productsOnPage'])) {
            $_SESSION['userPrefs']['productsOnPage'] = '15';
        }
        $obj_catalogue->set_catalogue_limit($_SESSION['userPrefs']['productsOnPage']);

        if (!isset($_SESSION['userPrefs']['orderBy'])) {
            $_SESSION['userPrefs']['orderBy'] = 'name';
        }
        if (!isset($_SESSION['userPrefs']['orderBy'])) {
            $_SESSION['userPrefs']['orderSort'] = 'ASC';
        }

        $obj_catalogue->set_catalogue_order($_SESSION['userPrefs']['orderBy'] . ' ' . $_SESSION['userPrefs']['orderSort']);
        $obj_catalogue->set_catalogue_menu_id();
        $obj_catalogue->set_dph_price_visibility(true);

        $obj_catalogue->submit_search_form();

        $catalogue = $obj_catalogue->get_catalogue();
        // - //
        // PRED KAZDYM POUZITIM TREBA NAJPRV OBJEKT UNSERIALIZOVAT ZO SESSION
        // A PO ZMENE OBSAHU OPAT SERIALIZOVAT
        $obj_cart = unserialize($_SESSION['serialized_cart']);
        //$obj_cart->set_dph_price_visibility($obj_catalogue->get_dph_price_visibility());
        /*
          if (isset($_POST)) {
          $obj_cart->insert_item($_POST['product_id'], $_POST['color'], $_POST['amount'], $_POST['price_item']);
          }
         */
        $_SESSION['serialized_cart'] = serialize($obj_cart);
        $obj_cart = unserialize($_SESSION['serialized_cart']);

        $obj_paginator = new Paginator;

        $obj_paginator->set_items_per_page($obj_catalogue->get_catalogue_limit());                                 //  pocet zobrazenych poloziek na 1 stranke
        $obj_paginator->set_items_count($obj_catalogue->get_catalogue_items_count());                                   //  pocet poloziek v databaze

        $obj_paginator->set_params_base(Menu::getHyperLinkById($navigateId));   //  base... to ani netreba menit
        $obj_paginator->set_params($navigateArrayUrlWithoutBase);               //  ani toto nie je treba menit... maximalne k tomu pripojit dalsie parametre, ak treba

        echo '<div class="page-title list">';
        echo '<h1>' . $obj_catalogue->get_category_name() . '</h1>';
        echo $obj_catalogue->generate_search_form();
        echo '</div>';
        echo '<div class="clear"></div>';
        ?>

        <!-- generovanie SORTING FORMULARA -->
        <?= $obj_catalogue->generate_sorting_form() ?>
        <div id="catalogue">
            <?
            $i = 0;
            foreach ($catalogue as $rowC) {
                $i++;
                ?>
                <!--<form method="post" enctype="multipart/form-data" action="" onsubmit="return kosikCheck(this);" class="product" style="overflow: hidden">-->
                <div class="product_tile left<?= (($i % 3 == 0) ? ' last' : ''); ?>">
                    <h2><a href="<?= ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) ?>/produkt/<?= String::SEOFriendlyText($rowC->name) . '/' . $rowC->product_id ?>" title="<?= $rowC->name ?>"><?= $rowC->name; ?></a></h2>
                    <?
                    $manufacturer = Product::get_manufacturer($rowC->manufacturer_id);
                    foreach ($manufacturer as $rowM) {
                        if (is_file("photos/images/manufacturers/" . $rowM->logo))
                            echo '<img src="photos/images/manufacturers/' . $rowM->logo . '" alt="' . $rowM->name . '" class="manu_logo" />';
                    }
                    ?>
                    <div class="img-container-outer">
                        <a class="img-container" href="<?= ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) ?>/produkt/<?= String::SEOFriendlyText($rowC->name) . '/' . $rowC->product_id ?>" title="<?= $rowC->name ?>">
                            <? if (is_file("./photos/preview/" . $rowC->image_src)) { ?>
                                <img src="photos/thumbnail/<?= $rowC->image_src; ?>" alt="<?= $rowC->name; ?>" class="product_photo left" /><?
                            } else {
                                print '<center>' . $cTranslator->getTranslation('Žiaden obrázok', 0) . '</center>';
                            }
                            ?>
                        </a>
                    </div>
                    <div class="both"></div>
                    <?
                    //if ($user->isAuthenticated()) {
                    if ($rowC->price_old > 0) {
                        echo '<p class="price whv' . ($obj_catalogue->get_dph_price_visibility() ? ' first' : '') . '">' . number_format($rowC->price, 2, '.', '') . ' &euro; bez DPH <span style="text-decoration:line-through;">' . $rowC->price_old . ' &euro;</span></p>';
                    } else {
                        echo '<p class="price whv' . ($obj_catalogue->get_dph_price_visibility() ? ' first' : '') . '">' . number_format($rowC->price, 2, '.', '') . ' &euro; bez DPH</p>';
                    }
                    echo '<p class="go-to-detail"><a href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/produkt/' . String::SEOFriendlyText($rowC->name) . '/' . $rowC->product_id . '">' . $cTranslator->getTranslation('Detail', 0) . '</a></p>';
                    if ($obj_catalogue->get_dph_price_visibility()) {
                        if ($rowC->price_old > 0) {
                            echo '<p class="price wv">' . number_format(($rowC->price * VAT_COEFFICIENT), 2, '.', '') . ' &euro; s DPH <span style="text-decoration:line-through;">' . $rowC->price_old . ' &euro;</span></p>';
                        } else {
                            echo '<p class="price wv">' . number_format(($rowC->price * VAT_COEFFICIENT), 2, '.', '') . ' &euro; s DPH</p>';
                        }
                    }
                    //}
                    ?>
                    <div class="both"></div>
                </div>
                <!--</form>-->
                <?
            }
            echo '<div class="clear"></div>';
            //echo $obj_catalogue->catalogue_paginator();
            echo $obj_paginator->get_paginator();
            ?>
        </div>
        <?
        break;
}


$moduleContent = ob_get_contents();
ob_clean();
?>