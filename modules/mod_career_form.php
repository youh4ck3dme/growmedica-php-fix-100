<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = ''; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
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

// vykonanie akcii spojenych s odoslanim

if ($_GET['pozicia']) {
    $queryStringP = "select sk_name from " . TABLE_PREFIX . "article where 1 and article_id = '" . $_GET['pozicia'] . "';";
    if (!$ResultP = mysql_query($queryStringP)) {
        if (mysql_errno())
            print("MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />");
    }else {
        if (mysql_num_rows($ResultP) == 1) {
            $RowP = mysql_fetch_assoc($ResultP);
        }
    }
}

if ($_POST) {
    if ($_POST['akcia'] == "odoslat" && $_POST['botprotect'] == 'ok') {
        if (empty($_POST['priezvisko']))
            $error .= $cTranslator->getTranslation("Nie je vyplnené priezvisko.") . '<br />';
        if (empty($_POST['meno']))
            $error .= $cTranslator->getTranslation("Nie je vyplnené meno.") . '<br />';
        if (empty($_POST['narodenie']))
            $error .= $cTranslator->getTranslation("Nie je vyplnený dátum narodenia.") . '<br />';
        if (empty($_POST['email']))
            $error .= $cTranslator->getTranslation("Nie je vyplnený email.") . '<br />';

        if (!empty($_POST['oblast'])) {
            $oblast2 = implode(',', $_POST['oblast']);
        }

        if (empty($error)) {

            $body = "Tieto údaje boli poskytnute ako odpoved na inzerat " . $RowP['sk_name'] . "\r\n\n";
            $body .= "OSOBNE UDAJE:\r\n";
            $body .= "" . $_POST['priezvisko'] . " " . $_POST['meno'] . " " . $_POST['titul'] . "\r\n";
            $body .= "narodeny: " . $_POST['narodenie'] . "\r\n";
            $body .= "statna prislusnost: " . $_POST['stat'] . "\r\n\n";

            $body .= "KONTAKTNE UDAJE:\r\n";
            $body .= "email: " . $_POST['email'] . "\r\n";
            $body .= "telefon domov: " . $_POST['teldomu'] . "\r\n";
            $body .= "telefon do prace: " . $_POST['telprace'] . "\r\n";
            $body .= "mobil: " . $_POST['mobil'] . "\r\n";
            $body .= "fax: " . $_POST['fax'] . "\r\n\n";

            $body .= "ADRESA:\r\n";
            $body .= "" . $_POST['adresa'] . "\r\n\n";

            $body .= "OBLAST ZAUJMU:\r\n";
            $body .= "" . $oblast2 . "\r\n";
            $body .= "Vo funkcii: " . $_POST['funkcia'] . "\r\n\n";

            $body .= "VZDELANIE:\r\n";
            $body .= "Skola 1: " . $_POST['skola1'] . " ----- od - do: " . $_POST['skod1'] . " ----- specializacia: " . $_POST['spec1'] . "\r\n";
            $body .= "Skola 2: " . $_POST['skola2'] . " ----- od - do: " . $_POST['skod2'] . " ----- specializacia: " . $_POST['spec2'] . "\r\n";
            $body .= "Skola 3: " . $_POST['skola3'] . " ----- od - do: " . $_POST['skod3'] . " ----- specializacia: " . $_POST['spec3'] . "\r\n";
            $body .= "Skola 4: " . $_POST['skola4'] . " ----- od - do: " . $_POST['skod4'] . " ----- specializacia: " . $_POST['spec4'] . "\r\n\n";

            $body .= "PRAX:\r\n";
            $body .= "Zamestnanie1: " . $_POST['zam1'] . " ---- od - do: " . $_POST['zod1'] . " ---- zaradenie: " . $_POST['zarad1'] . " ---- napln: " . $_POST['napln1'] . "\r\n";
            $body .= "Zamestnanie2: " . $_POST['zam2'] . " ---- od - do: " . $_POST['zod2'] . " ---- zaradenie: " . $_POST['zarad2'] . " ---- napln: " . $_POST['napln2'] . "\r\n";
            $body .= "Zamestnanie3: " . $_POST['zam3'] . " ---- od - do: " . $_POST['zod3'] . " ---- zaradenie: " . $_POST['zarad3'] . " ---- napln: " . $_POST['napln3'] . "\r\n";
            $body .= "Zamestnanie4: " . $_POST['zam4'] . " ---- od - do: " . $_POST['zod4'] . " ---- zaradenie: " . $_POST['zarad4'] . " ---- napln: " . $_POST['napln4'] . "\r\n\n";

            $body .= "JAZYK:\r\n";
            $body .= "Anglicky: " . $_POST['anglictina'] . "\r\n";
            $body .= "" . $_POST['jazyk2'] . " " . $_POST['jazyk'] . "\r\n\n";

            $body .= "DOPLNUJUCE INFORMACIE:\r\n";
            $body .= "Znalosti: " . $_POST['znalSW'] . "\r\n";
            $body .= "Ine: " . $_POST['ine'] . "\r\n";
            $body .= "Datum nastupu: " . $_POST['datumnast'] . "\r\n";
            $body .= "Rozne:  " . $_POST['rozne'];

           
            $subject = PROJECT_TITLE . " - Registracia uchadzaca o zamestnanie";
            $response = array(
                'subject' => PROJECT_TITLE . " - Potvrdenie registracia uchadzaca o zamestnanie",
                'body' => getContentByLabel('Potvrdenie odoslania registračného formulára.', 0) . "\n\n\n" . $body,
                'success_message' => $cTranslator->getTranslation("Formulár bol úspešne odoslaný.", 0),
                'error_message' => $cTranslator->getTranslation("Nastala chyba! Kontaktný formulár nebol odoslaný. Prosím skúste znova.", 0)
            );
            //
            // sendMail::send($toName, $sendTo, $subject, $body, $attachment = NULL, $fromEmail = NULL, $fromEmailName = NULL, $response = NULL)
            sendMail::send($_POST['priezvisko'] . " " . $_POST['meno'], $_POST['email'], $subject, $body, NULL, NULL, NULL, $response);

            $sql = "INSERT INTO " . TABLE_PREFIX . "kariera_uchadzaci VALUES ('', '" . $_POST['priezvisko'] . " " . $_POST['meno'] . " " . $_POST['titul'] . "', '" . date("Y-m-d") . "', '" . $_POST['mobil'] . "', '" . $_POST['email'] . "', '" . $body . "')";

            if (!$Result = mysql_query($sql)) {
                if (mysql_errno()) {
                    echo "MySql Error (" . mysql_errno() . "): " . mysql_error() . "<br />";
                    echo $queryString;
                }
            } else {
                makeLog("Ulozenie uchadzaca o zamestnanie do DB", $_POST['priezvisko'] . " " . $_POST['meno'] . "<br>");
                $_SESSION['_message'] = $cTranslator->getTranslation("Registračný formulár bol úšpešne odoslaný");
            }
        } else {
            $_SESSION['_message'] = $error;
        }
    } else {
        $_SESSION['_message'] = $cTranslator->getTranslation("Potvrdenie odoslania vyplneného formulára je povinné!");
    }
}
// nacitanie obsahu modulu
ob_start();

