<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_kontakt.css" />';
$js_file = '';
$js_file .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language=sk"></script>' . n;
$js_file .= '<script type="text/javascript" src="js/gmap3/gmap3.min.js"></script>' . n;
$footer_js_file = '<script type="text/javascript" src="js/gmap3/init.js"></script>' . n;
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

// nacitanie obsahu modulu
ob_start();

// VYKONANIE AKCII

// vykonanie akcii spojenych s odoslanim
        if (isset($_POST['send'])) {

            $error = array();
            if (empty($_POST["Name"])) {
                $error['Name'] = $cTranslator->getTranslation("Nezadali ste Vaše meno a priezvisko");
            }
            if (!filter_var($_POST['Email'], FILTER_VALIDATE_EMAIL)) {
                $error['Email'] = $cTranslator->getTranslation("Nezadali korektnú e-mailovú adresu");
            }
            if (empty($_POST["Email"])) {
                $error['Email'] = $cTranslator->getTranslation("Nezadali ste e-mailovú adresu");
            }
            if (empty($_POST["Message"])) {
                $error['Message'] = $cTranslator->getTranslation("Nezadali ste správu, ktorú chcete odoslať");
            }

            if (empty($error)) {
                $subject = PROJECT_TITLE . " - Správa z kontaktného formulára";
                $body = sprintf("Meno odosielatela: %s\r\n", $_POST['Name']);
                $body .= sprintf("Mail odosielatela: %s\r\n", $_POST['Email']);
                if (!empty($_POST['Phone']))
                    $body .= sprintf("Telefón odosielatela: %s\r\n", $_POST['Phone']);
                $body .= sprintf("Správa: %s\r\n", $_POST['Message']);

                if (!empty($_FILES['Attachment']['name']))
                    $file = $_FILES['Attachment'];
                else
                    $file = '';

                $response_body = $cTranslator->getTranslation("Vazeny zakaznik,", 0) . "\n\n";
                $response_body .= $cTranslator->getTranslation("dakujeme za vyplnenie kontaktneho formulara. Vasa sprava bola odoslana a dorucena zodpovednym zamestnancom. Vasou spravou sa budeme co najskor zaoberat a odpoved Vam zasleme mailom hned ako to bude mozne.", 0) . "\n\n";
                $response_body .= $cTranslator->getTranslation("S pozdravom", 0) . "\n";
                $response_body .= $cTranslator->getTranslation("Tim fashionitalia .sk", 0);

                $response = array(
                    'subject' => 'Potvrdenie odoslania kontaktného formulára',
                    'body' => $response_body);

                // sendMail::send($toName, $sendTo, $subject, $body, $attachment = NULL, $fromEmail = NULL, $fromEmailName = NULL, $response = NULL)
                // sendMail::send($_POST['Name'], $_POST['Email'], $subject, $body, $file, NULL, NULL, $response);

                foreach ($emailAddress as $v) {
                    sendMail::send($fromName, $v, $subject, $body, $file);
                }
            } else {
                Message::setMessage($cTranslator->getTranslation('Nesprávne vyplnený formulár! Prosím skúste znova.', 0), 2);
            }
        }
