<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="' . fileWithLastChange('css/mod_account.css') . '" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
$js_file = ''; /* <script type="text/javascript" src="js/mod_xxc.js"></script> */
$footer_js_file = '';
$MODULE_HEADER = $css_file . $js_file;
$MODULE_FOOTER = $footer_js_file;
// definovanie potrebnych inline js action // uvadzat bez oznacenia <script>
$inline_js = ''; /* $(function() { $( "#datepicker" ).datepicker(); }); */
$MODULE_INLINE_JS = $inline_js;
// titulka (ak je prazdna, pouzije sa titulka zo struktury SQL)
$MODULE_TITLE = "";
// seo prvky daneho modulu
$MODULE_DESCRIPTION = "";
$MODULE_KEYWORDS = "";

// vykonanie akcii spojenych s odoslanim
// nacitanie obsahu modulu
ob_start();

// VYKONANIE AKCII
switch ($navigateArrayUrlWithoutBase[0]) {
    case "prihlasenie":
        if ($user->isAuthenticated()) {
            header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById($navigateId));
            exit;
        }
        ?>
        <div class="mod-account login">
            <div class="container">
                <h1><?= $cTranslator->getTranslation('Prihlásenie', 0); ?></h1>
                <div class="row">
                    <div class="col-12" style="display: flex; justify-content: center;">
                        <form id="login-form" name="login-form"  action="" method="post">
                            <label>
                                <span><?= $cTranslator->getTranslation('E-mail', 0); ?>: <strong>*</strong></span>
                                <input type="email" name="username"<?= ((isset($error) AND array_key_exists('fullname', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('fullname', $error)) ? '<small class="error">' . $error['fullname'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <span><?= $cTranslator->getTranslation('Heslo', 0); ?>: <strong>*</strong></span>
                                <input type="password" name="pwd" />
                                <?= ((isset($error) AND array_key_exists('fullname', $error)) ? '<small class="error">' . $error['fullname'] . '</small>' : ''); ?>
                            </label>
                            
                            <input type="hidden" name="form-action" value="login">
                            <div class="login-buttons">
                                <a href="<?= ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID); ?>/registracia" class="button invert"><?= $cTranslator->getTranslation('Registrácia', 0); ?></a>
                                <input type="submit" name="send" value="<?= $cTranslator->getTranslation('Prihlásiť', 0); ?>" class="submit-button" style="width: auto; min-width: 200px;" />
                            </div>
                            <div class="accout-tools text-end px-4">
                                <a href="<?= ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID); ?>/zabudnute-heslo"><?= $cTranslator->getTranslation('Zabudnuté heslo', 0); ?></a> 
                            </div>
                            <div class="clear"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <script language="javascript" type="text/javascript">
                var frmvalidator = new Validator("login-form");
                frmvalidator.addValidation("username", "maxlen=50", "<?= $cTranslator->getTranslation('Nezadali ste správnu dĺžku e-mailovej adresy', 0); ?>");
                frmvalidator.addValidation("username", "req", "<?= $cTranslator->getTranslation('Nezadali ste e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("username", "email", "<?= $cTranslator->getTranslation('Nezadali korektnú e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("pwd", "req", "<?= $cTranslator->getTranslation('Nezadali ste heslo', 0); ?>");
            </script>
        </div>
        <?
        break;
    case "registracia":
        if ($user->isAuthenticated()) {
            header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID));
            exit;
        }

        if ($_POST) {
            if (empty($_POST['fullname'])) {
                $error['fullname'] = $cTranslator->getTranslation("Nie je vyplnené celé meno.") . "<br />";
            }
            if (empty($_POST['mail'])) {
                $error['mail'] = $cTranslator->getTranslation("Nie je vyplnený e-mail.") . "<br />";
            }
            if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                $error['mail'] = $cTranslator->getTranslation("Nezadali ste korektnú e-mailovú adresu");
            }
            if (empty($_POST['pwd'])) {
                $error['pwd'] = $cTranslator->getTranslation("Nie je vyplnené heslo.") . "<br />";
            }
            if (empty($_POST['pwd_1'])) {
                $error['pwd_1'] = $cTranslator->getTranslation("Nie je vyplnené overenie hesla.") . "<br />";
            }
            if ($_POST['pwd'] != $_POST['pwd_1']) {
                $error['pwd_check'] = $cTranslator->getTranslation("Zadané hesla sa nezhodujú.") . "<br />";
            }

            if (empty($error)) {

                $find_user = mysql_query('SELECT user_id FROM ' . TABLE_PREFIX . 'user WHERE 1 AND mail="' . mysql_real_escape_string($_POST['mail']) . '";');
                if ($find_user) {
                    if (mysql_num_rows($find_user) == 0) {
                        $insert_sql = "INSERT INTO " . TABLE_PREFIX . "user (`fullname`, `username`, `pwd`, `mail`, `comment`, `active`, `newsletter`) VALUES ('" . $_POST['fullname'] . "', '" . $_POST['username'] . "', '" . md5($_POST['pwd']) . "', '" . $_POST['mail'] . "', '" . $_POST['comment'] . "', 1, '" . ((isset($_POST['newsletter'])) ? 1 : 0) . "');";
                        $insert_result = mysql_query($insert_sql);

                        if ($insert_result) {
                            Message::setMessage($cTranslator->getTranslation('Vaše konto bolo vytvorené.', 0), 0);
                            header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById($navigateId));
                            exit;
                        } else {
                            Message::setMessage($cTranslator->getTranslation('Nastalá Chyba! Vaše konto nebolo vytvorené.', 0), 2);
                            header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById($navigateId));
                            exit;
                        }
                    } else {
                        Message::setMessage($cTranslator->getTranslation('Zadaný e-mail je už spojený s iným účtom.', 0), 2);
                        header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById($navigateId));
                        exit;
                    }
                }
            } else {
                Message::setMessage($cTranslator->getTranslation('Nesprávne vyplnený formulár! Prosím skúste znova.', 0), 2);
            }
        }
        ?>
        <div class="mod-account registration">
            <div class="container">
                <h1><?= $cTranslator->getTranslation('Registrácia'); ?></h1>
                <div class="row">
                    <div class="col-12" style="display: flex; justify-content: center;">
                        <form id="registration-form" name="registration-form" method="post" action="">
                            <label>
                                <span><?= $cTranslator->getTranslation('Celé meno', 0); ?>: <strong>*</strong></span>
                                <input type="text" name="fullname" value="<?= $_POST['fullname']; ?>"<?= ((isset($error) AND array_key_exists('fullname', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('fullname', $error)) ? '<small class="error">' . $error['fullname'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <span><?= $cTranslator->getTranslation('E-mail', 0); ?>: <strong>*</strong></span>
                                <input id="mail" type="email" name="mail" value="<?= $_POST['mail']; ?>"<?= ((isset($error) AND array_key_exists('mail', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('mail', $error)) ? '<small class="error">' . $error['mail'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <span><?= $cTranslator->getTranslation('Heslo', 0); ?>: <strong>*</strong></span>
                                <input type="password" name="pwd" value="<?= $_POST['pwd']; ?>"<?= ((isset($error) AND ( array_key_exists('pwd', $error) OR array_key_exists('pwd_check', $error))) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('pwd', $error)) ? '<small class="error">' . $error['pwd'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <span><?= $cTranslator->getTranslation('Heslo znova', 0); ?>: <strong>*</strong></span>
                                <input type="password" name="pwd_1" value="<?= $_POST['pwd_1']; ?>"<?= ((isset($error) AND array_key_exists('pwd_1', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('pwd_1', $error)) ? '<small class="error">' . $error['pwd_1'] . '</small>' : ''); ?>
                            </label>
                            <label class="mb-4">
                                <span class="p-0"><?= $cTranslator->getTranslation('Newsletter', 0); ?>: <strong></strong></span>
                                <div class="d-flex" style="width: 370px; gap: 10px; align-items: start; padding-top: 8px;">
                                    <input type="checkbox" name="newsletter" value="1"<?= (isset($_POST['newsletter']) ? ($_POST['newsletter'] == 1 ? ' checked="checked"' : '' ) : ' checked="checked"' ) . ((isset($error) AND array_key_exists('newsletter', $error)) ? ' class="error"' : ''); ?> style="" />
                                    <div class="d-inline-block" style="line-height: 1.25;"><?= $cTranslator->getTranslation('Registráciou súhlasíte s použitím Vašich údajov na naše marketingové účely.', 0); ?></div>
                                </div>
                                <?= ((isset($error) AND array_key_exists('newsletter', $error)) ? '<small class="error">' . $error['newsletter'] . '</small>' : ''); ?>
                            </label>
                            <input type="submit" name="send" value="<?= $cTranslator->getTranslation('Registrovať', 0); ?>" class="submit-button" />
                            <div class="clear"></div>
                            <p><?= $cTranslator->getTranslation('Údaje označené * sú povinné.', 0); ?></p>
                        </form>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <script language="javascript" type="text/javascript">
                var frmvalidator = new Validator("registration-form");
                frmvalidator.addValidation("fullname", "req", "<?= $cTranslator->getTranslation('Nezadali ste Vaše celé meno', 0); ?>");
                frmvalidator.addValidation("mail", "maxlen=50", "<?= $cTranslator->getTranslation('Nezadali ste správnu dĺžku e-mailovej adresy', 0); ?>");
                frmvalidator.addValidation("mail", "req", "<?= $cTranslator->getTranslation('Nezadali ste e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("mail", "email", "<?= $cTranslator->getTranslation('Nezadali korektnú e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("pwd", "req", "<?= $cTranslator->getTranslation('Nezadali ste heslo', 0); ?>");
                frmvalidator.addValidation("pwd_1", "req", "<?= $cTranslator->getTranslation('Nezadali ste overenie hesla', 0); ?>");
                frmvalidator.setAddnlValidationFunction(pwdCompare);
                function pwdCompare() {
                    var frm = document.forms["registration-form"];
                    if (frm.pwd.value != frm.pwd_1.value) {
                        sfm_show_error_msg("Zadané hesla sa nezhodujú!", frm.pwd);
                        return false;
                    } else {
                        return true;
                    }
                }
            </script>
        </div>
        <?
        break;
    case "uprava-profilu":
        if (!$user->isAuthenticated()) {
            header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID) . "/registracia");
            exit;
        }

        if (!empty($_POST) AND $_POST['action'] == "update") {

            $queryString = "update " . TABLE_PREFIX . "user SET `username`='" . $_POST['mail'] . "', `mail`='" . $_POST['mail'] . "', `pwd`='" . $_POST['pwd'] . "', `newsletter`='" . ((isset($_POST['newsletter'])) ? 1 : 0) . "', `comment`='" . $_POST['comment'] . "', `fullname`='" . $_POST['fullname'] . "', `pevna_linka`='" . $_POST['pevna_linka'] . "', `ulica_cislo`='" . $_POST['ulica_cislo'] . "', `psc`='" . $_POST['psc'] . "', `mesto`='" . $_POST['mesto'] . "', `krajina`='" . $_POST['krajina'] . "' WHERE user_id=" . mysql_real_escape_string($_SESSION['user_id']);
            $ResultS = mysql_query($queryString);

            $queryString = "update " . TABLE_PREFIX . "user_profil SET `company_name`='" . $_POST['company_name'] . "', `ulica_cislo_f`='" . $_POST['ulica_cislo_f'] . "', `mesto_f`='" . $_POST['mesto_f'] . "', `psc_f`='" . $_POST['psc_f'] . "', `telefon_f`='" . $_POST['telefon_f'] . "', `mail_f`='" . $_POST['mail_f'] . "', `company_description`='" . $_POST['company_description'] . "', `web`='" . $_POST['web'] . "' WHERE id= (SELECT profil_id FROM " . TABLE_PREFIX . "user WHERE user_id=" . mysql_real_escape_string($_SESSION['user_id']) . ");";
            $ResultUP = mysql_query($queryString);

            if ($ResultS && $ResultUP) {
                header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID) . "/uprava-profilu");
                exit;
            } else {
                echo mysql_error();
            }
        }

        if ($_POST) {
            if (empty($_POST['fullname'])) {
                $error['fullname'] = $cTranslator->getTranslation("Nie je vyplnené celé meno.") . "<br />";
            }
            if (empty($_POST['mail'])) {
                $error['mail'] = $cTranslator->getTranslation("Nie je vyplnený e-mail.") . "<br />";
            }
            if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                $error['mail'] = $cTranslator->getTranslation("Nezadali ste korektnú e-mailovú adresu");
            }
            if (!empty($_POST['pwd']) AND ! empty($_POST['pwd_1'])) {
                if ($_POST['pwd'] != $_POST['pwd_1']) {
                    $error['pwd_check'] = $cTranslator->getTranslation("Zadané hesla sa nezhodujú.") . "<br />";
                }
            }

            if (empty($error)) {
                $update_sql = "UPDATE " . TABLE_PREFIX . "user SET
                                    `fullname`='" . $_POST['fullname'] . "',
                                    " . ((!empty($_POST['pwd']) AND ! empty($_POST['pwd_1'])) ? '`pwd`="' . $_POST['pwd'] . '", ' : '' ) . "
                                    `mail`='" . $_POST['mail'] . "',
                                    `newsletter`='" . ((isset($_POST['newsletter'])) ? 1 : 0) . "'
                              WHERE 1 AND user_id='" . mysql_real_escape_string($_SESSION['user_id']) . "';";
                $update_result = mysql_query($update_sql);

                if ($update_result) {
                    Message::setMessage($cTranslator->getTranslation('Vaše konto bolo upravené.', 0), 2);
                    header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById($navigateId));
                    exit;
                } else {
                    Message::setMessage($cTranslator->getTranslation('Nastalá Chyba! Vaše konto nebolo upravené.', 0), 0);
                    header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById($navigateId));
                    exit;
                }
            } else {
                Message::setMessage($cTranslator->getTranslation('Nesprávne vyplnený formulár! Prosím skúste znova.', 0), 0);
            }
        }

        // zistime si udaje do formulara
        $form_sql = "SELECT * FROM " . TABLE_PREFIX . "user WHERE 1 AND user_id='" . $_SESSION['user_id'] . "';";
        $form_data = mysql_query($form_sql);
        $_POST = mysql_fetch_assoc($form_data);
        ?>
        <div class="mod-account profile-update">
            <div class="container">
                <h1><?= $cTranslator->getTranslation('Úprava profilu', 0); ?></h1>
                <div class="row">
                    <div class="col-12" style="display: flex; justify-content: center;">
                        <form id="registration-form" name="registration-form" method="post" action="">
                            <label>
                                <span><?= $cTranslator->getTranslation('Celé meno', 0); ?>: <strong>*</strong></span>
                                <input type="text" name="fullname" value="<?= $_POST['fullname']; ?>"<?= ((isset($error) AND array_key_exists('fullname', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('fullname', $error)) ? '<small class="error">' . $error['fullname'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <span><?= $cTranslator->getTranslation('E-mail', 0); ?>: <strong>*</strong></span>
                                <input id="mail" type="email" name="mail" value="<?= $_POST['mail']; ?>"<?= ((isset($error) AND array_key_exists('mail', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('mail', $error)) ? '<small class="error">' . $error['mail'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <span><?= $cTranslator->getTranslation('Heslo', 0); ?>: <strong></strong></span>
                                <input type="password" name="pwd" value="<?= $_POST['pwd']; ?>"<?= ((isset($error) AND ( array_key_exists('pwd', $error) OR array_key_exists('pwd_check', $error))) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('pwd', $error)) ? '<small class="error">' . $error['pwd'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <span><?= $cTranslator->getTranslation('Heslo znova', 0); ?>: <strong></strong></span>
                                <input type="password" name="pwd_1" value="<?= $_POST['pwd_1']; ?>"<?= ((isset($error) AND array_key_exists('pwd_1', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('pwd_1', $error)) ? '<small class="error">' . $error['pwd_1'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <span><?= $cTranslator->getTranslation('Newsletter', 0); ?>: <strong></strong></span>
                                <input type="checkbox" name="newsletter" value="1"<?= (($_POST['newsletter']) ? ' checked="checked"' : '') . ((isset($error) AND array_key_exists('newsletter', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('newsletter', $error)) ? '<small class="error">' . $error['newsletter'] . '</small>' : ''); ?>
                            </label>
                            <input type="submit" name="send" value="<?= $cTranslator->getTranslation('Registrovať', 0); ?>" class="submit-button" />
                            <div class="clear"></div>
                            <p><?= $cTranslator->getTranslation('Údaje označené * sú povinné.', 0); ?></p>
                        </form>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <script language="javascript" type="text/javascript">
                var frmvalidator = new Validator("registration-form");
                frmvalidator.addValidation("fullname", "req", "<?= $cTranslator->getTranslation('Nezadali ste Vaše celé meno', 0); ?>");
                frmvalidator.addValidation("mail", "maxlen=50", "<?= $cTranslator->getTranslation('Nezadali ste správnu dĺžku e-mailovej adresy', 0); ?>");
                frmvalidator.addValidation("mail", "req", "<?= $cTranslator->getTranslation('Nezadali ste e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("mail", "email", "<?= $cTranslator->getTranslation('Nezadali korektnú e-mailovú adresu', 0); ?>");
                frmvalidator.setAddnlValidationFunction(pwdCompare);
                function pwdCompare() {
                    var frm = document.forms["registration-form"];
                    if (frm.pwd.value != "" && frm.pwd_1.value != "") {
                        if (frm.pwd.value != frm.pwd_1.value) {
                            sfm_show_error_msg("Zadané hesla sa nezhodujú!", frm.pwd);
                            return false;
                        } else {
                            return true;
                        }
                    } else {
                        return true;
                    }
                }
            </script>
        </div>
        <?
        break;
    case "newsletter-odhlasenie":
        $users_mail = array_reverse($navigateArrayUrlWithoutBase);
        echo '<div class="mod-account newsletter-unsubscribe">';
        echo '<h1>' . $cTranslator->getTranslation('Odhlásenie z newslettra') . '</h1>';

        if ($_GET AND isset($_GET['uid'])) {
            if (isset($_GET['uid'])) { // ctype_alpha($_GET['uid'])
                $uid = explode(';', $_GET['uid']);
                foreach ($uid as $key => $value) {
                    $find_user = mysql_query('SELECT user_id FROM ' . TABLE_PREFIX . 'user WHERE 1 AND IF(mail IS NULL, SHA1(CONCAT("email_message_class", username)), SHA1(CONCAT("email_message_class", mail)))="' . mysql_real_escape_string($users_mail[0]) . '" AND MD5(user_id)="' . mysql_real_escape_string($value) . '" AND newsletter="1";');
                    if (mysql_num_rows($find_user) == '1') {
                        $result = mysql_fetch_assoc($find_user);
                        $unsubscribe_users[] = $result['user_id'];
                        mysql_query('UPDATE ' . TABLE_PREFIX . 'user SET newsletter="0" WHERE user_id="' . $result['user_id'] . '"');
                    }
                }
                header('Location: ' . ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID) . '/newsletter-odhlasenie-uspesne');
                exit;
            } else {
                echo '<p>' . $cTranslator->getTranslation('Odhlásenie nebolo možné vykonať! Boli odoslané nesprávne údaje.', 0) . '</p>';
            }
        } else {
            $find_user = mysql_query('SELECT user_id FROM ' . TABLE_PREFIX . 'user WHERE 1 AND IF(mail IS NULL, SHA1(CONCAT("email_message_class", username)), SHA1(CONCAT("email_message_class", mail)))="' . mysql_real_escape_string($users_mail[0]) . '" AND newsletter="1";');
            if (!$find_user) {
                echo mysql_query();
            } else {
                if (mysql_num_rows($find_user) != 0) {
                    $uid = array();
                    while ($user_data = mysql_fetch_assoc($find_user)) {
                        $uid[] = md5($user_data['user_id']);
                    }
                    echo '<p>' . $cTranslator->getTranslation('Ak sa chcete odhlásiť z newslettra kliknite na tlačidlo "Odhlásiť sa z odberu".', 0) . '</p>';
                    echo '<br />';
                    echo '<p><a class="unsubscribe-link" href="' . ROOTDIR . '/' . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID) . '/newsletter-odhlasenie/' . $users_mail[0] . '?uid=' . implode(';', $uid) . '">' . $cTranslator->getTranslation('Odhlásiť sa z odberu', 0) . '</a></p>';
                } else {
                    echo '<p>' . $cTranslator->getTranslation('Zadaná e-mailová adresa nebolá nájdená v databáze pre odosielanie newslettrov.', 0) . '</p>';
                }
            }
        }
        echo '</div>';
        break;
    case "newsletter-odhlasenie-uspesne":
        echo '<div class="mod-account newsletter-unsubscribe success">';
        echo '<h1>' . $cTranslator->getTranslation('Odhlásenie z newslettra') . '</h1>';
        echo '<p>' . $cTranslator->getTranslation('Boli ste úspešne odhlásený z odberu.') . '</p>';
        echo '</div>';
        break;
    case "newsletter-chyba-odhlasenia":
        echo '<div class="mod-account newsletter-unsubscribe error">';
        echo '<h1>' . $cTranslator->getTranslation('Odhlásenie z newslettra') . '</h1>';
        echo '<p>' . $cTranslator->getTranslation('Zadaná e-mailová adresa nebolá nájdená v databáze pre odosielanie newslettrov.') . '</p>';
        echo '</div>';
        break;
    case "zabudnute-heslo":
        include_once("captcha.php");
        $_SESSION['captcha'] = simple_php_captcha();

        if ($_POST) {
            if (!filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)) {
                $error['mail'] = $cTranslator->getTranslation("Nezadali ste korektnú e-mailovú adresu", 0);
            }
            if (empty($_POST['mail'])) {
                $error['mail'] = $cTranslator->getTranslation("Nezadali ste e-mail", 0);
            }
            if (empty($_POST['check'])) {
                $error['check'] = $cTranslator->getTranslation("Nezadali ste text z obrázka", 0);
            }
            if ($_POST['check'] != $_POST['ottk']) {
                $error['pwd_check'] = $cTranslator->getTranslation("Nesprávne opísany text z obrázka!", 0);
            }

            if (empty($error)) {
                $find_user = mysql_query('SELECT user_id FROM ' . TABLE_PREFIX . 'user WHERE 1 AND mail="' . mysql_real_escape_string($_POST['mail']) . '" AND active="1";');
                if (!$find_user) {
                    echo mysql_query();
                } else {
                    if (mysql_num_rows($find_user) != 0) {
                        $user_data = mysql_fetch_assoc($find_user);
                        $new_password = $user->randomGenerator(6);
                        $update_sql = 'UPDATE ' . TABLE_PREFIX . 'user SET pwd="' . $new_password . '"
                                       WHERE 1 AND user_id="' . mysql_real_escape_string($user_data['user_id']) . '";';
                        $update_result = mysql_query($update_sql);

                        if ($update_result) {

                            require_once("shared/classes/class.mail.php");

                            $email_message = new email_message_class;
                            $email_message->SetEncodedEmailHeader("From", $fromAddress, $fromName);
                            $email_message->SetEncodedEmailHeader("Reply-To", $fromAddress, $fromName);
                            $email_message->SetHeader("Sender", $fromAddress);
                            $email_message->SetHeader("Subject", "Obnova hesla");
                            $email_message->AddHTMLPart("Vaše nové prihlasovacie údaje na stránke " . PROJECT_NAME . " sú:<br /><br />
		                             Vasa prihlasovacia adresa: " . $_POST['mail'] . "<br />
		                             Vase nove heslo: " . $new_password . "", "utf-8");
                            // email klientovi
                            $email_message->SetEncodedEmailHeader("To", $_POST['mail'], $_POST['mail']);
                            $email_message->Send();

                            Message::setMessage($cTranslator->getTranslation('Vaše heslo bolo obnovené a odoslané na zadaný e-mail', 0), 0);
                            header("Location: " . ROOTDIR);
                            exit;
                        } else {
                            Message::setMessage($cTranslator->getTranslation('Nastalá Chyba! Vaše heslo nebolo obnovené', 0), 2);
                            header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById($navigateId) . '/zabudnete-heslo');
                            exit;
                        }
                    } else {
                        Message::setMessage($cTranslator->getTranslation('Zadaná e-mailová adresa nebolá nájdená v databáze', 0), 2);
                    }
                }
            }
        }
        ?>
        
        <div class="narrow-content">
            <div class="container">
                <h1><?= $cTranslator->getTranslation('Zabudnuté heslo', 0); ?></h1>
                <div class="row">
                    <div class="col-12" style="display: flex; justify-content: center;">            
                        <form id="reset-password-form" name="reset-password-form"  action="" method="post">
                            <label>
                                <span><?= $cTranslator->getTranslation('E-mail', 0); ?>: <strong>*</strong></span>
                                <input type="email" name="mail"<?= ((isset($error) AND array_key_exists('mail', $error)) ? ' class="error"' : ''); ?> />
                                <?= ((isset($error) AND array_key_exists('mail', $error)) ? '<small class="error">' . $error['mail'] . '</small>' : ''); ?>
                            </label>
                            <label>
                                <img alt="<?=SEO_TITLE?>" src="<?= $_SESSION['captcha']['image_src']; ?>" />
                                <input type="text" name="check"<?= ((isset($error) AND array_key_exists('check', $error)) ? ' class="error"' : ''); ?> />
                                <input type="hidden" name="ottk" value="<?= $_SESSION['captcha']['code']; ?>" />
                                <?= ((isset($error) AND array_key_exists('check', $error)) ? '<small class="error">' . $error['check'] . '</small>' : ''); ?>
                            </label>
                            <input type="submit" name="send" value="<?= $cTranslator->getTranslation('Odoslať', 0); ?>" class="submit-button" />
                            <div class="clear"></div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="clear"></div>
            <script language="javascript" type="text/javascript">
                var frmvalidator = new Validator("reset-password-form");
                frmvalidator.addValidation("mail", "maxlen=50", "<?= $cTranslator->getTranslation('Nezadali ste správnu dĺžku e-mailovej adresy', 0); ?>");
                frmvalidator.addValidation("mail", "req", "<?= $cTranslator->getTranslation('Nezadali ste e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("mail", "email", "<?= $cTranslator->getTranslation('Nezadali korektnú e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("check", "req", "<?= $cTranslator->getTranslation('Nezadali ste text z obrázka', 0); ?>");
                frmvalidator.addValidation("check", "eqelmnt=ottk", "<?= $cTranslator->getTranslation('Nesprávne opísany text z obrázka!', 0); ?>");
            </script>
        </div>
        <?
        break;
    case "profil":
    default:
        if (!$user->isAuthenticated()) {
            header("Location: " . ROOTDIR . "/" . Menu::getHyperlinkById(USER_ACCOUNT_MANAGE_ID) . "/registracia");
            exit;
        }

        echo '<div class="mod-account profile">';

        // zobrazime profil uzivatela
        $profile_sql = "SELECT mail, fullname, newsletter FROM " . TABLE_PREFIX . "user WHERE 1 AND user_id=" . $_SESSION['user_id'] . ";";
        $profile_select = mysql_query($profile_sql);
        if ($profile_select) {
            $profile_detail = mysql_fetch_assoc($profile_select);
            ?>            
            <div class="profile-detail">
                <div class="container">
                    <h1><?= $cTranslator->getTranslation('Profil', 0); ?></h1>
                    <div class="row">
                        <div class="col-12" style="display: flex; justify-content: center;">
                            <p>
                                <strong><?= $cTranslator->getTranslation('Celé meno', 0); ?>:</strong> <?= $profile_detail['fullname']; ?><br />
                                <strong><?= $cTranslator->getTranslation('E-mail', 0); ?>:</strong> <?= $profile_detail['mail']; ?><br />
                                <strong><?= $cTranslator->getTranslation('Newsleter', 0); ?>:</strong> <?= ($profile_detail['newsletter'] == 1 ? $cTranslator->getTranslation('Prihlásený na odber', 0) : $cTranslator->getTranslation('Neprihlásený na odber', 0)); ?><br />
                            </p>
                            <div class="edit-link">
                                <a href="<?= ROOTDIR . '/' . Menu::getHyperlinkById($navigateId); ?>/uprava-profilu"><?= $cTranslator->getTranslation('Upraviť profil', 0); ?></a>
                            </div>
                            <div class="edit-link">
                                <img src="images/icons/logout.gif" alt="edit" /> <a href="<?= ROOTDIR; ?>/logout"><?= $cTranslator->getTranslation('Odhlásenie', 0); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?
        } else {
            echo(mysql_error());
        }
        @mysql_free_result($ResultQ);

        echo '</div>';
}

$moduleContent = ob_get_contents();
ob_clean();
?>