// VYKONANIE AKCII
switch ($navigateArrayUrlWithoutBase[0]) {
    
}
?>

<div id="career-form">    

    <?php
    if (empty($akcia) or ( !empty($error))) {
        ?>
        <form name="career" method="post" action="" id="career">
            <table border="0" cellpadding="1" cellspacing="1">
                <?php
                if (!empty($error)) {
                    echo '<tr><td>' . $error . '<br /></td></tr>';
                }
                ?>
                <tr>
                    <td>
                        <div align="left">
                            <?php if ($RowP['sk_name']) echo $cTranslator->getTranslation("Reagujete na inzerát: ") . " <b>" . $RowP['sk_name'] . "</b>"; ?>
                            <p>&nbsp;</p>
                            <p>
                                <?= getContentByLabel('kariera-hlavicka-text') ?><br>
                                <span><?= $cTranslator->getTranslation("Poznámka: Povinné údaje sú označené") ?><font color="#FF0000">*</font></span>
                            </p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="2" cellspacing="0">
                            <tr>
                                <td colspan="4">
                                    <strong><?= $cTranslator->getTranslation("Osobné údaje:") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("Priezvisko") ?><font color="#FF0000">*</font> :
                                </td>
                                <td>
                                    <input type="text" name="priezvisko" value="<?= $_POST['priezvisko'] ?>" />
                                </td>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("Dátum narodenia") ?><font color="#FF0000">*</font> :
                                </td>
                                <td>
                                    <input type="text" name="narodenie" value="<?= $_POST['narodenie'] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("Meno") ?><font color="#FF0000">*</font> :
                                </td>
                                <td>
                                    <input type="text" name="meno" value="<?= $_POST['meno'] ?>" />
                                </td>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("Št. príslušnosť") ?>:
                                </td>
                                <td>
                                    <input type="text" name="stat" value="<?= $_POST[stat] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("Titul") ?>:
                                </td>
                                <td>
                                    <input type="text" name="titul" size="10" value="<?= $_POST['titul'] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <strong><?= $cTranslator->getTranslation("Kontaktné údaje:") ?>&nbsp;</strong>
                                    <br>
                                    <?= $cTranslator->getTranslation("(Prosím, uvedomte si, že bez uvedenia aspoň jedného&nbsp; z uvedenýchkontaktných údajov nebude možné v našom vzájomnom dialógu pokračovať!)") ?>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td align="right"><?= $cTranslator->getTranslation("E-mail") ?><font color="#FF0000">*</font> :</td>
                                <td><input type="text" name="email" value="<?= $_POST['email'] ?>" /></td>
                                <td align="right" ><?= $cTranslator->getTranslation("Mobilný telefón:") ?></td>
                                <td><input type="text" name="mobil" value="<?= $_POST['mobil'] ?>" /></td>
                            </tr>
                            <tr>
                                <td align="right" >
                                    <?= $cTranslator->getTranslation("Telefón - domov:") ?>
                                </td>
                                <td>
                                    <input type="text" name="teldomu" value="<?= $_POST['teldomu'] ?>" />
                                </td>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("Fax:") ?>
                                </td>
                                <td>
                                    <input type="text" name="fax" value="<?= $_POST['fax'] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right" >
                                    <?= $cTranslator->getTranslation("Telefón - práca:") ?>
                                </td>
                                <td>
                                    <input type="text" name="telprace" value="<?= $_POST['telprace'] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">
                                    <?= $cTranslator->getTranslation("Kontaktná adresa:") ?> <br>
                                    <?= $cTranslator->getTranslation("(pre písomný styk)") ?></td>
                                <td colspan="3">
                                    <textarea name="adresa"><?= $_POST['adresa'] ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" valign="top">&nbsp;</td>
                                <td colspan="3"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="2" cellspacing=""0>
                            <tr>
                                <td colspan="4">
                                    <strong><?= $cTranslator->getTranslation("Mám záujem pracovať vo vašej spoločnosti:") ?></strong>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">&nbsp;<?= $cTranslator->getTranslation("v oblasti:") ?></td>
                                <td align="left">&nbsp;</td>
                                <td align="left"><input name="oblast[]" type="checkbox" class="noborder" id="oblast[]"  value="Obchodny zastupca"></td><td><?= $cTranslator->getTranslation("Obchodný zástupca") ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td align="left">&nbsp;</td>
                                <td align="left"><input name="oblast[]" type="checkbox" class="noborder" id="oblast[]"  value="Technolog"></td><td><?= $cTranslator->getTranslation("Technológ") ?></td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td align="left">&nbsp;</td>
                                <td align="left"><input name="oblast[]" type="checkbox" class="noborder" id="oblast[]"  value="Administrativa"></td><td><?= $cTranslator->getTranslation("Administratíva") ?></td>
                            </tr>
                            <tr>
                                <td align="right"><?= $cTranslator->getTranslation("vo funkcii") ?>&nbsp; </td>
                                <td align="left">&nbsp;</td>
                                <td></td>
                                <td><input type="text" name="funkcia" value="<?= $_POST['funkcia'] ?>" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="2" cellspacing="0">
                            <tr>
                                <td colspan="3">
                                    <strong><?= $cTranslator->getTranslation("Dosiahnuté vzdelanie a prax:") ?> </strong><br>
                                    <?= $cTranslator->getTranslation("(vrátane postgraduálneho a špecializovaného štúdia. Základnú školu nezadávajte!)") ?>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">&nbsp;</td>
                            </tr>
                            <tr>
                                <td><?= $cTranslator->getTranslation("Názov školy") ?></td>
                                <td><?= $cTranslator->getTranslation("Od - Do") ?></td>
                                <td><?= $cTranslator->getTranslation("Špecializácia") ?></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="skola1" value="<?= $_POST['skola1'] ?>" /></td>
                                <td><input name="skod1" type="text" size="11" maxlength="21" value="<?= $_POST['skod1'] ?>" /></td>
                                <td><input name="spec1" type="text"  size="21" value="<?= $_POST['spec1'] ?>" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="skola2" value="<?= $_POST['skola2'] ?>" /></td>
                                <td><input name="skod2" type="text" size="11" maxlength="21" value="<?= $_POST['skod2'] ?>" /></td>
                                <td><input name="spec2" type="text" size="21" value="<?= $_POST['spec2'] ?>" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="skola3" value="<?= $_POST['skola3'] ?>" /></td>
                                <td><input name="skod3" type="text" size="11" maxlength="21" value="<?= $_POST['skod3'] ?>" /></td>
                                <td><input name="spec3" type="text"  size="21" value="<?= $_POST['spec3'] ?>" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="skola4" value="<?= $_POST['skola4'] ?>" /></td>
                                <td><input name="skod4" type="text" size="11" maxlength="21" value="<?= $_POST['skod4'] ?>" /></td>
                                <td><input name="spec4" type="text"  size="21" value="<?= $_POST['spec4'] ?>" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>&nbsp;
                        <table width="100%" border="0" cellpadding="2" cellspacing="0">
                            <tr>
                                <td><?= $cTranslator->getTranslation("Zamestnávateľ") ?></td>
                                <td><?= $cTranslator->getTranslation("Od - Do") ?></td>
                                <td><?= $cTranslator->getTranslation("Zaradenie") ?></td>
                                <td><?= $cTranslator->getTranslation("Náplň práce") ?></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="zam1" value="<?= $_POST['zam1'] ?>" /></td>
                                <td><input name="zod1" type="text" size="11" maxlength="21" value="<?= $_POST['zod1'] ?>" /></td>
                                <td><input name="zarad1" type="text" size="21" value="<?= $_POST['zarad1'] ?>" /></td>
                                <td><input name="napln1" type="text" size="21" value="<?= $_POST['napln1'] ?>" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="zam2" value="<?= $_POST['zam2'] ?>" /></td>
                                <td><input name="zod2" type="text" size="11" maxlength="21" value="<?= $_POST['zod2'] ?>" /></td>
                                <td><input name="zarad2" type="text" size="21"  value="<?= $_POST['zarad2'] ?>" /></td>
                                <td><input name="napln2" type="text" size="21"  value="<?= $_POST['napln2'] ?>" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="zam3"  value="<?= $_POST['zam3'] ?>" /></td>
                                <td><input name="zod3" type="text" size="11" maxlength="21"  value="<?= $_POST['zod3'] ?>" /></td>
                                <td><input name="zarad3" type="text" size="21" value="<?= $_POST['zarad3'] ?>" /></td>
                                <td><input name="napln3" type="text" size="21"  value="<?= $_POST['napln3'] ?>" /></td>
                            </tr>
                            <tr>
                                <td><input type="text" name="zam4"  value="<?= $_POST['zam4'] ?>" /></td>
                                <td><input name="zod4" type="text" size="11" maxlength="21"  value="<?= $_POST['zod4'] ?>" /></td>
                                <td><input name="zarad4" type="text" size="21" value="<?= $_POST['zarad4'] ?>" /></td>
                                <td><input name="napln4" type="text" size="21"  value="<?= $_POST['napln4'] ?>" /></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="2" cellspacing="0">
                            <tr>
                                <td colspan="3"><strong><?= $cTranslator->getTranslation("Jazykové znalosti:") ?></strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <td align="right"><?= $cTranslator->getTranslation("Angličtina") ?></td>
                                <td align="right"><?= $cTranslator->getTranslation("Iný jazyk:") ?></td>
                                <td align="left">
                                    <input type="text" name="jazyk2"  value="<?= $_POST['jazyk2'] ?>" />
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("žiadna") ?>
                                    <input name="anglictina" type="radio" class="noborder" value="ziadna" checked />
                                </td>  
                                <td>&nbsp;</td>                      
                                <td align="left">
                                    <input name="jazyk" type="radio" class="noborder" value="ziadna" checked />
                                    <?= $cTranslator->getTranslation("žiadna") ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right" nowrap> 
                                    <?= $cTranslator->getTranslation("základná") ?>
                                    <input name="anglictina" type="radio" class="noborder" value="základná" <?= (($_POST['anglictina'] == "základná") ? "checked'checked'" : "") ?> />
                                </td>
                                <td align="center"> 
                                    <?= $cTranslator->getTranslation("(základná komunikácia)") ?> 
                                </td>
                                <td align="left">
                                    <input name="jazyk" type="radio" class="noborder" value="základná" <?= (($_POST['jazyk'] == "základná") ? "checked'checked'" : "") ?> />
                                    <?= $cTranslator->getTranslation("základná") ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right"> 
                                    <?= $cTranslator->getTranslation("priemerná") ?>
                                    <input name="anglictina" type="radio" class="noborder" value="mierne pokročilá" <?= (($_POST['anglictina'] == "mierne pokročilá") ? "checked'checked'" : "") ?> />
                                </td>
                                <td align="center">
                                    <?= $cTranslator->getTranslation("(jednoduchá komunikácia v odbore)") ?>
                                </td>
                                <td align="left">
                                    <input name="jazyk" type="radio" class="noborder" value="mierne pokročilá" <?= (($_POST['jazyk'] == "mierne pokročilá") ? "checked'checked'" : "") ?> />
                                    <?= $cTranslator->getTranslation("priemerná") ?></td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("pokročilá") ?>
                                    <input name="anglictina" type="radio" class="noborder" value="pokročilá" <?= (($_POST['anglictina'] == "pokročilá") ? "checked'checked'" : "") ?> />
                                </td>
                                <td align="center">
                                    <?= $cTranslator->getTranslation("(schopnosť argumentácie v odbore)") ?>
                                </td>
                                <td align="left">
                                    <input name="jazyk" type="radio" class="noborder" value="pokročilá" <?= (($_POST['jazyk'] == "pokročilá") ? "checked'checked'" : "") ?> />
                                    <?= $cTranslator->getTranslation("pokročilá") ?>
                                </td>
                            </tr>
                            <tr>
                                <td align="right">
                                    <?= $cTranslator->getTranslation("vynikajúca") ?>
                                    <input name="anglictina" type="radio" class="noborder" value="vynikajúca" <?= (($_POST['anglictina'] == "vynikajúca") ? "checked'checked'" : "") ?> />
                                </td>
                                <td align="center">
                                    <?= $cTranslator->getTranslation("(dokonalé ovládnutie jazyka)") ?>
                                </td>
                                <td align="left">
                                    <input name="jazyk" type="radio" class="noborder" value="vynikajúca" <?= (($_POST['jazyk'] == "vynikajúca") ? "checked'checked'" : "") ?> />
                                    <?= $cTranslator->getTranslation("vynikajúca") ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <table width="100%" border="0" cellpadding="2" cellspacing="0">
                            <tr>
                                <td colspan="5">&nbsp;</td>
                            </tr>
                            <tr>
                                <td colspan="5"><strong><?= $cTranslator->getTranslation("Doplňujúce údaje:") ?></strong></td>
                            </tr>
                            <tr>
                                <td width="220" align=RIGHT valign="top">Znalosti :</td>
                                <td width="379" colspan="4"><textarea name="znalSW" cols="50" rows="5" class="career_form2"  ><?= $_POST[znalSW] ?></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td align=RIGHT width="220"><?= $cTranslator->getTranslation("Iné znalosti a zručnosti:") ?></td>
                                <td colspan="4"><input name="ine" type="text" size="50" value="<?= $_POST[ine] ?>" class="career_form2"  />
                                </td>
                            </tr>
                            <tr>
                                <td align=RIGHT width="220"><?= $cTranslator->getTranslation("Dátum možného nástupu.") ?></td>
                                <td colspan="4"><input name="datumnast" type="text" size="50" value="<?= $_POST[datumnast] ?>" class="career_form2"  />
                                </td>
                            </tr>
                            <tr>
                                <td align=RIGHT valign=TOP width="220"><br>
                                    <?= $cTranslator->getTranslation("Rôzne") ?> <br>
                                    <?= $cTranslator->getTranslation("(tu môžete uviesť doplňujúce informácie o svojej osobe, ktoré považujete za dôležité pre prijímací pohovor)") ?></td>
                                <td colspan="4"><textarea name="rozne" cols="50" rows="5" class="career_form2"  ><?= $_POST[rozne] ?></textarea>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td></td>
                </tr>
                <tr>
                    <td align="justify">
                        <p>&nbsp;</p>
                        <?= getContentByLabel('kariera-potvrdenie-odosielanych-udajov') ?>
                    </td>
                </tr>
                <tr>
                    <td >&nbsp;</td>
                </tr>
                <tr>
                    <td align="center" ><div id='botprotect'><input type='checkbox' id='botprotect_checkbox' name='botprotect' value='ok'>
                            <?= $cTranslator->getTranslation("Áno, prajem si odoslať vyplnený formulár") ?> 
                        </div>
                        <!--<script>botprotect();</script>-->
                        <p><br />
                            <input type="submit"  value="Odoslať" name="Submit" class="button" >
                            <input type="reset" value="Zrušiť" class="button" name="Reset">
                            <input name="akcia" type="hidden" id="akcia" value="odoslat">
                        </p></td>
                </tr>
            </table>
        </form>
        <?php
    } else {
        ?>
        <p>
            <br>
            <?= $cTranslator->getTranslation("Ďakujeme za váš čas a dôveru, ktorú ste nám prejavili. Vaše údaje boli zaznamenané a v prípade potreby vás budeme kontaktovať") ?> 
        </p>
        <h2 align="center"><a href="<?= ROOTDIR ?>"><?= $cTranslator->getTranslation("Návrat na úvodnú stránku") ?></a></h2>
        <?php
    }
    ?>

</div>

<?php
$moduleContent = ob_get_contents();
ob_clean();
?>          
