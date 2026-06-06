<?php
require_once("../shared/config.inc.php");

if ($_GET['action'] == "logout"):
    $user->Logout();
endif;

if ((!isset($_GET['module']) or $_GET['module'] != "login") and ! $user->isAdmin()):
    header("Location:" . ROOTDIR . "/setup/index.php?module=login");
    exit;
endif;

if (!isset($_GET['module'])):
    header("Location:" . ROOTDIR . "/setup/index.php?module=menu");
    exit;
endif;

if (isset($_POST['mail']) AND $_POST['action'] == 'setup-login') { // and !$user->isAuthenticated()
    //if(isset($_POST['mail'])):
    if ($user->isAuthenticated()) {
        session_start();
    }
    $user->Authenticate($_POST['mail'], $_POST['pwd'], 0);
}

if (!isset($_GET['module'])) {
    header("Location:index.php?module=menu");
    exit;
}

if (file_exists("modules/_" . $_GET['module'] . ".php")) {
    ob_start();
    require_once("modules/_" . $_GET['module'] . ".php");
    $moduleContentSetup = ob_get_contents();
    ob_clean();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title><?= PROJECT_NAME ?></title>
        <base href="<?= ROOTDIR ?>/setup/" />
        <!-- CSS -->
        <link type="text/css" href="<?= fileWithLastChange('index.css'); ?>" rel="stylesheet" />
        <link type="text/css" href="../js/fancybox/jquery.fancybox-1.3.4.css" rel="stylesheet" />
        <link type="text/css" href="../fonts/css/font-awesome.min.css" rel="stylesheet" />
        <link rel="stylesheet" id="bsdp-css" href="../js/bootstrap-datepicker/css/datepicker.css" />
        <link type="text/css" href = "http://fonts.googleapis.com/css?family=Signika:400,700&amp;subset=latin,latin-ext" rel="stylesheet" />
        <!-- JS -->
        <script type="text/javascript" src="../js/jquery/jquery-1.9.1.min.js"></script>
        <script type="text/javascript" src="../js/jquery/jquery-migrate-1.1.0.js"></script>
        <script type="text/javascript" src="../js/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="../js/functions.js"></script>
        <script type="text/javascript" src="../js/fancybox/jquery.fancybox-1.3.4_patch.js"></script>
        <script type="text/javascript" src="../js/ckeditor/ckeditor.js?t=<?= time(); ?>"></script>
        <script type="text/javascript" src="../js/form-validator.js" async="async"></script>
        <!--Bootstrap datepicker-->
        <script type="text/javascript" src="../js/bootstrap-datepicker/bootstrap-datepicker.js"></script>
        <script type="text/javascript" charset="UTF-8" src="../js/bootstrap-datepicker/locales/bootstrap-datepicker.sk.js"></script>
        <script type="text/javascript" src="../js/site-admin.js"></script>
    </head>
    <body>
        <div id="maindiv">
            <div id="header">
                <div id="headerName"><img src = "images/header-logo_01.jpg" width = "283" height = "90" align = "left" alt = "SixAdmin" />
                    <div id="logout">
                        <? if ($user->isAdmin()) { ?>
                            <a href="<?= ROOTDIR ?>" target="_blank"><strong>Zobraziť stránku</strong></a><strong> | <a href="./index.php?module=users&amp;action=logout">Odhlásenie</a></strong>
                        <? } ?>
                    </div>
                    <h1><?= PROJECT_NAME ?></h1>
                    <p>CMS administračný systém ver.3.0</p>
                </div>
                <?
                if ($user->isAdmin()) {
                    ?>
                    <div class="top-menu">
                        <ul>
                            <li><a<?= ($_GET['module'] == 'menu' ? ' class="selected"' : ''); ?> href="./index.php?module=menu">Štruktúra</a></li>
                            <li><a<?= ($_GET['module'] == 'users' ? ' class="selected"' : ''); ?> href="./index.php?module=users">Uživatelia</a></li>
                            <li><a<?= ($_GET['module'] == 'slideshow' ? ' class="selected"' : ''); ?> href="./index.php?module=slideshow">Záhlavia</a></li>
                            <li><a<?= ($_GET['module'] == 'static-content' ? ' class="selected"' : ''); ?> href="./index.php?module=static-content">Pomocné texty</a></li>
                            <li><a<?= (($_GET['module'] == 'article' OR $_GET['module'] == 'article_category') ? ' class="selected"' : ''); ?> href="./index.php?module=article">Články</a></li>
                            <li><a<?= (($_GET['module'] == 'gallery' OR $_GET['module'] == 'article_gallery') ? ' class="selected"' : ''); ?> href="./index.php?module=gallery">Galéria</a></li>
                            <!--<li><a<?= (($_GET['module'] == 'coupon' OR $_GET['module'] == 'coupon') ? ' class="selected"' : ''); ?> href="./index.php?module=coupon">Kupóny</a></li>-->
                            <li><a<?= ($_GET['module'] == 'translations' ? ' class="selected"' : ''); ?> href="./index.php?module=translations">Preklad</a>
                                <?
                                if (STATUS_NEWSLETTER != '0') {
                                    ?>
                                    <li><a<?= ($_GET['module'] == 'newsletter' ? ' class="selected"' : ''); ?> href="./index.php?module=newsletter">Newsletter</a></li>
                                    <?
                                }
                                if (STATUS_CAREER == '1') {
                                    ?>
                                    <li><a<?= ($_GET['module'] == 'career_uchadzaci' ? ' class="selected"' : ''); ?> href="./index.php?module=career_uchadzaci">Kariéra</a></li>
                                    <?
                                }
                                if (STATUS_SETTINGS == '1') {
                                    ?>
                                    <li><a<?= ($_GET['module'] == 'settings' ? ' class="selected"' : ''); ?> href="./index.php?module=settings">Nastavenia</a></li>
                                    <?
                                }
                                ?>
                        </ul>
                    </div>
                    <?
                    if (STATUS_ESHOP == '1') {
                        ?>
                        <div class="top-menu sub-menu">
                            <ul>
                                <li><a<?= (($_GET['module'] == 'eshop_product' OR $_GET['module'] == 'eshop_product_content') ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_product">Produkty</a></li>
                                <!--<li><a<?= ($_GET['module'] == 'eshop_gallery_product' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_gallery_product&amp;eshop=1">Fotogaléria produktov</a></li>-->
                                <li><a<?= ($_GET['module'] == 'eshop_orders' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_orders&amp;eshop=1">Objednávky</a></li>
                                <li><a<?= ($_GET['module'] == 'eshop_manufacturers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_manufacturers&amp;eshop=1">Výrobcovia</a></li>
                                <li><a<?= ($_GET['module'] == 'eshop_delivery_payment' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_delivery_payment&amp;eshop=1">Platba &amp; doručenie</a></li>
                                <li><a<?= ($_GET['module'] == 'eshop_supliers' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_supliers&amp;eshop=1">Dodávatelia</a></li>
                                <li><a<?= ($_GET['module'] == 'eshop_import' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&amp;eshop=1">Import</a></li>
                            </ul>
                        </div>
                        <?
                    }
                }
                ?>
            </div>
            <div id="content">
                <?
                Message::getMessage();
                if (file_exists("modules/_" . $_GET['module'] . ".php")) {
                    print $moduleContentSetup;
                } else {
                    require_once("modules/_404.php");
                }
                ?>
                <div class="clear"></div>
            </div>
            <div style="height: 33px;"></div>
            <div id="footer">V prípade potreby nás kontaktujte na telefónnom čísle + 421 55 72 87 533</div>
        </div>
    </body>
</html>