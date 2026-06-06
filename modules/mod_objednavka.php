<?

// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_objednavka.css" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
$js_file = ''; /* <script type="text/javascript" src="js/mod_xxc.js"></script> */
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
    case "insert":
        break;

    case "edit":
        break;

    case "detail":
        break;

    case "delete":
        break;
    default:

        // vykonanie akcii spojenych s odoslanim
        if (isset($_POST['send'])) {
            if (empty($_POST["Tovar"]))
                $error .= $cTranslator->getTranslation("Nevybrali ste tovar.") . "<br>";
            if (empty($_POST["Mnozstvo"]))
                $error .= $cTranslator->getTranslation("Nevybrali ste množstvo.") . "<br>";
            if (empty($_POST["Name"]))
                $error .= $cTranslator->getTranslation("Nie je vyplnene meno.") . "<br>";
            if (empty($_POST["Email"]))
                $error .= $cTranslator->getTranslation("Nie je vyplneny email.") . "<br>";
            if (empty($_POST["Message"]))
                $error .= $cTranslator->getTranslation("Nie je vyplnená sprava.") . "<br>";

            if (empty($error)) {
                $message .= sprintf("Tovar: %s\r\n", $_POST['Tovar']);
                $message .= sprintf("Množstvo: %s\r\n", $_POST['Mnozstvo']);
                $message .= sprintf("Meno odosielatela: %s\r\n", $_POST['Name']);
                $message .= sprintf("Mail odosielatela: %s\r\n", $_POST['Email']);
                $message .= sprintf("Telefón odosielatela: %s\r\n", $_POST['Phone']);
                $message .= sprintf("Správa: %s\r\n", $_POST['Message']);

                $headers = "Content-Type: text/plain; charset=utf-8\r\n";
                $headers .= "From: " . String::diakritika($_POST["Name"]) . " <" . String::diakritika($_POST["Email"]) . ">\r\n";
                $headers .= "X-Mailer: PHP Engine\r\n";
                foreach ($emailAddress as $k => $v):
                    mail($v, "Kontakt - " . PROJECT_NAME . " - " . strtoupper($lang), String::diakritika($message), $headers);
                endforeach;
                mail($_POST["Email"], "Kontakt - " . PROJECT_NAME, String::diakritika($message), $headers);

                $error .= $cTranslator->getTranslation("Kontaktny formular bol uspesne odoslany.");
                echo message($error);
                break;
            }
        }

        echo '<div id="content">
	                <div id="page-wrap">
                        <h1>Objednávka</h1><div class="clear"></div>
                        <div id="contact_left"></div>
                        <div id="contact_right">' . ( (!empty($error)) ? '' . message($error) . '' : '') . '
                            <div id="contact-area">
            			<form name="form" method="post" action="" class="contactform">
                            <label>Tovar: *</label>
                            <input type="text" name="Tovar"></input><br>
                            <label>Množstvo: *</label>
                            <input type="number" name="Mnozstvo"></input><br>
                           	<label for="Name">Vaše meno: *</label>
            				<input type="text" name="Name" id="Name" />
            				<label for="Phone">Telefón:</label>
            				<input type="text" name="Phone" id="Phone" />
            				<label for="Email">E-mail: *</label>
            				<input type="text" name="Email" id="Email" />
            				<label for="Message">Správa: *</label><br />
            				<textarea name="Message" rows="20" cols="20" id="Message"></textarea>
            				<input type="submit" name="send" value="Odoslať" class="submit-button" />
            		 	</form>
	              <p style="text-align:center">Údaje označené * sú povinné.</p>
                     <div style="clear: both;"></div>
                 </div>
             </div>
        </div>
  </div>';
        ?>

        <script language="javascript" type="text/javascript">
            var frmvalidator = new Validator("form");
            frmvalidator.addValidation("Email", "maxlen=50", "Nezadali ste správnu dĺžku e-mailovej adresy");
            frmvalidator.addValidation("Email", "req", "Nezadali ste e-mailovú adresu");
            frmvalidator.addValidation("Email", "email", "Nezadali korektnú e-mailovú adresu");
            frmvalidator.addValidation("Name", "req", "Nezadali ste Vaše meno a priezvisko");
            frmvalidator.addValidation("Message", "req", "Nezadali ste správu, ktorú chcete odoslať");
            DoPassValidation();
        </script>

    <?

}

$moduleContent = ob_get_contents();
ob_clean();
?>