?>
<div class="container">
    <h1><span><?= Menu::getHyperLinkTextById($navigateId); ?></span></h1>
    <div id="content" class="row">
        <div class="content col-md-5 col-md-offset-1">
            <?
            echo html_entity_decode($Row['content'], ENT_QUOTES, "UTF-8");
            if ($user->isAdmin()) {
                ?>
                <div class="edit-link">
                    <a href="#" onclick="javascript:openPopupWindow('content', <?= $Row["menu_id"]; ?>, 900, 600); return false;">
                        <span class="translation_edit" rel="<?= $cTranslator->getTranslation('Upraviť obsah', 0); ?>"></span>
                        <?= $cTranslator->getTranslation('Upraviť obsah', 0); ?>
                    </a>
                </div>
                <?
            }
            ?>
        </div>

        <div class="contact-form col-md-6">
            <form name="form" method="post" action="" class="contactform form-horizontal" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="Name" class="col-sm-3 control-label">
                        <span class="req"><?= $cTranslator->getTranslation('Vaše meno', 0); ?>:</span>
                    </label>
                    <div class="col-sm-9">
                        <input type="text" name="Name" id="Name" class="form-control<?= ((isset($error) AND array_key_exists('Name', $error)) ? ' error' : ''); ?>" />
                        <?= ((isset($error) AND array_key_exists('Name', $error)) ? '<small class="error">' . $error['Name'] . '</small>' : ''); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="Phone" class="col-sm-3 control-label">
                        <span><?= $cTranslator->getTranslation('Telefón', 0); ?>:</span>
                    </label>
                    <div class="col-sm-9">
                        <input type="text" name="Phone" id="Phone" class="form-control<?= ((isset($error) AND array_key_exists('Phone', $error)) ? ' error' : ''); ?>" />
                        <?= ((isset($error) AND array_key_exists('Phone', $error)) ? '<small class="error">' . $error['Phone'] . '</small>' : ''); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="Email" class="col-sm-3 control-label">
                        <span class="req"><?= $cTranslator->getTranslation('E-mail', 0); ?>:</span>
                    </label>
                    <div class="col-sm-9">
                        <input type="text" name="Email" id="Email" class="form-control<?= ((isset($error) AND array_key_exists('Email', $error)) ? 'error' : ''); ?>" />
                        <?= ((isset($error) AND array_key_exists('Email', $error)) ? '<small class="error">' . $error['Email'] . '</small>' : ''); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="Message" class="col-sm-3 control-label">
                        <span class="req"><?= $cTranslator->getTranslation('Správa', 0); ?>:</span>
                    </label>
                    <div class="col-sm-9">
                        <textarea name="Message" rows="6" id="Message" class="form-control<?= ((isset($error) AND array_key_exists('Message', $error)) ? ' error' : ''); ?>"></textarea>
                        <?= ((isset($error) AND array_key_exists('Message', $error)) ? '<small class="error">' . $error['Message'] . '</small>' : ''); ?>
                    </div>
                </div>
                <p class="footnote col-sm-offset-3 col-sm-9"><?= str_replace('*', '<span class="dnt">*</span>', $cTranslator->getTranslation('Údaje označené * sú povinné.', 0)); ?></p>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <button type="submit" name="send"><?= $cTranslator->getTranslation('Odoslať', 0); ?></button>
                    </div>
                </div>
            </form>
            <script language="javascript" type="text/javascript">
                var frmvalidator = new Validator("form");
                frmvalidator.addValidation("Email", "maxlen=50", "<?= $cTranslator->getTranslation('Nezadali ste správnu dĺžku e-mailovej adresy', 0); ?>");
                frmvalidator.addValidation("Email", "req", "<?= $cTranslator->getTranslation('Nezadali ste e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("Email", "email", "<?= $cTranslator->getTranslation('Nezadali korektnú e-mailovú adresu', 0); ?>");
                frmvalidator.addValidation("Name", "req", "<?= $cTranslator->getTranslation('Nezadali ste Vaše meno a priezvisko', 0); ?>");
                frmvalidator.addValidation("Message", "req", "<?= $cTranslator->getTranslation('Nezadali ste správu, ktorú chcete odoslať', 0); ?>");
                frmvalidator.addValidation("Phone", "num", "Nezadali ste správny formát čísla");
            </script>
        </div>
        <div class="gmap col-md-12">
            <!--<iframe src="https://www.google.com/maps/embed?key=AIzaSyCldrgT9bYG7nC2r4FMzDsEpHZB2PEvUUE&amp;pb=!1m18!1m12!1m3!1d2632.8047679682977!2d21.24485731604167!3d48.70921241915226!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x473ee012e494bccb%3A0x4c8915c1de0ff82!2sDunajsk%C3%A1+12%2C+040+01+Ko%C5%A1ice!5e0!3m2!1ssk!2ssk!4v1481115626249" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>-->
        </div>
    </div>
</div>



<?
$moduleContent = ob_get_contents();
ob_clean();
?>