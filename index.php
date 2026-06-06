<?php
require_once("shared/config.inc.php");
setcookie("we-love-cookies", "", time() - 3600);

header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// redirect neexistujúci "webshop" na "hlavna-stranka"
// premenili názov
if (strpos($_GET['param'], 'sk/webshop/') !== false) {
    //echo ROOTDIR . str_replace('sk/webshop/', '/sk/hlavna-stranka/', $_GET['param']);
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: ' . ROOTDIR . str_replace('sk/webshop/', '/sk/hlavna-stranka/', $_GET['param']));
    exit;
}

$parts = array(); // ???????????????????????

$_GET['action'] = end(explode("/", $_GET['param']));
// prihlásenie
if (isset($_POST['form-action']) AND $_POST['form-action'] == 'login' AND ! $user->isAuthenticated()) {
    $user->Authenticate($_POST['username'], $_POST['pwd']);
}
// odhlásenie
if ($_GET['action'] == "logout" AND $user->isAuthenticated()) {
    $user->Logout();
}
// Shopping cart
if (empty($_SESSION['serialized_cart'])) {
    $obj_cart = new Cart;
    $_SESSION['serialized_cart'] = serialize($obj_cart); // toto treba tiez vykonat VZDY po zmene v kosiku (inak sa zmena trvalo neprejavi)
} else {
    $obj_cart = unserialize($_SESSION['serialized_cart']);
}
$products_count = $obj_cart->get_cart_count(); // počet produktov v košiku

