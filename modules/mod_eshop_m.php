<?
if($_GET['filter']) {
    $_SESSION['userPrefs']['filter'] = '';
    $_SESSION['base']['filter'] = $_GET['filter'];
    header('Location: ' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY));
    exit;
}
if($navigateId != ESHOP_MAIN_CATEGORY OR isset($_POST['submit-sorting'])) {
    unset($_SESSION['base']['filter']);
}

if(!is_array($_SESSION['userPrefs']['filter'])) {
    $filter[] = $_SESSION['userPrefs']['filter'];
    $_SESSION['userPrefs']['filter'] = $filter;
}
/*else
    $_SESSION['userPrefs']['filter'] = 'none'; 
*/
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="' . fileWithLastChange('css/mod_eshop.css') . '" />';
$_js_file = '<script type="text/javascript" src="js/form-validator.js"></script>' . n;
//$js_file = '<script type="text/javascript" src="js/mod_step1_validation.js"></script>';
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
switch ($navigateArrayUrlWithoutBase[0]) {
    case "cart":
    case "kosik":
        ?>
        <div class="container">
        <?
        if (isset($_GET['tps'])) {
            if (is_numeric($_GET['REF']) AND is_numeric($_GET['RES'])) {
                $result = mysql_query('SELECT o.order_id, o.user_id, o.price_total, o.payment_type, u.mail, u.username, u.fullname, d.price_eur AS delivery_price, p.price_eur AS payment_price FROM ' . TABLE_PREFIX . 'order AS o
                                       LEFT JOIN ' . TABLE_PREFIX . 'user AS u USING(user_id)
                                       LEFT JOIN ' . TABLE_PREFIX . 'delivery_type AS d ON o.delivery_type=d.delivery_type_id
                                       LEFT JOIN ' . TABLE_PREFIX . 'payment_type AS p ON o.payment_type=p.payment_type_id
                                       WHERE LPAD(o.order_id,8,0)="' . mysql_real_escape_string($_GET['REF']) . '";');
                if (mysql_num_rows($result) == '0') {
                    header('Location: ' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '?tps=no-data&REF=' . $_GET['REF'] . '&RES=' . $_GET['RES']);
                    exit;
                }
                $or = mysql_fetch_object($result);
                $total_price = $or->price_total + $or->delivery_price + $or->payment_price;
                $reference = str_pad($or->order_id, 8, '0', STR_PAD_LEFT);
                $result = mysql_query('UPDATE ' . TABLE_PREFIX . 'order SET res="' . mysql_real_escape_string($_GET['RES']) . '" WHERE LPAD(order_id,8,0)="' . mysql_real_escape_string($_GET['REF']) . '";');
                switch ($_GET['tps']) {
                    case 'success':
                        echo getContentByLabel('Trustpay - platba prebehla úspešne');
                        break;
                    case 'cancel':

                        if ($_GET['REF'] != $reference) {
                            header('Location: ' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '?tps=no-data&REF=' . $_GET['REF'] . '&RES=' . $_GET['RES']);
                            exit;
                        }

                        if ($or->payment_type == TRUSTPAY_TRANSFER_PAYMENT_ID) {
                            $message = getContentByLabel('Trustpay prevod - platba bola zrušená');
                        } elseif ($or->payment_type == TRUSTPAY_CREDIT_PAYMENT_ID) {
                            $message = getContentByLabel('Trustpay kredit - platba bola zrušená');
                        }
                        $search_tags = array('{trustpay_transfer_link}', '{trustpay_credit_link}', '{trustpay_transfer_logo}', '{trustpay_credit_logo}', '{send_link}');
                        $replace_tags = array(
                            '<a class="button-trustpay" href="' . trustPayTransferLink($total_price, $reference) . '">' . $cTranslator->getTranslation('Prejsť na TrustPay', 0) . '</a>',
                            '<a class="button-trustpay" href="' . trustPayCreditLink($total_price, $reference) . '">' . $cTranslator->getTranslation('Prejsť na TrustPay', 0) . '</a>',
                            '<a class="trustpay-logo" href="' . trustPayTransferLink($total_price, $reference) . '"></a>',
                            '<a class="trustpay-logo" href="' . trustPayCreditLink($total_price, $reference) . '"></a>',
                            '<strong><a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '?tps=send-mail&REF=' . $_GET['REF'] . '&RES=' . $_GET['RES'] . '&PID=' . $_GET['PID'] . '">' . $cTranslator->getTranslation('Odoslať odkaz na zaplatenie na môj e-mail', 0) . '</a></strong>');
                        $message = str_replace($search_tags, $replace_tags, $message);
                        echo $message;
                        break;
                    case 'error':

                        if ($_GET['REF'] != $reference) {
                            header('Location: ' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '?tps=no-data');
                            exit;
                        }

                        if ($or->payment_type == TRUSTPAY_TRANSFER_PAYMENT_ID) {
                            $message = getContentByLabel('Trustpay prevod - platba neprebehla úspešne');
                        } elseif ($or->payment_type == TRUSTPAY_CREDIT_PAYMENT_ID) {
                            $message = getContentByLabel('Trustpay kredit - platba neprebehla úspešne');
                        }
                        $search_tags = array('{trustpay_transfer_link}', '{trustpay_credit_link}', '{trustpay_transfer_logo}', '{trustpay_credit_logo}', '{send_link}');
                        $replace_tags = array(
                            '<a class="button-trustpay" href="' . trustPayTransferLink($total_price, $reference) . '">' . $cTranslator->getTranslation('Prejsť na TrustPay', 0) . '</a>',
                            '<a class="button-trustpay" href="' . trustPayCreditLink($total_price, $reference) . '">' . $cTranslator->getTranslation('Prejsť na TrustPay', 0) . '</a>',
                            '<a class="trustpay-logo" href="' . trustPayTransferLink($total_price, $reference) . '"></a>',
                            '<a class="trustpay-logo" href="' . trustPayCreditLink($total_price, $reference) . '"></a>',
                            '<strong><a href="' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '?tps=send-mail&REF=' . $_GET['REF'] . '&RES=' . $_GET['RES'] . '&PID=' . $_GET['PID'] . '">' . $cTranslator->getTranslation('Odoslať odkaz na zaplatenie na môj e-mail', 0) . '</a></strong>');
                        $message = str_replace($search_tags, $replace_tags, $message);
                        echo $message;
                        break;
                    case 'send-mail':
                        $subject = $cTranslator->getTranslation("Objednávka #", 0) . ' ' . $_GET['REF'] . ' - ' . $cTranslator->getTranslation("odkaz na platobnú bránu", 0) . ' ' . PROJECT_TITLE;
                        $search_tags = array(
                            '{surname}',
                            '{order_number}',
                            '{order_status}',
                            '{project_title}',
                            '{trustpay_transfer_link}',
                            '{trustpay_credit_link}'
                        );
                        $replace_tags = array(
                            $row['priezvisko'],
                            '#' . $reference,
                            $status,
                            PROJECT_TITLE,
                            '<a class="button-trustpay" href="' . trustPayTransferLink(($or->price_total +$or->delivery_price + $or->payment_price), str_pad($or->order_id, 8, '0', STR_PAD_LEFT)) . '">' . $cTranslator->getTranslation('Prejsť na TrustPay a zaplatiť', 0) . '</a>',
                            '<a class="button-trustpay" href="' . trustPayCreditLink(($or->price_total + $or->delivery_price + $or->payment_price), str_pad($or->order_id, 8, '0', STR_PAD_LEFT)) . '">' . $cTranslator->getTranslation('Prejsť na TrustPay a zaplatiť', 0) . '</a>'
                        );
                        $body = str_replace($search_tags, $replace_tags, getContentByLabel('Trustpay - e-mail s linkom', 0));


                        if ($or->payment_type == TRUSTPAY_TRANSFER_PAYMENT_ID) {
                            $body = str_replace($search_tags, $replace_tags, getContentByLabel('Trustpay prevod - e-mail s linkom', 0));
                        } elseif ($or->payment_type == TRUSTPAY_CREDIT_PAYMENT_ID) {
                            $body = str_replace($search_tags, $replace_tags, getContentByLabel('Trustpay kredit - e-mail s linkom', 0));
                        }


                        require_once('shared/classes/class.send_mail.php');
                        $sendMail = new sendMail();

                        $success_message = $cTranslator->getTranslation("Odkaz na zaplatenie bol odoslaný na e-mail uvedený v objednávke.", 0);
                        $error_message = $cTranslator->getTranslation("Nastala chyba! Odkaz na zaplatenie objednávky nebol odoslaný. Prosím skúste znova.", 0);
                        $response = array('subject' => $subject, 'body' => $body, 'success_message' => $success_message, 'error_message' => $error_message);
                        $sendMail->send($or->fullname, (!empty($or->mail) ? $or->mail : $or->username), NULL, NULL, NULL, NULL, NULL, $response);
                        header('Location: ' . ROOTDIR);
                        exit;
                        break;
                    case 'no-data':
                    default:
                        echo getContentByLabel('Trustpay - žiadne dáta pri návrate');
                        break;
                }
            } else {
                Message::setMessage($cTranslator->getTranslation('Nesprávna adresa! Požiadavka nemôže byť dokončená.', 0), 2);
                header('Location: ' . ROOTDIR);
                break;
            }
        } elseif ($products_count == '0') {
            //echo '<h1 class="cart">' . $cTranslator->getTranslation('Nákupný košík je prázdny', 0) . '</h1>';
            echo '<h1>' . $cTranslator->getTranslation('Nákupný košík je prázdny', 0) . '</h1>';

            echo '<div class="cart-empty">';
            echo '<p>' . $cTranslator->getTranslation('Vo Vašom košíku nie sú žiadne položky.', 0) . '</p>';
            echo '<p>' . $cTranslator->getTranslation('Kliknite', 0) . ' <a href="' . ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '">' . $cTranslator->getTranslation('sem', 0) . '</a> ' . $cTranslator->getTranslation('pre pokračovanie v nákupe.', 0) . '</p>';
            echo '</div>';
        } else {
            echo '<h1>' . $cTranslator->getTranslation('Nákupný košík', 0) . '</h1>';
            // PRED KAZDYM POUZITIM TREBA NAJPRV OBJEKT UNSERIALIZOVAT ZO SESSION
            // A PO ZMENE OBSAHU OPAT SERIALIZOVAT
            $obj_cart = unserialize($_SESSION['serialized_cart']);
            $obj_cart->set_dph_price_visibility(VAT_VISIBILITY);

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
                    ?>
                    <form method="post" action="" name="step1-form" id="step1-form" class="form-horizontal">
                        <?
                        echo '<div class="row">';
                            echo '<div id="delivery" class="col-md-6">';
                                echo '<h2>' . $cTranslator->getTranslation('Spôsob doručenia tovaru', 0) . '</h2>';
                                echo '<fieldset>';

                                $delivery_types = $obj_cart->get_delivery_types();
                                $delivery_count = count($delivery_types);
                                foreach ($delivery_types as $delivery) {
                                    if (!empty($_SESSION['doprava'])) {
                                        if ($_SESSION['doprava'] == $delivery['delivery_type_id'] OR $delivery['default_choice'] == 1 OR $delivery_count == '1') {
                                            $checked = ' checked="checked"';
                                        } else {
                                            $checked = '';
                                        }
                                    } else {
                                        if ($delivery['default_choice'] == 1 OR $delivery_count == 1) {
                                            $checked = ' checked="checked"';
                                        } else {
                                            $checked = '';
                                        }
                                    }
                                    echo '<div class="radio col-md-offset-3">';
                                      echo '<label>';
                                        echo '<input type="radio" name="doprava" value="' . $delivery['delivery_type_id'] . '" ' . $checked . ' rel="' . $delivery['payment'] . '">
                                                &nbsp;' . $delivery['name'] . ' <small>(' . $delivery['price_eur'] . ' €)</small>';
                                      echo '</label>';
                                    echo '</div>';
                                }
                                if ($delivery_count == '1') {
                                    echo '<input class="hidden" type="radio" name="doprava" value="0" rel="0" />';
                                }
                        /*
                                if ($delivery_count == '1') {                                    
                                    echo '<div class="radio col-md-offset-3">';
                                      echo '<label>';
                                        echo '<input type="radio" name="doprava" value="0" rel="0" checked="checked" disabled>
                                                &nbsp;' . $delivery['name'] . ' <small>(' . $delivery['price_eur'] . ' €)</small>';
                                      echo '</label>';
                                    echo '</div>';
                                    //echo '<input class="hidden" type="radio" name="doprava" value="0" rel="0" />';
                                }*/
                                echo '</fieldset>';

                            echo '</div>';

                            echo '<div id="payment" class="col-md-6">';
                                echo '<h2>' . $cTranslator->getTranslation('Spôsob platby', 0) . '</h2>';
                                echo '<fieldset>';

                                $payment_types = Cart::get_payment_types();
                                $payment_count = count($payment_types);
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
                                    echo '<div class="radio col-md-offset-3">';
                                      echo '<label id="payment_' . $payment->payment_type_id . '">';
                                        echo '<input type="radio" name="platba" value="' . $payment->payment_type_id . '" ' . $checked . '>
                                                &nbsp;' . $payment->name . ' <small class="price">(' . $payment->price_eur . ' €)</small>';
                                                if (!empty($payment->description)) {
                                                    echo '<small>' . nl2br($payment->description) . '</small>';
                                                }
                                      echo '</label>';
                                    echo '</div>';
                                }
                                if ($payment_count == '1') {                                    
                                    echo '<div class="radio col-md-offset-3">';
                                      echo '<label id="payment_' . $payment->payment_type_id . '">';
                                        echo '<input type="radio" name="platba" value="0"  rel="0" ' . $checked . ' checked="checked" disabled>
                                                &nbsp;' . $payment->name . ' <small class="price">(' . $payment->price_eur . ' €)</small>';
                                                if (!empty($payment->description)) {
                                                    echo '<small>' . nl2br($payment->description) . '</small>';
                                                }
                                      echo '</label>';
                                    echo '</div>';
                                }
                                echo '</fieldset>';

                            echo '</div>';
                        
                        

                        if ($user->isAuthenticated()) {
                            $query_user = 'SELECT * FROM ' . TABLE_PREFIX . 'user JOIN ' . TABLE_PREFIX . 'user_address_book USING(user_id) WHERE user_id = ' . $_SESSION['user_id'];
                            if ($result_user = mysql_query($query_user)) {
                                $row_user = mysql_fetch_object($result_user);
                            }
                        }
                        ?>
                        <div class="col-md-12">
                            <h2><?= $cTranslator->getTranslation('Objednávateľ', 0); ?></h2>
                        </div>

                        <div class="invoice-address col-md-6">
                            <fieldset>
                            <div class="form-group">
                                <h3 class="col-sm-offset-3 col-sm-9"><?= $cTranslator->getTranslation('Fakturačná adresa'); ?></h3>
                            </div>

                            <div class="form-group">
                                <label for="fname" class="col-sm-3 col-xs-12 control-label"><span class="req"><?= $cTranslator->getTranslation('Meno'); ?>: </span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="fname" id="fname" class="form-control" value="<?= (isset($row_user->fname) ? $row_user->fname : $_SESSION['fname']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="lname" class="col-sm-3 col-xs-12 control-label"><span class="req"><?= $cTranslator->getTranslation('Priezvisko'); ?>: </span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="lname" id="lname" class="form-control" value="<?= (isset($row_user->lname) ? $row_user->lname : $_SESSION['lname']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address1" class="col-sm-3 col-xs-12 control-label"><span class="req"><?= $cTranslator->getTranslation('Adresa'); ?>: </span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="address1" id="address1" class="form-control" value="<?= (isset($row_user->address1) ? $row_user->address1 : $_SESSION['address1']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="city1" class="col-sm-3 col-xs-12 control-label"><span class="req"><?= $cTranslator->getTranslation('Mesto'); ?>: </span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="city1" id="city1" class="form-control" value="<?= (isset($row_user->city1) ? $row_user->city1 : $_SESSION['city1']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="psc1" class="col-sm-3 col-xs-12 control-label"><span class="req"><?= $cTranslator->getTranslation('PSČ'); ?>: </span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="psc1" id="psc1" class="form-control" value="<?= (isset($row_user->psc1) ? $row_user->psc1 : $_SESSION['psc1']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="state1" class="col-sm-3 col-xs-12 control-label"><span class="req"><?= $cTranslator->getTranslation('Štát'); ?>: </span></label>
                                <div class="col-sm-9">
                                    <input type="text" name="state1" id="state1" class="form-control" value="<?= (isset($row_user->state1) ? $row_user->state1 : $_SESSION['state1']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-3 col-xs-12 control-label"><span class="req"><?= $cTranslator->getTranslation('Telefon'); ?>: </span></label>
                                <div class="col-sm-9">
                                    <input type="tel" name="phone" id="phone" class="form-control" value="<?= (isset($row_user->phone) ? $row_user->phone : $_SESSION['phone']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="mail" class="col-sm-3 col-xs-12 control-label"><span class="req"><?= $cTranslator->getTranslation('E-mail'); ?>: </span></label>
                                <div class="col-sm-9">
                                    <input type="email" name="mail" id="mail" class="form-control" value="<?= (isset($row_user->mail) ? $row_user->mail : $_SESSION['mail']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="cname" class="col-sm-3 col-xs-12 control-label"><?= $cTranslator->getTranslation('Názov spoločnosti'); ?>: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="cname" id="cname" class="form-control" value="<?= (isset($row_user->cname) ? $row_user->cname : $_SESSION['cname']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="ico" class="col-sm-3 col-xs-12 control-label"><?= $cTranslator->getTranslation('IČO'); ?>: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="ico" id="ico" class="form-control" value="<?= (isset($row_user->ico) ? $row_user->ico : $_SESSION['ico']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="dic" class="col-sm-3 col-xs-12 control-label"><?= $cTranslator->getTranslation('DIČ'); ?>: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="dic" id="dic" class="form-control" value="<?= (isset($row_user->dic) ? $row_user->dic : $_SESSION['dic']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="icdph" class="col-sm-3 col-xs-12 control-label"><?= $cTranslator->getTranslation('IČ DPH'); ?>: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="icdph" id="icdph" class="form-control" value="<?= (isset($row_user->icdph) ? $row_user->icdph : $_SESSION['icdph']); ?>" />
                                </div>
                            </div>
                            <div class="col-md-offset-3">
                                <span class="required-info"><?= str_replace('*', '<span class="dnt">*</span>', $cTranslator->getTranslation('Údaje označené * sú povinné.', 0)); ?></span>
                            </div>
                            </fieldset>
                        </div>
                        
                        <div class="delivery-address col-md-6">
                            <fieldset>
                            <div class="form-group">
                                <h3 class="col-sm-offset-3 col-sm-9"><?= $cTranslator->getTranslation('Adresa doručenia'); ?></h3>
                            </div>
                            <div class="form-group">
                                <label for="address2" class="col-sm-3 col-xs-12 control-label"><?= $cTranslator->getTranslation('Adresa'); ?>: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="address2" id="address2" class="form-control" value="<?= (isset($row_user->address2) ? $row_user->address2 : $_SESSION['address2']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="city2" class="col-sm-3 col-xs-12 control-label"><?= $cTranslator->getTranslation('Mesto'); ?>: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="city2" id="city2" class="form-control" value="<?= (isset($row_user->city2) ? $row_user->city2 : $_SESSION['city2']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="psc2" class="col-sm-3 col-xs-12 control-label"><?= $cTranslator->getTranslation('PSČ'); ?>: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="psc2" id="psc2" class="form-control" value="<?= (isset($row_user->psc2) ? $row_user->psc2 : $_SESSION['psc2']); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="state2" class="col-sm-3 col-xs-12 control-label"><?= $cTranslator->getTranslation('Štát'); ?>: </label>
                                <div class="col-sm-9">
                                    <input type="text" name="state2" id="state2" class="form-control" value="<?= (isset($row_user->state2) ? $row_user->state2 : $_SESSION['state2']); ?>" />
                                </div>
                            </div>
                            </fieldset>
                        </div>
                        
                        <div class=" col-md-6">
                            <fieldset>
                            <div class="form-group">
                                <label for="address2" class="col-xs-12"><?= $cTranslator->getTranslation('Poznámka'); ?>: </label>
                                <div class="col-xs-12">
                                    <textarea name="comment" class="form-control"><?= ($_SESSION['comment']); ?></textarea>
                                </div>
                            </div>
                            </fieldset>
                        </div>
                        <div class="form-footer col-md-12">
                            <fieldset>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" name="newsletter"<?= (isset($row_user->newsletter) ? ' checked="checked"' : ''); ?> value="" />
                                    <?= $cTranslator->getTranslation('Informujte ma o novinkách na stránke prostredníctvom emailu'); ?>
                                </label>
                            </div>
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" id="readTerms" name="readTerms" />
                                    <?= $cTranslator->getTranslation('Týmto potvrdzujem, že som sa pri vytvorení konta oboznámil so spôsobom a podmienkami spracovania mojich osobných údajov na internetovom obchode'); ?>
                                    <br /><a href="<?= ROOTDIR . '/' . Menu::getHyperLinkById(16); ?>"><?= Menu::getHyperLinkTextById(16); ?></a>
                                </label>
                            </div>
                            </fieldset>


                            <div class="form-group">
                                <div class="col-sm-12">
                                    <button type="submit" class="right" name="submit" ><?= $cTranslator->getTranslation('Rekapitulácia objednávky', 0); ?></button>
                                </div>
                            </div>

                        </div>
                    </div>
                    </form>
                    <script type="text/javascript">
                        var frmvalidator = new Validator("step1-form");
                        frmvalidator.addValidation("doprava", "selone_radio", "<?= $cTranslator->getTranslation('Nevybrali ste spôsob dopravy', 0); ?>");
                        frmvalidator.addValidation("platba", "selone_radio", "<?= $cTranslator->getTranslation('Nevybrali ste spôsob platby', 0); ?>");
                        frmvalidator.addValidation("fname", "req", "<?= $cTranslator->getTranslation('Nezadali ste meno', 0); ?>");
                        frmvalidator.addValidation("lname", "req", "<?= $cTranslator->getTranslation('Nezadali ste priezvisko', 0); ?>");
                        frmvalidator.addValidation("address1", "req", "<?= $cTranslator->getTranslation('Nezadali ste adresu', 0); ?>");
                        frmvalidator.addValidation("city1", "req", "<?= $cTranslator->getTranslation('Nezadali ste mesto', 0); ?>");
                        frmvalidator.addValidation("psc1", "req", "<?= $cTranslator->getTranslation('Nezadali ste PSČ', 0); ?>");
                        frmvalidator.addValidation("psc1", "minlen=5", "<?= $cTranslator->getTranslation('PSČ musí obsahovať 5 čísel', 0); ?>");
                        frmvalidator.addValidation("psc1", "maxlen=5", "<?= $cTranslator->getTranslation('PSČ musí obsahovať 5 čísel', 0); ?>");
                        frmvalidator.addValidation("psc1", "num", "<?= $cTranslator->getTranslation('PSČ môže obsahovať iba čísla', 0); ?>");
                        frmvalidator.addValidation("state1", "req", "<?= $cTranslator->getTranslation('Nezadali ste štát', 0); ?>");
                        frmvalidator.addValidation("phone", "req", "<?= $cTranslator->getTranslation('Nezadali ste telefónne číslo', 0); ?>");
                        frmvalidator.addValidation("mail", "maxlen=50", "<?= $cTranslator->getTranslation('Nezadali ste správnu dĺžku e-mailovej adresy', 0); ?>");
                        frmvalidator.addValidation("mail", "req", "<?= $cTranslator->getTranslation('Nezadali ste e-mailovú adresu', 0); ?>");
                        frmvalidator.addValidation("mail", "email", "<?= $cTranslator->getTranslation('Nezadali korektnú e-mailovú adresu', 0); ?>");
                        frmvalidator.addValidation("readTerms", "shouldselchk=1", "<?= $cTranslator->getTranslation('Pre pokračovanie musíte súhlasiť s obchodnými podmienkami', 0); ?>");
                        frmvalidator.setAddnlValidationFunction(check_zip);
                        function check_zip() {
                            var form = document.forms["step1-form"];
                            var zip = form.psc2;
                            var isnum = /^\d+$/.test(zip);
                            if (zip.value != '') {
                                if (zip.value.length != '5') {
                                    sfm_show_error_msg("<?= $cTranslator->getTranslation('PSČ musí obsahovať 5 čísel', 0); ?>", form.psc2);
                                    return false;
                                }
                                if (!$.isNumeric(zip.value)) {
                                    sfm_show_error_msg("<?= $cTranslator->getTranslation('PSČ môže obsahovať iba čísla', 0); ?>", form.psc2);
                                    return false;
                                }
                                return true;
                            } else {
                                return true;
                            }
                        }
                    </script>
                    <?
                    break;

                case 'step2':
                    echo $obj_cart->show_cart_detail();

                    echo '<div class="step2 row">';
                    $query_delivery = 'SELECT * FROM ' . TABLE_PREFIX . 'delivery_type WHERE delivery_type_id="' . mysql_real_escape_string($_SESSION['doprava']) . '"';
                    if ($result_delivery = mysql_query($query_delivery))
                        $row_delivery = mysql_fetch_object($result_delivery);

                    $query_payment = 'SELECT * FROM ' . TABLE_PREFIX . 'payment_type WHERE payment_type_id="' . mysql_real_escape_string($_SESSION['platba']) . '"';
                    if ($result_payment = mysql_query($query_payment))
                        $row_payment = mysql_fetch_object($result_payment);
                    ?>
                    <div id="delivery" class="col-md-6">
                        <h2><?= $cTranslator->getTranslation('Spôsob doručenia tovaru', 0); ?></h2>
                        <?= $row_delivery->name; ?> <small>(<?= $row_delivery->price_eur; ?> €)</small>
                    </div>

                    <div id="payment" class="col-md-6">
                        <h2><?= $cTranslator->getTranslation('Spôsob platby', 0); ?></h2>
                        <?= $row_payment->name; ?> <small>(<?= $row_payment->price_eur; ?> €)</small>
                    </div>

                    <div class="invoice-address col-md-6">
                        <h2><?= $cTranslator->getTranslation('Fakturačná adresa', 0); ?></h2>
                        <p><span class="lab"><?= $cTranslator->getTranslation('Meno', 0); ?>:</span><?= $_SESSION['fname']; ?></p>
                        <p><span class="lab"><?= $cTranslator->getTranslation('Priezvisko', 0); ?>:</span><?= $_SESSION['lname']; ?></p>
                        <p><span class="lab"><?= $cTranslator->getTranslation('Adresa', 0); ?>:</span><?= $_SESSION['address1']; ?></p>
                        <p><span class="lab"><?= $cTranslator->getTranslation('Mesto', 0); ?>:</span><?= $_SESSION['city1']; ?></p>
                        <p><span class="lab"><?= $cTranslator->getTranslation('PSČ', 0); ?>:</span><?= $_SESSION['psc1']; ?></p>
                        <p><span class="lab"><?= $cTranslator->getTranslation('Štát', 0); ?>:</span><?= $_SESSION['state1']; ?></p>
                        <p><span class="lab"><?= $cTranslator->getTranslation('Telefón', 0); ?>:</span><?= $_SESSION['phone']; ?></p>
                        <p><span class="lab"><?= $cTranslator->getTranslation('E-mail', 0); ?>:</span><?= $_SESSION['mail']; ?></p>
                        <? if (!empty($_SESSION['cname'])) {
                            ?>
                            <p><span class="lab"><?= $cTranslator->getTranslation('Názov spoločnosti', 0); ?>:</span><?= $_SESSION['cname']; ?></p>
                        <?
                        }
                        if (!empty($_SESSION['ico'])) {
                            ?>
                            <p><span class="lab"><?= $cTranslator->getTranslation('IČO', 0); ?>:</span><?= $_SESSION['ico']; ?></p>
                        <?
                        }
                        if (!empty($_SESSION['dic'])) {
                            ?>
                            <p><span class="lab"><?= $cTranslator->getTranslation('DIČ', 0); ?>:</span><?= $_SESSION['dic']; ?></p>
                            <?
                        }
                        if (!empty($_SESSION['icdph'])) {
                            ?>
                            <p><span class="lab"><?= $cTranslator->getTranslation('IČ DPH', 0); ?>:</span><?= $_SESSION['icdph']; ?></p>
                            <?
                        }
                        ?>
                    </div>
                    <div class="delivery-address col-md-6">
                    <?
                    if (!empty($_SESSION['address2']) AND ! empty($_SESSION['state2']) AND ! empty($_SESSION['city2']) AND ! empty($_SESSION['psc2'])) {
                        ?>
                        
                            <h2><?= $cTranslator->getTranslation('Adresa doručenia', 0); ?></h2>
                            <p><span class="lab"><?= $cTranslator->getTranslation('Adresa', 0); ?>:</span><?= $_SESSION['address2']; ?></p>
                            <p><span class="lab"><?= $cTranslator->getTranslation('Štát', 0); ?>:</span><?= $_SESSION['state2']; ?></p>
                            <p><span class="lab"><?= $cTranslator->getTranslation('Mesto', 0); ?>:</span><?= $_SESSION['city2']; ?></p>
                            <p><span class="lab"><?= $cTranslator->getTranslation('PSČ', 0); ?>:</span><?= $_SESSION['psc2']; ?></p>
                        
                        <?
                    }

                        if (!empty($_SESSION['comment'])) {
                            ?>
                            <h2>&nbsp;</h2>
                            <p><span class="lab"><?= $cTranslator->getTranslation('poznámka', 0); ?>: </span><?= $_SESSION['comment']; ?></p>
                            <?
                        }
                    ?>
                    </div>
                    </div>
                    <?
                    $payment_action_query = 'SELECT payment_action FROM ' . TABLE_PREFIX . 'payment_type WHERE payment_type_id = ' . $_SESSION['platba'];
                    if ($payment_action_result = mysql_query($payment_action_query)) {
                        while ($payment_action_row = mysql_fetch_object($payment_action_result)) {
                            $_SESSION['platba_action'] = $payment_action_row->payment_action;

                            echo '<button type="button" title="' . $cTranslator->getTranslation('Objednať', 0) . '" class="button right" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '/step3\';">' . $cTranslator->getTranslation('Objednať', 0) . '</button>';
                        }
                    }
                    echo '<button type="button" title="' . $cTranslator->getTranslation('Vrátiť sa späť', 0) . '" class="button" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) . '/' . $navigateArrayUrlWithoutBase[0] . '/step1\';">' . $cTranslator->getTranslation('Vrátiť sa späť', 0) . '</button>';
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
                            echo '<br />';
                            $obj_cart->submit_step2();
                            break;

                        case 'trustpay-transfer':
                            $obj_cart->submit_step2();
                            $message = getContentByLabel('krok 3 - platba TrustPay prevod');
                            $search_tags = array('{trustpay_transfer_link}', '{trustpay_transfer_logo}');
                            $replace_tags = array('<a class="button-trustpay" href="' . trustPayTransferLink($_SESSION['order']['total_price'], $_SESSION['order']['reference']) . '">' . $cTranslator->getTranslation('Prejsť na TrustPay', 0) . '</a>', '<a class="trustpay-logo" href="' . trustPayTransferLink($_SESSION['order']['total_price'], $_SESSION['order']['reference']) . '"><img src="images/wrapper/trust-pay.svg" alt="TrustPay" /></a>');
                            $message = str_replace($search_tags, $replace_tags, $message);
                            echo $message;
                            
                            unset($_SESSION['order']);
                            break;
                        case 'trustpay-credit':
                            $obj_cart->submit_step2();
                            $message = getContentByLabel('krok 3 - platba TrustPay kredit');
                            $search_tags = array('{trustpay_credit_link}', '{trustpay_credit_logo}');
                            $replace_tags = array('<a class="button-trustpay" href="' . trustPayCreditLink($_SESSION['order']['total_price'], $_SESSION['order']['reference']) . '">' . $cTranslator->getTranslation('Prejsť na TrustPay', 0) . '</a>', '<a class="trustpay-logo" href="' . trustPayCreditLink($_SESSION['order']['total_price'], $_SESSION['order']['reference']) . '"><img src="images/wrapper/trust-pay.svg" alt="TrustPay" /></a>');
                            $message = str_replace($search_tags, $replace_tags, $message);
                            echo $message;
                            
                            unset($_SESSION['order']);
                            break;
                    }
                    break;

                default:
                    echo $obj_cart->show_cart_detail();

                    echo '<div class="clear"></div><br/>';

                    echo '<button type="button" title="' . $cTranslator->getTranslation('Pokračovať k pokladni', 0) . '" class="button btn-proceed-checkout btn-checkout right" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '/kosik/step1\';">' . $cTranslator->getTranslation('Pokračovať k pokladni', 0) . '</button>';
                    echo '<button type="button" title="' . $cTranslator->getTranslation('Pokračovať v nákupe', 0) . '" class="button btn-proceed-checkout btn-checkout" onclick="window.location=\'' . ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '\';">' . $cTranslator->getTranslation('Pokračovať v nákupe', 0) . '</button>';
                    echo '<div class="clear"></div>';

                    break;
            }
        }
        ?>
        </div>
        <?
        break;
    case "produkt":

        $obj_product = new Product;
        $obj_product->set_dph_price_visibility(VAT_VISIBILITY);
        $product = $obj_product->get_product();

        $manufacturer = Product::get_manufacturer($product->manufacturer_id);
        $photogallery = $obj_product->get_product_photogallery();

        // PRED KAZDYM POUZITIM TREBA NAJPRV OBJEKT UNSERIALIZOVAT ZO SESSION
        // A PO ZMENE OBSAHU OPAT SERIALIZOVAT
        // KOSIK SI VEZME Z NASTAVENIA PRODUKTOV NASTAVENIE O ZOBRAZOVANI DPH
        $obj_cart = unserialize($_SESSION['serialized_cart']);
        //$obj_cart->set_dph_price_visibility($obj_product->get_dph_price_visibility());

        if (isset($_POST['product_id']) AND isset($_POST['price_item']) AND isset($_POST['amount'])) {
            $obj_cart->insert_item($_POST['product_id'], $_POST['color'], $_POST['size'], $_POST['amount'], $_POST['price_item']);
            $_SESSION['product'] = 'added';
        }


        $_SESSION['serialized_cart'] = serialize($obj_cart);
        $obj_cart = unserialize($_SESSION['serialized_cart']);

        if (isset($_POST['product_id']) AND isset($_POST['price_item']) AND isset($_POST['amount'])) {
            header("refresh:0;url=" . ROOTDIR . '/' . $_GET['param']);
            exit;
        }
        // SEO produktu
        $Row['sk_name'] = $product->name;
        $Row['sk_product_description'] = $product->description;
        $Row['sk_product_keywords'] = $product->keywords;
        //
        ?>
        <div class="container">
            <div class="row">
                <div id="left-column" class="col-lg-3 col-md-3 col-sm-3 hidden-xs hidden-vs">
                    <?
                    include_once('include/left-column.php');
                    ?>
                </div>
                <div id="right-column" class="col-lg-9 col-md-9 col-sm-9 col-xs-12 col-vs-12">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="head diagonal-light">
                                <?= Menu::crumbleNavigation(); ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <h1><?= $product->name; ?></h1>
                        </div>
                    </div>

                    <div id="detail" class="row">
                        <div id="product-detail-photo" class="col-lg-4 col-md-4 col-sm-6 col-xs-6 col-xs-offset-0 col-vs-10 col-vs-offset-1">
                            <div class="item">
                                <a href="<?= ROOTDIR . '/photos/original/' . $product->image_src; ?>" class="fancybox" rel="gallery" title="<?= $product->name ?>">
                                    <div class="crust ratio-3_4">
                                        <div class="core">
                                            <?
                                            if (is_file("photos/preview/" . $product->image_src)) {
                                                echo '<img src="photos/preview/' . $product->image_src . '" alt="' . $product->name . '" />';
                                            } else {
                                                echo '<center>' . $cTranslator->getTranslation('Žiaden obrázok', 0) . '</center>';
                                                echo '<img src="images/wrapper/no-product-photo.png" alt="" />';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                        <div id="product-detail-info" class="col-lg-4 col-lg-push-4 col-md-4 col-md-push-4 col-sm-6 col-xs-6 col-xs-offset-0 col-vs-10 col-vs-offset-1">
                            <?
                            if (strtotime(date("Y-m-d H:i")) < strtotime($rowC->date . NEW_PRODUCT_LENGTH) OR $product->action == '1' OR $product->recommend == '1' OR $product->sale == '1' OR $product->action == '1' OR $product->novelty == '1') {
                                echo '<div class="status-container">';
                                /*
                                if (strtotime(date("Y-m-d H:i")) < strtotime($product->date . NEW_PRODUCT_LENGTH))
                                    echo '<span class="product-status new">' . $cTranslator->getTranslation('nový', 1) . '</span>';
                                */
                                if ($product->novelty == '1')
                                    echo '<span class="product-status new">' . $cTranslator->getTranslation('nový', 1) . '</span>';
                                if ($product->sale == '1')
                                    echo '<span class="product-status sale">' . $cTranslator->getTranslation('výpredaj', 1) . '</span>';
                                if ($product->recommend == '1')
                                    echo '<span class="product-status recommended">' . $cTranslator->getTranslation('odporúčaný', 1) . '</span>';
                                if ($rowC->action == '1' OR ($rowC->price_old > 0 AND $rowC->price < $rowC->price_old))
                                    echo '<span class="product-status discount' . ($rowC->action == '1' ? ' action' : '') . '">' . $cTranslator->getTranslation('akcia', 0) . '</span>';
                                echo '</div>';
                            }
                            ?>

                            <div class="code"><?= $cTranslator->getTranslation('Kód tovaru:', 0); ?> <strong><?= $product->code_1; ?></strong></div>
                            <div class="delivery-time<?= ($product->delivery_time == '1' ? ' wh' : ''); ?>"><span><?= ($product->delivery_time == '1' ? $cTranslator->getTranslation('skladom', 1) : $cTranslator->getTranslation('na objednávku', 1)); ?></span></div>

                            <form method="post" class="product" style="overflow: hidden">
                                <div>
                                    <input name="product_id" id="product_id" type="hidden" value="<?= $navigateEnd; ?>" />
                                    <input type="hidden" name="price_item" value="<?= $product->price; ?>" />
                                    <?
                                    /*
                                    $colors = $obj_product->get_product_colors();
                                    if (!empty($colors) AND $colors{0}->univerzal != '1') {
                                        echo '<label id="color-container" class="attribute"><span>' . $cTranslator->getTranslation('Vyberte si farbu:', 0) . '</span>';
                                        echo '<select name="color" id="color-select" class="styled">';

                                        foreach ($colors as $color) {
                                            if ($color->univerzal == 1)
                                                echo '<option value="' . $color->color_id . '">' . $cTranslator->getTranslation('Základná farba', 0) . '</option>';
                                            else
                                                echo '<option value="' . $color->product_color_id . '">' . $color->name . '</option>';
                                        }
                                        echo '</select>';
                                        echo '</label>';
                                    } else {
                                        //echo '<p class="attribute"><strong>' . $cTranslator->getTranslation('Farba', 0) . '</strong>: ' . $colors{0}->name . '</p>';
                                        echo '<input type="hidden" name="color" value="' . $colors{0}->product_color_id . '" />';
                                    }
                                    $sizes = $obj_product->get_product_sizes(NULL, $colors{0}->product_color_id);
                                    if (!empty($sizes) AND $sizes{0}->univerzal != '1') {

                                        echo '<label id="size-container" class="attribute"><span>' . $cTranslator->getTranslation('Vyberte si veľkosť:', 0) . '</span>';
                                        echo '<select name="size" id="size-select" class="styled">';

                                        foreach ($sizes as $size) {
                                            echo '<option value="' . $size->product_type_id . '">' . $size->name . '</option>';
                                        }
                                        echo '</select>';
                                        echo '</label>';
                                    } else {
                                        echo '<p class="attribute"><strong>' . $cTranslator->getTranslation('Veľkosť', 0) . '</strong>: ' . $sizes{0}->name . '</p>';
                                        echo '<input type="hidden" name="size" value="' . $sizes{0}->product_type_id . '" />';
                                    }
                                    */
                                    ?>
                                    <div id="price-container" class="col-lg-12 no-gutter">
                                        <?
                                        echo '<div class="price-box">';
                                            echo '<p class="price">' . number_format($product->price, 2, '.', ' ') . '&nbsp;&euro;</p>';
                                            echo '<p class="price old">' . (!empty($product->price_old) ? $cTranslator->getTranslation('Pôvodná cena') . '<br /><span>' . number_format($product->price_old, 2, '.', ' ') . '&nbsp;&euro;</span>' : '') . '</p>';
                                            if ($obj_product->get_dph_price_visibility()) {                                        
                                                echo '<p class="price wo-vat">' . $cTranslator->getTranslation('Cena bez DPH') . ': ' . number_format(($product->price / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;</p>';
                                            }
                                        echo '</div>';
                                        echo '<div class="discount-box">';
                                            if ($product->price_old != '0') {
                                                echo '<p class="percentage-discount"><span>' . $cTranslator->getTranslation('Zľava') . '</span><br />' . percentageDiscount($product->price, $product->price_old, 0) . '<span>%</span></p>';
                                            }
                                        echo '</div>';
                                        ?>
                                    </div>
                                    <div id="purchase-container" class="col-lg-12 no-gutter">                                        
                                        <label id="amount-container" class="attribute"><span><?= $cTranslator->getTranslation('Počet kusov:', 0); ?></span>
                                            <div id="inc-dec-control">
                                                <div class="dec amount-button">-</div>
                                                <input id="amount" type="text" name="amount" value="1" />
                                                <div class="inc amount-button">+</div>
                                            </div>
                                        </label>
                                        <input id="add-to-cart" type="submit" title="<?= $cTranslator->getTranslation("Kúpiť", 0); ?>" value="<?= $cTranslator->getTranslation("Kúpiť", 0); ?>" />
                                    </div>
                                </div>
                            </form>

                            <?
                            if ($_SESSION['product'] == 'added') {
                                unset($_SESSION['product']);
                                echo '<div id="product-added" data-redirect="' . ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '/kosik/step1" data-cancel="' . $cTranslator->getTranslation('Pokračovať v nákupe', 0) . '" data-confirm="' . $cTranslator->getTranslation('Pokračovať k pokladni', 0) . '">' . $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0) . '</div>';
                            }
                            ?>

                        </div>
                        <div id="product-detail-gallery" class="col-lg-4 col-lg-pull-4 col-md-4 col-md-pull-4 col-sm-12 col-xs-12 col-xs-offset-0 col-vs-10 col-vs-offset-1">
                            <?
                            if (count($photogallery) > 0) {
                                echo '<div id="gallery" class="">';
                                if (!empty($photogallery)) {
                                    foreach ($photogallery as $gallery_image) {
                                        ?>
                                        <div class="col-lg-6 col-md-6 col-sm-3 col-xs-2 col-vs-6 no-gutter">
                                            <div class="item">
                                                <a href="photos/original/<?= $gallery_image->src; ?>" class="fancybox" rel="gallery" title="<?= (!empty($rowP->name) ? $rowP->name : $product->name); ?>">
                                                    <div class="crust">
                                                        <div class="core">
                                                            <img src="photos/thumbnail/<?= $gallery_image->src; ?>" alt="<?= $product->name; ?>" />
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                        <?
                                    }
                                }
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="row">
                    	<div id="product-detail-description" class="default-text col-lg-12">
                                <?= $product->description; ?>
                          </div>
                    </div>
                    <?
                    $related_products = $obj_product->related_product();
                    if (count($related_products)) {
                        echo '<div id="related-products">';
                        echo '<h2>' . $cTranslator->getTranslation("Súvisiace produkty", 0) . '</h2>';
                        foreach ($related_products as $rowC) {
                                ?>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-4 col-vs-12 no-gutter">
                                    <div class="item">
                                        <a href="<?= ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) ?>/produkt/<?= String::SEOFriendlyText($rowC->name) . '/' . $rowC->product_id ?>" title="<?= $rowC->name ?>">
                                            <div class="crust ratio-4_3">
                                                <div class="core">
                                                    <?
                                                    if (is_file("./photos/thumbnail/" . $rowC->image_src)) { ?>
                                                        <img src="photos/thumbnail/<?= $rowC->image_src; ?>" alt="<?= $rowC->name; ?>" /><?
                                                    } else {
                                                        echo '<center>' . $cTranslator->getTranslation('Žiaden obrázok', 0) . '</center>';
                                                        echo '<img src="images/wrapper/no-product-photo.png" alt="" />';
                                                    }
                                                    if($rowC->delivery_time == '1') {
                                                        echo '<div class="delivery-time"><img src="images/wrapper/icon-mame-skladom.png" alt="' . $cTranslator->getTranslation('skladom', 0) . '" /></div>';
                                                    }
                                                    else {
                                                        echo '<div class="delivery-time"><img src="images/wrapper/icon-na-objednavku.png" alt="' . $cTranslator->getTranslation('na objednávku', 0) . '" /></div>';
                                                    }
                                                    ?>
                                                    <div class="status-container">
                                                        <?
                                                        /*
                                                        if (strtotime(date("Y-m-d H:i")) < strtotime($rowC->date . NEW_PRODUCT_LENGTH)) {
                                                            echo '<span class="new">' . $cTranslator->getTranslation('nový', 0) . '</span>';
                                                        }
                                                        */
                                                        if ($rowC->novelty == '1') {
                                                            echo '<span class="new">' . $cTranslator->getTranslation('nový', 0) . '</span>';
                                                        }
                                                        if ($rowC->sale == '1') {
                                                            echo '<span class="sale">' . $cTranslator->getTranslation('výpredaj', 0) . '</span>';
                                                        }
                                                        if ($rowC->recommend == '1') {
                                                            echo '<span class="recommended">' . $cTranslator->getTranslation('odporúčaný', 0) . '</span>';
                                                        }
                                                        if ($rowC->action == '1') {
                                                            echo '<span class="discount' . ($rowC->action == '1' ? ' action' : '') . '">' . $cTranslator->getTranslation('akcia', 0) . '</span>';
                                                        }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="directive">
                                                <h2><?= $rowC->name; ?></h2>
                                                <div class="price-box">
                                                    <?
                                                    if (VAT_VISIBILITY === TRUE) {
                                                        echo '<p class="price old">' . (!empty($rowC->price_old) ? number_format(($rowC->price_old / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;' : '') . '</p>';
                                                        echo '<p class="price">' . number_format(($rowC->price / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;</p>';
                                                    }
                                                    else {
                                                        echo '<p class="price old">' . (!empty($rowC->price_old) ? number_format($rowC->price_old, 2, '.', ' ') . '&nbsp;&euro;' : '') . '</p>';
                                                        echo '<p class="price">' . number_format($rowC->price, 2, '.', ' ') . '&nbsp;&euro;</p>';
                                                    }
                                                    echo '<div class="discount-box">';
                                                    if ($rowC->price_old > 0 AND $rowC->price < $rowC->price_old) {
                                                        
                                                            echo '<p class="percentage-discount"><span>' . $cTranslator->getTranslation('Zľava') . '</span><br />' . percentageDiscount($rowC->price, $rowC->price_old, 0) . '<span>%</span></p>';            
                                                        
                                                    }
                                                    echo '</div>';
                                                    ?>
                                                </div>
                                            </div>
                                        </a>
                                        <?
                                            if($rowC->delivery_time == '1') {
                                                ?>
                                                <div class="add-to-cart" data-product="<?= $rowC->product_id; ?>" data-price="<?= $rowC->price; ?>" data-redirect="<?= ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik/step1" data-cancel="<?= $cTranslator->getTranslation('Pokračovať v nákupe', 0); ?>" data-confirm="<?= $cTranslator->getTranslation('Pokračovať k pokladni', 0); ?>" data-message="<?= $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0); ?>" title="<?= $cTranslator->getTranslation("Kúpiť", 0); ?>"><i class="icon icon-cart"></i></div>
                                                <?
                                            }
                                            else {
											//POVODNA CLASSA DIV-KA ABY MAL KOSIK INU FARBU class="add-to-cart on-request"
                                                ?>
                                                <div class="add-to-cart" data-product="<?= $rowC->product_id; ?>" data-price="<?= $rowC->price; ?>" data-redirect="<?= ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik/step1" data-cancel="<?= $cTranslator->getTranslation('Pokračovať v nákupe', 0); ?>" data-confirm="<?= $cTranslator->getTranslation('Pokračovať k pokladni', 0); ?>" data-message="<?= $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0); ?>" title="<?= $cTranslator->getTranslation("Kúpiť", 0) . ' ' . $cTranslator->getTranslation('na objednávku', 0); ?>"><i class="icon icon-cart"></i></div>
                                                <?
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?
                            }
                        /*
                        echo '<div class="clear"></div>';
                        $i = 0;
                        foreach ($related_products as $related) {
                            $i++;
                            echo '<div class="item' . (($i % 3 == 0) ? ' last' : '') . '">';
                            echo '<div class="image-container">';
                            if (is_file("photos/thumbnail/" . $related->image_src)) {
                                echo '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/produkt/' . $related->name_seo . '/' . $related->product_id . '">';
                                echo '<span class="helper"></span>';
                                echo '<img src="photos/thumbnail/' . $related->image_src . '" alt="' . $related->name . '" />';
                                echo '</a>';
                            } else {
                                print '<center>' . $cTranslator->getTranslation('Žiaden obrázok', 0) . '</center>';
                            }
                            echo '</div>';
                            echo '<a class="title shorten" href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/produkt/' . $related->name_seo . '/' . $related->product_id . '">' . $related->name . '</a>';
                            if ($obj_product->get_dph_price_visibility()) {
                                echo '<p class="price">' . number_format($related->price, 2, '.', ' ') . ' &euro;</p>';
                            } else {
                                echo '<p class="price">' . number_format(($related->price / VAT_COEFFICIENT), 2, '.', ' ') . ' &euro;</p>';
                            }
                            echo '<div class="clear"></div>';
                            echo '<div class="add-to-cart">';
                            echo '<a href="' . ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) . '/produkt/' . String::SEOFriendlyText($related->name) . '/' . $related->product_id . '">' . $cTranslator->getTranslation('pridať do košíka', 0) . '<span></span>';
                            echo '</a>';
                            echo '</div>';
                            echo '<div class="clear"></div>';
                            echo '</div>';
                        }
                        */
                        echo '</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="row">
                <?
                include_once('include/inc_small_banners.php');
                ?>
            </div>
        </div>
        <?
        break;
    default:
        // 	VYTVORENIE objektu katalogu a generovanie katalogu
        $catalogue = new Catalogue;
        if (isset($_POST['submit-sorting'])) {
            if(!empty($_POST['filter'])) {
                foreach($_POST['filter'] as $filter) {
                    $array[] = $filter;
                }
                $_SESSION['userPrefs']['filter'] = $array;
            }
            else {
                $_SESSION['userPrefs']['filter'] = '';
            }
            $_SESSION['userPrefs']['productsOnPage'] = $_POST['catalogue_limit'];
            //$_SESSION['userPrefs']['orderBy'] = $_POST['sort_by'];
            if ($_POST['sort_by'] == 'price') {
                $_SESSION['userPrefs']['orderBy'] = 'discount_price_w_vat';
            } else {
                $_SESSION['userPrefs']['orderBy'] = $_POST['sort_by'];
            }
            $_SESSION['userPrefs']['orderSort'] = $_POST['dir'];
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
        if (!isset($_SESSION['userPrefs']['productsOnPage'])) {
            $_SESSION['userPrefs']['productsOnPage'] = '30';
        }
        $catalogue->set_catalogue_limit($_SESSION['userPrefs']['productsOnPage']);

        if (!isset($_SESSION['userPrefs']['orderBy'])) {
            //$_SESSION['userPrefs']['orderBy'] = 'default';
            $_SESSION['userPrefs']['orderBy'] = 'sorter';
        }
        if (!isset($_SESSION['userPrefs']['orderBy'])) {
            $_SESSION['userPrefs']['orderSort'] = 'ASC';
        }

        //$catalogue->set_catalogue_order(($_SESSION['userPrefs']['orderBy'] == 'default' ? ' action DESC, recommend DESC, sale DESC, date DESC ' : $_SESSION['userPrefs']['orderBy'] . ' ' . $_SESSION['userPrefs']['orderSort']));
        $catalogue->set_catalogue_order(($_SESSION['userPrefs']['orderBy'] == 'sorter' ? ' sorter ASC ' : ($_SESSION['userPrefs']['orderBy'] == 'default' ? ' action DESC, recommend DESC, sale DESC, date DESC ' : $_SESSION['userPrefs']['orderBy'] . ' ' . $_SESSION['userPrefs']['orderSort'])));
        $catalogue->set_catalogue_menu_id();
        $catalogue->set_dph_price_visibility(VAT_VISIBILITY);
        if (!empty($_GET['manufacturer'])) {
            $catalogue->set_manufacturer($_GET['manufacturer']);
        }

        if($_SESSION['base']['filter']) {
            $catalogue->set_filter($_SESSION['base']['filter']); // new, action, sale, recommend, novelty
        }
        else {
            $catalogue->set_filter((is_array($_SESSION['userPrefs']['filter']) ? implode(',', $_SESSION['userPrefs']['filter']) : $_SESSION['userPrefs']['filter'])); // new, action, sale, recommend, novelty
        }

        $products = $catalogue->get_catalogue();
        // - //
        // PRED KAZDYM POUZITIM TREBA NAJPRV OBJEKT UNSERIALIZOVAT ZO SESSION
        // A PO ZMENE OBSAHU OPAT SERIALIZOVAT
        $obj_cart = unserialize($_SESSION['serialized_cart']);
        //$obj_cart->set_dph_price_visibility($catalogue->get_dph_price_visibility());
        /*
          if (isset($_POST)) {
          $obj_cart->insert_item($_POST['product_id'], $_POST['color'], $_POST['amount'], $_POST['price_item']);
          }
         */
        $_SESSION['serialized_cart'] = serialize($obj_cart);
        $obj_cart = unserialize($_SESSION['serialized_cart']);

        $obj_paginator = new Paginator;

        $obj_paginator->set_items_per_page($catalogue->get_catalogue_limit()); //  pocet zobrazenych poloziek na 1 stranke
        $obj_paginator->set_items_count($catalogue->get_catalogue_items_count()); //  pocet poloziek v databaze

        $obj_paginator->set_params_base(Menu::getHyperLinkById($navigateId)); //base... to ani netreba menit
        $obj_paginator->set_params($navigateArrayUrlWithoutBase); //ani toto nie je treba menit... maximalne k tomu pripojit dalsie parametre, ak treba
        ?>
        <div class="container">
            <div class="row">
                <div id="left-column" class="col-lg-3 col-md-3 col-sm-3 hidden-xs hidden-vs">
                    <?
                    include_once('include/left-column.php');
                    ?>
                </div>
                <div id="right-column" class="col-lg-9 col-md-9 col-sm-9 col-xs-12 col-vs-12">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="head diagonal-light">
                                <?= Menu::crumbleNavigation(); ?>
                            </div>
                        </div>
                    </div>

                    <!--<div class="row">
                        <div class="col-lg-12">
                            <h1><?= (getParentId($navigateId) == ESHOP_MAIN_CATEGORY 
                                        ? '<strong>' . Menu::getHyperLinkTextById($navigateId) . '</strong>' 
                                        : '<strong>' . Menu::getHyperLinkTextById(getParentId($navigateId)) . '</strong>' . Menu::getHyperLinkTextById($navigateId)); ?></h1>
                        </div>
                    </div>-->

                    <div class="row">
                        <div class="sorting">
                            <?= $catalogue->generate_sorting_form(); ?>
                        </div>
                    </div>

                    <?
                    if(isset($_GET['manufacturer'])) {
                        $manufacturer = Product::get_manufacturer($_GET['manufacturer']);
                        ?>
                        <div class="row">
                            <div id="manufacturer" class="col-xs-12">
                                <img src="images/manufacturers/preview/<?= $manufacturer[0]->logo; ?>" alt="logo <?= $manufacturer[0]->name; ?>" />
                            </div>
                        </div>
                        <?
                    }
                    ?>

                    <div class="row">
                        <div class="col-lg-12">
                            <?= $obj_paginator->get_paginator(); ?>
                        </div>
                    </div>
                    
                    <div id="catalogue" class="row<?= (count($products) == 0 ? ' empty' : ''); ?>">
                        <div class="col-lg-12 col-xs-12">
                        <?
                        if (count($products) != 0) {
                            foreach ($products as $rowC) {
                                ?>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-6 col-vs-6 no-gutter">
                                    <div class="item">
                                        <a href="<?= ROOTDIR . '/' . Menu::getHyperLinkById($navigateId) ?>/produkt/<?= String::SEOFriendlyText($rowC->name) . '/' . $rowC->product_id ?>" title="<?= $rowC->name ?>">
                                            <div class="crust ratio-4_3">
                                                <div class="core">
                                                    <?
                                                    if (is_file("./photos/thumbnail/" . $rowC->image_src)) { ?>
                                                        <img src="photos/thumbnail/<?= $rowC->image_src; ?>" alt="<?= $rowC->name; ?>" /><?
                                                    } else {
                                                        echo '<center>' . $cTranslator->getTranslation('Žiaden obrázok', 1) . '</center>';
                                                        echo '<img src="images/wrapper/no-product-photo.png" alt="" />';
                                                    }
												//ZAKOMENTOVANE ZOBRAZOVANIE PRIZNAKOV PRODUKTU V ZOZNAMOCH
                                                   /*												   
												   if($rowC->delivery_time == '1') {
                                                        echo '<div class="delivery-time"><img src="images/wrapper/icon-mame-skladom.png" alt="' . $cTranslator->getTranslation('skladom', 0) . '" /></div>';
                                                    }
                                                    else {
                                                        echo '<div class="delivery-time"><img src="images/wrapper/icon-na-objednavku.png" alt="' . $cTranslator->getTranslation('na objednávku', 0) . '" /></div>';
                                                    }
													*/
                                                    ?>
                                                    <div class="status-container">
                                                        <?
                                                        /*
                                                        if (strtotime(date("Y-m-d H:i")) < strtotime($rowC->date . NEW_PRODUCT_LENGTH)) {
                                                            echo '<span class="new">' . $cTranslator->getTranslation('nový', 0) . '</span>';
                                                        }
                                                        */
													//ZAKOMENTOVANE ZOBRAZOVANIE PRIZNAKOV PRODUKTU V ZOZNAMOCH
														/*
                                                        if ($rowC->novelty == '1') {
                                                            echo '<span class="new">' . $cTranslator->getTranslation('nový', 0) . '</span>';
                                                        }
                                                        if ($rowC->sale == '1') {
                                                            echo '<span class="sale">' . $cTranslator->getTranslation('výpredaj', 0) . '</span>';
                                                        }
                                                        if ($rowC->recommend == '1') {
                                                            echo '<span class="recommended">' . $cTranslator->getTranslation('odporúčaný', 0) . '</span>';
                                                        }
                                                        if ($rowC->action == '1') {
                                                            echo '<span class="' . ($rowC->action == '1' ? ' action' : '') . '">' . $cTranslator->getTranslation('akcia', 0) . '</span>';
                                                        }
														*/
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="directive">
                                                <h2><?= $rowC->name; ?></h2>
                                                <div class="price-box">
                                                    <?
                                                    if (VAT_VISIBILITY === TRUE) {
                                                        echo '<p class="price old">' . (!empty($rowC->price_old) ? number_format(($rowC->price_old / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;' : '') . '</p>';
                                                        echo '<p class="price">' . number_format(($rowC->price / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;</p>';
                                                    }
                                                    else {
                                                        echo '<p class="price old">' . (!empty($rowC->price_old) ? number_format($rowC->price_old, 2, '.', ' ') . '&nbsp;&euro;' : '') . '</p>';
                                                        echo '<p class="price">' . number_format($rowC->price, 2, '.', ' ') . '&nbsp;&euro;<br/>';
														if($rowC->delivery_time == '1') {
                                                        echo '<span style="font-size:9px;color:#4A6D22;">'.$cTranslator->getTranslation('skladom', 0).'</span>';
                                                    }
                                                    else {
                                                        echo '<span style="font-size:9px;color:#936544;">'.$cTranslator->getTranslation('na objednávku', 0).'</span>';
                                                    }
														echo'</p>';
                                                    }
                                                    echo '<div class="discount-box">';
												//ZAKOMENTOVANE ZOBRAZOVANIE ZLAVY PRODUKTU V ZOZNAMOCH
													/*	
                                                    if ($rowC->price_old > 0 AND $rowC->price < $rowC->price_old) {
                                                        
                                                            echo '<p class="percentage-discount"><span>' . $cTranslator->getTranslation('Zľava') . '</span><br />' . percentageDiscount($rowC->price, $rowC->price_old, 0) . '<span>%</span></p>';            
                                                        
                                                    }
													*/
                                                    echo '</div>';
                                                    ?>
                                                </div>
                                            </div>
                                        </a>
                                        <?
                                            if($rowC->delivery_time == '1') {
                                                ?>
                                                <div class="add-to-cart" data-product="<?= $rowC->product_id; ?>" data-price="<?= $rowC->price; ?>" data-redirect="<?= ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik/step1" data-cancel="<?= $cTranslator->getTranslation('Pokračovať v nákupe', 0); ?>" data-confirm="<?= $cTranslator->getTranslation('Pokračovať k pokladni', 0); ?>" data-message="<?= $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0); ?>" title="<?= $cTranslator->getTranslation("Kúpiť", 0); ?>"><i class="icon icon-cart"></i></div>
                                                <?
                                            }
                                            else {
											//POVODNA CLASSA DIV-KA ABY MAL KOSIK INU FARBU class="add-to-cart on-request"
                                                ?>
                                                <div class="add-to-cart" data-product="<?= $rowC->product_id; ?>" data-price="<?= $rowC->price; ?>" data-redirect="<?= ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik/step1" data-cancel="<?= $cTranslator->getTranslation('Pokračovať v nákupe', 0); ?>" data-confirm="<?= $cTranslator->getTranslation('Pokračovať k pokladni', 0); ?>" data-message="<?= $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0); ?>" title="<?= $cTranslator->getTranslation("Kúpiť", 0) . ' ' . $cTranslator->getTranslation('na objednávku', 0); ?>"><i class="icon icon-cart"></i></div>
                                                <?
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?
                            }
                        } else {
                            echo '<p>' . $cTranslator->getTranslation("V tejto kategórii sa nič nenachádza", 0) . '</p>';
                        }
                        ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <?= $obj_paginator->get_paginator(); ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <?
                include_once('include/inc_small_banners.php');
                ?>
            </div>
        </div>
        <?
        break;
}
?>
<script type="text/javascript" src="js/mod_eshop.js?v=20170305"></script>
<?

$moduleContent = ob_get_contents();
ob_clean();
?>