// newsletter - registracia
if ($_POST AND ( isset($_POST['newsletter']) AND $_POST['newsletter'] == 'register')) {
    if (empty($_POST['newsletter_email'])) {
        $error['newsletter_email'] = $cTranslator->getTranslation("Nezadali ste Vašu e-mailovú adresu");
    }
    if (!filter_var($_POST['newsletter_email'], FILTER_VALIDATE_EMAIL)) {
        $error['newsletter_email'] = $cTranslator->getTranslation("Nezadali ste korektnú e-mailovú adresu");
    }
    if ($user->checkUser_email($_POST['newsletter_email'])) {
        $error['newsletter_email'] = $cTranslator->getTranslation("Zadaný e-mail sa už nachádza v našej databáze.");
    }
    if (empty($error)) {
        $user_data = array(
            'meno' => 'Newsletter',
            'priezvisko' => $_POST['newsletter_email'],
            'fullname' => 'Newsletter ' . $_POST['newsletter_email'],
            'mail' => $_POST['newsletter_email'],
            'active' => "0",
            'newsletter' => '1'
        );
        $user_insert_result = $user->insertUser($user_data);
        if ($user_insert_result === 'email-exist') {
            Message::setMessage($cTranslator->getTranslation('Zadaný e-mail sa už nachádza v našej databáze.', 0), 2);
        } else {
            Message::setMessage($cTranslator->getTranslation('Váš e-mail bol úspešne pridaný do databázy na obder newslettra', 0), 0);
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }
}


// generovanie obsahu stránky
HTML::createPageContent();
// DEFINICIA REZERVOVANYCH PREMENNYCH
$isMobile = $_SESSION['pref']['isMobile'];
$navigateId = $Row["menu_id"];
$navigateParentId = $Row["child_of"];
$navigateArrayUrl = explode("/", $_GET['param']);
$navigateEnd = end(explode("/", $_GET['param']));
$navigateUrlWithoutBase = explode($Row[strtolower($_SESSION['lang']) . '_name_seo'] . '/', $_GET['param']);
$navigateUrlWithoutBase = $navigateUrlWithoutBase[1];
$navigateArrayUrlWithoutBase = explode("/", $navigateUrlWithoutBase);
$navigateArrayNumbers = explode(";", $Row["left_menu_id"]);
$navigateArrayNumbers = array_reverse($navigateArrayNumbers);
$navigateBaseId = $navigateArrayNumbers[1];
$navigateArrayChildId = explode(";", $Row["right_menu_id"]);
$lang = $_SESSION['lang'];
$h1Expanded = ((!empty($Row[$_SESSION['lang'] . '_page_title']) AND trim($Row[$_SESSION['lang'] . '_page_title']) != '') ? $Row[$_SESSION['lang'] . '_page_title'] : Menu::getHyperLinkTextById($navigateId));
// includuje stránku
if (file_exists("modules/mod_" . $Row['module'] . ".php"))
    include("modules/mod_" . $Row['module'] . ".php");
?>
<!DOCTYPE html>
<html lang="<?= $_SESSION["lang"]; ?>">
    <head>
        <?= HTML::createHeader(); ?>
        <link rel="stylesheet" type="text/css" href="js/cookieconsent/cookieconsent.css">
        <script type="text/javascript">
            var rootdir = '<?= ROOTDIR; ?>';
            var eshopdir = '<?= ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY); ?>'
            var notice_title = '<?= $cTranslator->getTranslation('Oznam', 0); ?>';
            var error_title = '<?= $cTranslator->getTranslation('Chyba', 0); ?>';
            var close_title = '<?= $cTranslator->getTranslation('Zatvoriť', 0); ?>';
            var max_amount = '<?= $cTranslator->getTranslation('Zadaný počet momentálne nemáme na sklade.', 0); ?>';
            var validator_send_function = '<?= $cTranslator->getTranslation('Odošlite názov funkcie "validator.setAddnlValidationFunction(DoCustomValidation)".', 0); ?>';
            var validator_wrong_form = '<?= $cTranslator->getTranslation('Nesprávne nastavený formulár. Kontaktujte administrátora.', 0); ?>';
            var validator_form_not_found = '<?= $cTranslator->getTranslation('Formulár {frmname} nebol nájdený.', 0); ?>';
            var validator_input_not_found = '<?= $cTranslator->getTranslation('Nemôžeme nájsť textové pole s názvom {itemname}.', 0); ?>';
            <?= (isset($_SESSION['pref']['ga'])) ? 'var gac = \'' . $_SESSION['pref']['ga'] . '\';' : ''; ?>

        </script>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= $_SESSION['pref']['ga']; ?>" data-cookiecategory="analytics"></script>
    <script data-cookiecategory="analytics">
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= $_SESSION['pref']['ga']; ?>');
    </script>
    <script src="https://analytics.ahrefs.com/analytics.js" data-key="u3EbJCSmwHUNN29XlqvQ9Q" async></script>
        <!-- Meta Pixel Code -->
            <script>
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', '523025483646757');
            fbq('track', 'PageView');
            </script>
            <noscript><img height="1" width="1" style="display:none"
            src="https://www.facebook.com/tr?id=523025483646757&ev=PageView&noscript=1"
            /></noscript>
        <!-- End Meta Pixel Code -->
    </head>
    <body class="lang-<?= $_SESSION["lang"] . ' page-' . $Row['menu_id'] . ' ' . $Row['module']; ?>">
        <!--<div class="breakpoint-viewer d-none d-xxl-block bg-secondary">XXL</div>
        <div class="breakpoint-viewer d-none d-xl-block d-xxl-none bg-info">XL</div>
        <div class="breakpoint-viewer d-none d-lg-block d-xl-none bg-primary">LG</div>
        <div class="breakpoint-viewer d-none d-md-block d-lg-none bg-success">MD</div>
        <div class="breakpoint-viewer d-none d-sm-block d-md-none bg-warning">SM</div>
        <div class="breakpoint-viewer d-block d-sm-none bg-danger">XS</div>-->
        <noscript><?= $cTranslator->getTranslation('Táto stránka vyžaduje pre svoj chod <strong>Javascript</strong>. Niektoré funckie a stránky sú <strong>s vypnutým javascriptom nedostupné</strong>. Pre správny chod stránky <strong>prosím povoľte javascript</strong>.', 0); ?></noscript>
        <div id="wrapper"><!-- wrapper -->
            <div id="header"><!-- header -->
                <?php /*
                <div id="strip-nav">
                    <div class="container-fluid">
                        <div class="row d-flex align-items-center">
                            <div class="col-md-2 col-sm-2 col-xs-4">
                                <div id="logo">
                                    <a href="<?= ROOTDIR ?>" title="<?= Menu::getHyperLinkTextByID(2); ?>">
                                        <img src="images/wrapper/logo-growmedical-titan.png" alt="logo" />
                                    </a>
                                </div>
                            </div>
                            <div class="icon-nav col-md-2 col-md-push-8 col-sm-2 col-sm-push-8 col-sm-offset-0 col-xs-12">
                                <div class="menu-icons">
                                    <a href="<?= Menu::getHyperlinkById(SEARCH_PAGE_ID); ?>"></a>
                                    <div id="search-container">
                                        <i class="search-icon"></i>
                                        <form action="<?= Menu::getHyperlinkById(SEARCH_PAGE_ID); ?>" method="get">
                                            <input name="q" type="text" id="search_keyword" placeholder="<?= $cTranslator->getTranslation('Zadajte hľadaný výraz', 0); ?>" />
                                            <button name="submit" type="submit" id="search_button" title="<?= $cTranslator->getTranslation('Hľadať', 0); ?>" /></button>
                                        </form>
                                    </div>

                                    <a class="shopping-cart" href="<?= Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik" title="<?= $cTranslator->getTranslation('Nákupný košík', 0); ?>" aria-label="<?= $cTranslator->getTranslation('Nákupný košík', 0); ?><?= ($obj_cart->get_cart_quantity() > 0) ? ' (' . $obj_cart->get_cart_quantity() . ')' : '' ?>">
                                        <i class="cart-icon"></i>
                                        <?
                                        if ($obj_cart->get_cart_quantity() > 0 ) {
                                            ?>
                                            <span class="cart quantity"><span>
                                            <?
                                                echo $obj_cart->get_cart_quantity();
                                            ?>

                                            </span></span>
                                        <?}?>
                                    </a>
                                    <div id="shopping-cart-info" style="display: none;">
                                        <?
                                        foreach ($obj_cart->get_cart_items() as $key => $value) {
                                            echo '<div data-pid="' . $value['product_id'] . '" data-stock="' . $value['stock'] . '" data-amount="' . $value['amount'] . '"></div>';
                                        }
                                        ?>
                                    </div>
                                    <?
                                    //if ($_SESSION['product'] == 'added') {
                                    //    unset($_SESSION['product']);
                                    //    echo '<div id="product-added">' . $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0) . '</div>';
                                    }
                                    if (!$user->isAuthenticated()) {
                                        ?>
                                        <a class="user-account" href="<?= ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID); ?>/prihlasenie" title="<?= $cTranslator->getTranslation('Prihlásenie', 0); ?>">
                                            <i class="user-icon"></i>
                                        </a>
                                        <?
                                    } else {
                                        ?>
                                        <a class="user-account" href="<?= ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID); ?>" title="<?= Menu::getHyperlinkTextById(USER_ACCOUNT_MANAGE_ID); ?>">
                                            <i class="user-icon"></i>
                                        </a>
                                        <?
                                    }
                                    ?>
                                </div>
                                <!--
                                <div id="shopping-cart-preview">
                                    <a href="<?= Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik"><?= $cTranslator->getTranslation('Nákupný košík', 0) . ' <strong>' . (VAT_VISIBILITY ? number_format($obj_cart->get_cart_value(), 2, '.', ' ') : number_format(($obj_cart->get_cart_value() / VAT_COEFFICIENT), 2, '.', ' ')) . ' &euro;</strong>'; ?></a>

                                </div>-->
                            </div>
                            <div class="col-md-8 col-md-pull-2 col-sm-8 col-sm-pull-2 col-xs-12">
                                <div id="menu">
                                    <? Menu::popupMenu(ESHOP_MAIN_CATEGORY); ?>
                                    <!-- <? Menu::popupMenu(1); ?> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                */ ?>
                <nav class="navbar fixed-top navbar-expand-lg">
                    <div class="container-fluid" style="flex-wrap: wrap;">
                        <a class="navbar-brand header-logo-container" href="<?= ROOTDIR ?>" aria-label="Domov">
                            <img src="images/wrapper/logo-growmedica-titan.png" alt="GrowMedica logo" width="188" height="81" />
                        </a>
                        <div class="icon-nav order-lg-2">
                            <div class="menu-icons">
                                <a href="<?= Menu::getHyperlinkById(SEARCH_PAGE_ID); ?>" class="search-toggler" aria-label="Vyhľadávanie"><i class="search-icon"></i></a>
                                <a class="shopping-cart" href="<?= Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik" title="<?= $cTranslator->getTranslation('Nákupný košík', 0); ?>" aria-label="<?= $cTranslator->getTranslation('Nákupný košík', 0); ?> (<?= $obj_cart->get_cart_quantity(); ?>)">
                                    <i class="cart-icon"></i>
                                    <?php
                                    if ($obj_cart->get_cart_quantity() >= 0 ) {
                                        ?>
                                        <span class="cart quantity"><span>
                                        <?php
                                            echo $obj_cart->get_cart_quantity();
                                        ?>
                                        </span></span>
                                    <?php
                                    }
                                    ?>
                                </a>
                                <div id="shopping-cart-info" style="display: none;">
                                    <?
                                    foreach ($obj_cart->get_cart_items() as $key => $value) {
                                        echo '<div data-pid="' . $value['product_id'] . '" data-stock="' . $value['stock'] . '" data-amount="' . $value['amount'] . '"></div>';
                                    }
                                    ?>
                                </div>
                                <?
                                //if ($_SESSION['product'] == 'added') {
                                //    unset($_SESSION['product']);
                                //    echo '<div id="product-added">' . $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0) . '</div>';
                                //}
                                if (!$user->isAuthenticated()) {
                                    ?>
                                    <a class="user-account" href="<?= ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID); ?>/prihlasenie" title="<?= $cTranslator->getTranslation('Prihlásenie', 0); ?>" aria-label="Prihlásenie">
                                        <i class="user-icon"></i>
                                    </a>
                                    <?php
                                }
                                else {
                                    ?>
                                    <a class="user-account" href="<?= ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID); ?>" title="<?= Menu::getHyperlinkTextById(USER_ACCOUNT_MANAGE_ID); ?>" aria-label="Môj účet">
                                        <i class="user-icon"></i>
                                    </a>
                                    <?php
                                }
                                ?>
                            </div>
                            <!--
                            <div id="shopping-cart-preview">
                                <a href="<?= Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik"><?= $cTranslator->getTranslation('Nákupný košík', 0) . ' <strong>' . (VAT_VISIBILITY ? number_format($obj_cart->get_cart_value(), 2, '.', ' ') : number_format(($obj_cart->get_cart_value() / VAT_COEFFICIENT), 2, '.', ' ')) . ' &euro;</strong>'; ?></a>

                            </div>-->
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                        <div class="collapse navbar-collapse order-lg-4" id="navbarNav" style="width: 100%;">
                            <?php Menu::popupMenuBS(ESHOP_MAIN_CATEGORY, 'nav main-menu'); ?>
                        </div>
                    </div>
                </nav>
                <div id="search-container">
                    <div class="container">
                        <div class="col-12 col-md-6 offset-md-3">
                            <form action="<?= Menu::getHyperlinkById(SEARCH_PAGE_ID); ?>" method="get">
                                <div class="input-group my-5">
                                    <input name="q" type="text" id="search_keyword" class="form-control" placeholder="<?= $cTranslator->getTranslation('Zadajte hľadaný výraz', 0); ?>" />
                                    <button name="submit" type="submit" id="search_button" class="btn btn-outline-secondary" title="<?= $cTranslator->getTranslation('Hľadať', 0); ?>" /><i class="icon search-icon"></i> </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?
                include ('include/slideshow.php'); // slider
                ?>
                <!-- <div id="deputy"></div> -->
            </div><!-- header END -->

            <main id="content"><!-- content -->
                <?
                Message::getMessage();
                // obsah
                if (file_exists("modules/mod_" . $Row['module'] . ".php")) {
                    echo $moduleContent;
                }
                else {
                    ?>
                    <div id="static_content">
                        <div class="container">
                                    <h1><?= $h1Expanded; ?></h1>
                                    <div class="default-text">
                                        <?= html_entity_decode($Row['content'], ENT_QUOTES, "UTF-8"); ?>
                                    </div>
                                    <?
                                    if ($user->isAdmin()) {
                                        ?>
                                        <div class="edit-link">
                                            <a href="#" onClick="javascript:openPopupWindow('content', <?= $Row["menu_id"]; ?>, 900, 600); return false;">
                                                <span class="translation_edit" rel="<?= $cTranslator->getTranslation('Upraviť obsah', 0); ?>"></span>
                                                <?= $cTranslator->getTranslation('Upraviť obsah', 0); ?>
                                            </a>
                                        </div>
                                        <?
                                    }
                                    ?>
                        </div>
                    </div>
                    <?
                }
                ?>
            </main><!-- content END -->

            <footer><!-- footer -->
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-3">
                            <div id="logo">
                                <a href="<?= ROOTDIR ?>" title="<?= Menu::getHyperLinkTextByID(2); ?>" aria-label="Domov">
                                    <span class="middler"></span>
                                    <img src="images/wrapper/logo-<?= PAGE; ?>-white.png" alt="GrowMedica logo" width="189" height="81" loading="lazy" />
                                </a>
                            </div>
                        </div>
                        <div class="col-12 col-md-9">
                            <div class="row">
                                <div class="col-12 col-md-4">
                                    <hr class="over hidden-xs" />
                                    <div class="pushed">
                                        <h2><?= $cTranslator->getTranslation('footer: informácie'); ?></h2>
                                        <?= Menu::returnMenu(1000, FALSE, '', 'fnav', NULL, '68, 112, 113, 130, 145, 146, 12'); ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <hr class="over" />
                                    <div class="pushed">
                                        <h2><?= $cTranslator->getTranslation('footer: menu'); ?></h2>
                                        <?= Menu::returnMenu(ESHOP_MAIN_CATEGORY, FALSE, '', 'fnav'); ?>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <hr class="over" />
                                    <div class="pushed">
                                        <h2 class="contacts"><?= $cTranslator->getTranslation('Kontakt'); ?></h2>
                                        <ul class="contacts">
                                            <li><img src="images/wrapper/location.png" alt="" width="12" height="16" loading="lazy"><?= $cTranslator->getTranslation('footer-adress'); ?></li>
                                            <li><?= $cTranslator->getTranslation('footer-city'); ?></li>
                                            <li><img src="images/wrapper/phone_icon.png" alt="" width="18" height="18" loading="lazy"><a href="tel:<?= $cTranslator->getTranslation('footer-phone', 0); ?>"><?= $cTranslator->getTranslation('footer-phone'); ?></a></li>
                                            <li><img src="images/wrapper/mail_icon.png" alt="" width="20" height="13" loading="lazy"><a href="mailto:<?= $cTranslator->getTranslation('footer-email', 0); ?>"><?= $cTranslator->getTranslation('footer-email'); ?></a></li>
                                        </ul>
                                    </div>
                                    <hr class="over" />
                                    <div class="social">
                                        <ul>
                                            <li>
                                                <a href="<?php echo $cTranslator->getTranslation('https://www.instagram.com/', 0); ?>" target="_blank" title="Instagram" aria-label="Instagram">
                                                    <img src="images/icons/icon-instagram.svg" alt="Instagram" width="24" height="24" loading="lazy" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo $cTranslator->getTranslation('https://www.facebook.com/', 0); ?>" target="_blank" title="Facebook" aria-label="Facebook">
                                                    <img src="images/icons/icon-facebook.svg" alt="Facebook" width="24" height="24" loading="lazy" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo $cTranslator->getTranslation('https://www.twitter.com/', 0); ?>" target="_blank" title="Twitter" aria-label="Twitter">
                                                    <img src="images/icons/icon-twitter.svg" alt="Twitter" width="24" height="24" loading="lazy" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo $cTranslator->getTranslation('https://www.pinterest.com/', 0); ?>" target="_blank" title="Pinterest" aria-label="Pinterest">
                                                    <img src="images/icons/icon-pinterest.svg" alt="Pinterest" width="24" height="24" loading="lazy" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo $cTranslator->getTranslation('https://www.youtube.com/', 0); ?>" target="_blank" title="YouTube" aria-label="YouTube">
                                                    <img src="images/icons/icon-youtube.svg" alt="YouTube" width="24" height="24" loading="lazy" />
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?php echo $cTranslator->getTranslation('https://www.tiktok.com/', 0); ?>" target="_blank" title="TikTok" aria-label="TikTok">
                                                    <img src="images/icons/icon-tiktok.svg" alt="TikTok" width="24" height="24" loading="lazy" />
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-9 offset-md-3">
                            <div class="row">
                                <div class="col-12 col-md-7">
                                    <div class="newslform">
                                        <form id="newsletter" class="" action="#newsletter" method="post">
                                            <label for="newsletter_email">
                                                <?= $cTranslator->getTranslation('footer: newsletter-text', 1); ?>
                                            </label>
                                            <input type="hidden" name="newsletter" value="register" />
                                            <button name="submit" type="submit" id="reg-newsl"><?= $cTranslator->getTranslation('Prihlásiť', 0); ?></button>
                                            <div style="overflow: hidden; padding-right: .5em;">
                                                <input style="width: 100%;" type="text" id="newsletter_email"  placeholder="<?= $cTranslator->getTranslation('Váš e-mail', 0); ?>" name="newsletter_email"<?= ((isset($error) AND array_key_exists('newsletter_email', $error)) ? ' class="error"' : ''); ?> />
                                            </div>
                                        </form>
                                        <?= ((isset($error) AND array_key_exists('newsletter_email', $error)) ? '<div class="error"><small>' . $error['newsletter_email'] . '</small></div>' : ''); ?>
                                        <script type="text/javascript">
                                            window.addEventListener('DOMContentLoaded', function() {
                                                var search = new Validator("newsletter");
                                                search.addValidation("newsletter_email", "maxlen=50", "<?= $cTranslator->getTranslation('Nezadali ste správnu dĺžku e-mailovej adresy', 0); ?>");
                                                search.addValidation("newsletter_email", "req", "<?= $cTranslator->getTranslation('Nezadali ste e-mailovú adresu', 0); ?>");
                                                search.addValidation("newsletter_email", "email", "<?= $cTranslator->getTranslation('Nezadali korektnú e-mailovú adresu', 0); ?>");
                                            });
                                        </script>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 offset-md-1">
                                <div class="paygates">
                                    <span class="cf"><?= $cTranslator->getTranslation('footer: paygate-text', 1); ?></span>
                                    <div class="clear"></div>
                                    <ul class="nav">
                                        <li><img src="images/wrapper/mastercard.svg" alt="masterCard" /></li>
                                        <li><img src="images/wrapper/maestro.svg" alt="maestro" /></li>
                                        <li><img src="images/wrapper/visa.svg" alt="VISA" /></li>
                                        <li><img src="images/wrapper/Google_Pay_Logo.png" alt="Google Pay" /></li>
                                        <li><img src="images/wrapper/Apple_Pay_logo.png" alt="Apple Pay" /></li>
                                        <li><img src="images/wrapper/dpd_logo.png" alt="DPD" /></li>
                                        <li><img src="images/wrapper/packeta_logo.png" alt="Packeta" /></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div class="copy">
                                <?= '<p>&copy;' . date("Y") . ' <span style="color: hsl(0, 0%, 100%)">' . PROJECT_NAME . '</span><span class="delimiter">|</span> <a href="' . Menu::getHyperlinkById(16) . '">' . Menu::getHyperlinkTextById(16) . '</a><span class="delimiter">|</span> <a href="#" data-cc="show-preferencesModal">' . $cTranslator->getTranslation('Nastavenia cookies', 0) . '</a> <span class="delimiter">|</span> Design by <a href="http://www.sixnet.sk" target="_blank" title="Webdizajn, tvorba web stránok Košice, webhosting, SEO, vyhľadávače">SIXNET</a></p>'; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </footer><!-- footer END -->
        </div><!-- wrapper END -->
    </div>
    <?= HTML::createFooter(); ?>
    <div id="overlayBox"></div>
    <script type="text/javascript">
        $(document).ready(function() {
            var sub = $('.left-nav ul .subsubmenu');
                    sub.each(function() {
                    if ($(this).hasClass('selected')) {
                        $(this).children('a').append('<div class="sub-wrapp"><i class="glyphicon glyphicon-minus"></i><i class="glyphicon glyphicon-plus" style="display: none"></i></div>');
                    }
                    else {
                        $(this).children('a').append('<div class="sub-wrapp"><i class="glyphicon glyphicon-plus"></i><i class="glyphicon glyphicon-minus" style="display: none"></i></div>');
                    }
            });
            var w = $('.sub-wrapp');
            w.on('click', function(e) {
                e.preventDefault();
                $(this).parent().next().slideToggle();
                $(this).find('i').toggle();
            });
            // Remove incorrect ARIA roles added by legacy slick slider library
            $('.slick-track').removeAttr('role');
            $('.slick-slide').removeAttr('role');
        });

    </script>
    
    <?php include_once('include/cookieconsent-init.php'); ?>
</body>
</html>