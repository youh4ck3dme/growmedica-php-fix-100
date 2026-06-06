<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_cenova-ponuka.css" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
$js_file = ''; /* <script type="text/javascript" src="js/mod_xxc.js"></script> */
$js_file .= '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&amp;language=sk"></script>';
$js_file .= '<script type="text/javascript" src="js/gmap3/gmap3.min.js"></script>';
$footer_js_file = '<script type="text/javascript" src="js/gmap3/init.js"></script>';
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



echo '<div id="price_offer">';
$string_prod = 'select ' . $_SESSION['lang'] . '_name as name, ' . $_SESSION['lang'] . '_name_seo as name_seo, menu_id from ' . TABLE_PREFIX . 'menu where child_of="22" order by sorter';
$result_prod = mysql_query($string_prod);
while ($row_prod = mysql_fetch_array($result_prod)) {
    if ($navigateEnd == $row_prod['menu_id'])
        $class = "price_offer_item_selected";
    else
        $class = "price_offer_item";
    echo '<a href="' . Menu::getHyperLinkByID($navigateId) . '/detail/' . $row_prod['menu_id'] . '"><div class="' . $class . '">' . $row_prod['name'] . '</div></a>';
}

echo '</div>';




// VYKONANIE AKCII

if (isset($_POST['send'])) {

    $error = array();
    if (empty($_POST["Name"]))
        $error['Name'] = $cTranslator->getTranslation("Nie je vyplnene meno.") . "<br />";
    if (empty($_POST["Email"]))
        $error['Email'] = $cTranslator->getTranslation("Nie je vyplneny email.") . "<br />";

    if (empty($error)) {

        $subject = PROJECT_TITLE . " - Cenová ponuka";

        $string_product = 'select ' . $_SESSION['lang'] . '_name as title from ' . TABLE_PREFIX . 'menu where menu_id="' . $navigateEnd . '"';
        $result_product = mysql_query($string_product);
        $row_product = mysql_fetch_array($result_product);

        $body = "Typ produktu: " . $row_product['title'] . "\r\n";
        $body .=sprintf("Popis vyrobku: %s\r\n", $_POST['popis_vyrobku']);

        if ($_POST['product_name'] != 'zabradlia') {
            $body .=sprintf("Material: %s\r\n", $_POST[$_POST['product_name'] . 'material']);
            $material = $_POST[$_POST['product_name'] . 'material'];
            if ($material == 'tvrde drevo' or $material == 'makke drevo') {
                $pole = explode(" ", $material);
                $material = $pole[1];
            }
            $body .=sprintf("Povrchova uprava: %s\r\n", $_POST[$_POST['product_name'] . $material . '_povrch_uprava']);
        }



        if ($_POST['product_name'] == 'brany' or $_POST['product_name'] == 'ploty' or $_POST['product_name'] == 'schody' or $_POST['product_name'] == 'mreze') {
            $body .=sprintf("Jednotky rozmerov: %s\r\n", $_POST['jedn_rozmerov']);
        }




        switch ($_POST['product_name']) {

            case "brany":
                $body .="ROZMERY:\r\n" . sprintf("Sirka otvoru: %s\r\n", $_POST['brany_sirka_otvoru']);
                $body .=sprintf("Vyska otvoru: %s\r\n", $_POST['brany_vyska_otvoru']);
                $body .=sprintf("Pocet bran: %s\r\n", $_POST['brany_pocet']);
                $body .=sprintf("Druh brany: %s\r\n", $_POST['brany_druh']);
                if ($_POST['brany_druh'] == 'posuvne')
                    $body .=sprintf("Kolajnicka: %s\r\n", $_POST['posuvne_druh']);

                break;

            case "ploty":
                $body .="ROZMERY:\r\n" . sprintf("Sirka otvoru: %s\r\n", $_POST['ploty_sirka_otvoru']);
                $body .=sprintf("Vyska otvoru: %s\r\n", $_POST['ploty_vyska_otvoru']);
                $body .=sprintf("Pocet dielcov: %s\r\n", $_POST['ploty_pocet']);
                break;

            case "mreze":
                $body .="ROZMERY:\r\n" . sprintf("Sirka otvoru: %s\r\n", $_POST['mreze_sirka_otvoru']);
                $body .=sprintf("Vyska otvoru: %s\r\n", $_POST['mreze_vyska_otvoru']);
                $body .=sprintf("Pocet mrezi: %s\r\n", $_POST['mreze_pocet']);
                $body .=sprintf("Mreze: %s\r\n", $_POST['mreze_sposob_otvor']);
                break;

            case "schody":
                $body .="DLZKY RAMIEN SCHODOV:\r\n" . sprintf("Hlavne rameno: %s\r\n", $_POST['schody_hlavne_rameno']);
                $body .=sprintf("Bocne rameno: %s\r\n", $_POST['schody_bocne_prve']);
                $body .=sprintf("Bocne rameno: %s\r\n", $_POST['schody_bocne_druhe']);
                $body .=sprintf("Bocne rameno: %s\r\n", $_POST['schody_bocne_tretie']);
                $body .=sprintf("Priemer schodov: %s\r\n", $_POST['schody_priemer']);
                $body .=sprintf("Vyska schodov: %s\r\n", $_POST['schody_vyska']);
                $body .=sprintf("Poziadavka na zabradlie: %s\r\n", $_POST['schody_zabradlie']);

                break;

            case "zabradlia":
                $body .=sprintf("Sposob kotvenia zabradlia: %s\r\n", $_POST['zabradlie_kotvenie']);
                $body .="MADLO:\r\n" . sprintf("Prierez: %s\r\n", $_POST['zabradlie_madlo_prierez']);
                $body .=sprintf("Material: %s\r\n", $_POST[$_POST['product_name'] . 'madlo_material']);
                $material = $_POST[$_POST['product_name'] . 'madlo_material'];
                if ($material == 'tvrde drevo' or $material == 'makke drevo') {
                    $pole = explode(" ", $material);
                    $material = $pole[1];
                }
                $body .=sprintf("Povrchova uprava: %s\r\n", $_POST[$_POST['product_name'] . 'madlo_' . $material . '_povrch_uprava']);

                $body .="VYPLN:\r\n" . sprintf("Prierez: %s\r\n", $_POST['zabradlie_vypln_prierez']);
                $body .=sprintf("Material: %s\r\n", $_POST[$_POST['product_name'] . 'vypln_material']);
                $material = $_POST[$_POST['product_name'] . 'vypln_material'];
                if ($material == 'tvrde drevo' or $material == 'makke drevo') {
                    $pole = explode(" ", $material);
                    $material = $pole[1];
                }
                $body .=sprintf("Povrchova uprava: %s\r\n", $_POST[$_POST['product_name'] . 'vypln_' . $material . '_povrch_uprava']);
                $body .=sprintf("Sposob kotvenia vyplne: %s\r\n", $_POST['zabradlie_vypln_kotvenie']);
                $body .=sprintf("Pocet prutov vyplne: %s\r\n", $_POST['zabradlie_vypln_pocet']);

                $body .="STOJKA:\r\n" . sprintf("Prierez: %s\r\n", $_POST['zabradlie_stojka_prierez']);
                $body .=sprintf("Material: %s\r\n", $_POST[$_POST['product_name'] . 'stojka_material']);
                $material = $_POST[$_POST['product_name'] . 'stojka_material'];
                if ($material == 'tvrde drevo' or $material == 'makke drevo') {
                    $pole = explode(" ", $material);
                    $material = $pole[1];
                }
                $body .=sprintf("Povrchova uprava: %s\r\n", $_POST[$_POST['product_name'] . 'stojka_' . $material . '_povrch_uprava']);
                $body .=sprintf("Jednotky rozmerov: %s\r\n", $_POST['jedn_rozmerov']);

                $body .="ROZMERY:\r\n" . sprintf("Rozmer A1: %s\r\n", $_POST['zabradlie_rozmer_a1']);
                $body .=sprintf("Rozmer A2: %s\r\n", $_POST['zabradlie_rozmer_a2']);
                $body .=sprintf("Rozmer A3: %s\r\n", $_POST['zabradlie_rozmer_a3']);
                $body .=sprintf("Rozmer A4: %s\r\n", $_POST['zabradlie_rozmer_a4']);
                $body .=sprintf("Rozmer A5: %s\r\n", $_POST['zabradlie_rozmer_a5']);
                $body .=sprintf("Celková dĺžka zábradlia: %s\r\n", $_POST['zabradlie_dlzka']);


                break;
        }


        if ($_POST['product_name'] != 'zabradlia') {
            $body .=sprintf("Požiadavka na dopravu a montaz: %s\r\n", $_POST['poziadavka_montaz']);
        }

        if ($_POST['product_name'] == 'atypicky') {
            $body .=sprintf("Pocet kusov: %s\r\n", $_POST['atypicky_pocet_ks']);
        }

        $body .= sprintf("Meno odosielatela: %s\r\n", $_POST['Name']);
        $body .= sprintf("Mail odosielatela: %s\r\n", $_POST['Email']);
        $body .= sprintf("Telefón odosielatela: %s\r\n", $_POST['Phone']);
        $body .= sprintf("Doplňujúce poznámky: %s\r\n", $_POST['Message']);
        //
        // sendMail::send($toName, $sendTo, $subject, $body, $attachment = NULL, $fromEmail = NULL, $fromEmailName = NULL, $response = NULL)
        if (!empty($_FILES['Attachment']['name']))
            $file = $_FILES['Attachment'];
        else
            $file = '';
        sendMail::send($_POST['Name'], $_POST['Email'], $subject, $body, $file);
    } else {
        $_SESSION['_message'] = $cTranslator->getTranslation('Nesprávne vyplnený formulár! Prosím skúste znova.', 0);
    }
}

echo '<div class="showimage_div"></div>';

switch ($navigateArrayUrlWithoutBase[0]) {

    case "detail":

        switch ($navigateEnd) {
            case '41':
                $produkt = 'zabradlia';
                break;

            case '42':
                $produkt = 'brany';
                break;

            case '43':
                $produkt = 'ploty';
                break;

            case '47':
                $produkt = 'schody';
                break;

            case '46':
                $produkt = 'mreze';
                break;

            case '45':
                $produkt = 'atypicky';
                break;
        }

        $string_title = 'select ' . $_SESSION['lang'] . '_name as title from ' . TABLE_PREFIX . 'menu where menu_id="' . $navigateEnd . '"';
        $result_title = mysql_query($string_title);
        $row_title = mysql_fetch_array($result_title);

        echo '
                <div id="mod_cenova_ponuka">
                    <h1>' . Menu::getHyperLinkTextById($navigateId) . ' - ' . $row_title['title'] . '</h1>
                    <form name="form" method="post" action="" class="contactform" enctype="multipart/form-data" id="form">
                        <input type="hidden" value="' . $produkt . '" name="product_name" />

                        <div class="left name popis_vyrobku_resp"><label>' . $cTranslator->getTranslation("Popis výrobku:") . '</label></div>     <div class="left"><textarea name="popis_vyrobku"';
        if ($produkt == 'zabradlia')
            echo 'style="height:127px"';
        echo' ></textarea></div>';

        if ($produkt == 'zabradlia') {
            echo '<div id="zabradlia_popis"></div>';
        }
        echo '
                        <div style="clear:both"></div>
                        <div style="margin:15px 0 0 0">' . $cTranslator->getTranslation("Ak máte možnosť, vložte obrázok, náčrt, výkres, foto, odkaz na web stránku výrobku, ktorý chcete naceniť.") . '</div>
                        <div style="clear:both"></div>
                        <div class="left name"><label>' . $cTranslator->getTranslation("Nahrať súbor:") . ' </label></div><div class="left"><input type="file" name="Attachment" ></div>
                        <div style="clear:both"></div>';


        if ($produkt == 'brany' or $produkt == 'ploty' or $produkt == 'schody' or $produkt == 'mreze' or $produkt == 'atypicky') {
            echo '<div class="type_style">';
            $select_name = $produkt; //pouzita premenna pre name selectu
            require ('include/form-vyber-materialu.php');
            echo '</div>';
        }


        if ($produkt == 'brany' or $produkt == 'ploty' or $produkt == 'schody' or $produkt == 'mreze')
            echo '
                        <div class="paragraph1">' . $cTranslator->getTranslation("Zadajte stavebné rozmery, zvoľte si jednotky rozmerov:") . '</div>
                            <div style="clear:both"></div>
                                <div class="paragraph3" style="margin-left:40px"><input type="radio" name="jedn_rozmerov" value="mm"><label>' . $cTranslator->getTranslation(" mm") . '</label></div>
                                <div class="paragraph3"><input type="radio" name="jedn_rozmerov" value="cm"><label>' . $cTranslator->getTranslation(" cm") . '</label></div>
                                <div class="paragraph3"><input type="radio" name="jedn_rozmerov" value="m"><label>' . $cTranslator->getTranslation(" m") . '</label> </div>
                                <div style="clear:both"></div>';


        if ($produkt == 'brany')
            echo '
                                <div style="margin:10px 0 0 40px">
                                    <div class="left name showimage" id="img_' . $produkt . '">' . $cTranslator->getTranslation("Šírka otvoru A = ") . '</div><div class="left"><input type="text" name="brany_sirka_otvoru" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name showimage" id="img_' . $produkt . '">' . $cTranslator->getTranslation("Výška otvoru B = ") . '</div><div class="left"><input type="text" name="brany_vyska_otvoru" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name">' . $cTranslator->getTranslation("Počet brán: ") . '</div><div class="left"><input type="text" name="brany_pocet" >' . $cTranslator->getTranslation(" ks") . '</div>
                                    <div style="clear:both"></div>
                                    <div>' . $cTranslator->getTranslation("Pre zadanie ďalších rozmerov vytvorte ďalšiu objednávku.") . '</div>
                                    <div style="clear:both"></div>
                                </div>

                                <div class="paragraph1">' . $cTranslator->getTranslation("Označte druh brány: ") . '</div>
                                <div style="clear:both"></div>
                                <div class="paragraph3_bez_float"><input type="radio" name="brany_druh" value="1-kridlove"><label>' . $cTranslator->getTranslation(" 1-krídlové") . '</label></div>
                                <div class="paragraph3_bez_float"><input type="radio" name="brany_druh" value="2-kridlove"><label>' . $cTranslator->getTranslation(" 2-krídlové") . '</label></div>
                                <div class="paragraph3_bez_float"><input type="radio" name="brany_druh" value="3-kridlove"><label>' . $cTranslator->getTranslation(" 3-krídlové") . '</label></div>
                                <div class="paragraph3_bez_float"><input type="radio" name="brany_druh" value="viackridlove"><label>' . $cTranslator->getTranslation(" viac krídlové") . '</label></div>
                                <div class="paragraph3_bez_float"><input type="radio" name="brany_druh" value="posuvne"><label>' . $cTranslator->getTranslation(" posuvné brány") . '</label></div>
                                    <div class="paragraph4"><input type="radio" name="posuvne_druh" value="posuvne s kolajnickou"><label>' . $cTranslator->getTranslation(" s koľajničkou") . '</label></div>
                                    <div class="paragraph4"> <input type="radio" name="posuvne_druh" value="posuvne bez kolajnicky"><label>' . $cTranslator->getTranslation(" bez koľajničky") . '</label></div>';

        if ($produkt == 'ploty')
            echo '
                                <div style="margin: 10px 0 0 40px">
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Šírka otvoru A = ") . '</label></div><div class="left"><input type="text" name="ploty_sirka_otvoru" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Výška otvoru B = ") . '</label></div><div class="left"><input type="text" name="ploty_vyska_otvoru" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name"><label>Počet dielcov: </label></div><div class="left"><input type="text" name="ploty_pocet" >' . $cTranslator->getTranslation(" ks") . '</div>
                                    <div style="clear:both"></div>
                                    <div>' . $cTranslator->getTranslation("Pre zadanie ďalších rozmerov vytvorte ďalšiu objednávku.") . '</div>
                                    <div style="clear:both"></div>
                                </div>';

        if ($produkt == 'mreze')
            echo '
                                <div style="margin: 10px 0 0 40px">
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Šírka otvoru A = ") . '</div><div class="left"><input type="text" name="mreze_sirka_otvoru" ></label></div>
                                    <div style="clear:both"></div>
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Výška otvoru B = ") . '</div><div class="left"><input type="text" name="mreze_vyska_otvoru" ></label></div>
                                    <div style="clear:both"></div>
                                    <div class="left name"><label>' . $cTranslator->getTranslation("Počet mreží: ") . '</div><div class="left"><input type="text" name="mreze_pocet" >' . $cTranslator->getTranslation(" ks") . '</label></div>
                                    <div style="clear:both"></div>
                                    <div>' . $cTranslator->getTranslation("Pre zadanie ďalších rozmerov vytvorte ďalšiu objednávku.") . '</div>
                                    <div style="clear:both"></div>
                                </div>
                                <div style="clear:both;margin:10px 0 0 0 "></div>
                                <div class="paragraph3 showimage" style="width: 125px;" id="img_' . $produkt . '"> <input type="radio" name="mreze_sposob_otvor" value="mreza do otvoru"><label>' . $cTranslator->getTranslation(" mreža do otvoru") . '</label></div>
                                <div class="paragraph3 showimage" style="width: 125px;" id="img_' . $produkt . '"> <input type="radio" name="mreze_sposob_otvor" value="mreza cez otvor"><label>' . $cTranslator->getTranslation(" mreža cez otvor") . '</label></div>
                                <div style="clear:both"></div>';

        if ($produkt == 'schody')
            echo '  <div class="paragraph1">' . $cTranslator->getTranslation("Dĺžky ramien schodov: ") . '</div>
                                <div style="clear:both"></div>
                                <div style="margin-left:40px">
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Hlavné rameno C = ") . '</label></div><div class="left"><input type="text" name="schody_hlavne_rameno" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Bočné D = ") . '</label></div><div class="left"><input type="text" name="schody_bocne_prve" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Bočné E = ") . '</label></div><div class="left"><input type="text" name="schody_bocne_druhe" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Bočné F = ") . '</label></div><div class="left"><input type="text" name="schody_bocne_tretie" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Priemer schodov G = ") . '</label></div><div class="left"><input type="text" name="schody_priemer" ></div>
                                    <div style="clear:both"></div>
                                    <div class="left name showimage" id="img_' . $produkt . '"><label>' . $cTranslator->getTranslation("Výška schodov V = ") . '</label></div><div class="left"><input type="text" name="schody_vyska" ></div>
                                    <div style="clear:both"></div>
                                </div>
                                <div class="paragraph1">' . $cTranslator->getTranslation("Požiadavka na zábradlie: ") . '</div>
                                <div style="clear:both"></div>
                                <div class="paragraph3"><input type="radio" name="schody_zabradlie" value="ano"><label>' . $cTranslator->getTranslation(" áno") . '</label></div>
                                <div class="paragraph3"><input type="radio" name="schody_zabradlie" value="nie"><label>' . $cTranslator->getTranslation(" nie") . '</label></div>
                                <div style="clear:both"></div>';

        if ($produkt == 'zabradlia') {
            echo '
                                <div class="paragraph1">' . $cTranslator->getTranslation("Spôsob kotvenia zábradlia: ") . '</div>
                                <div style="clear:both"></div>
                                <div class="paragraph3 radio_left_float showimage" id="img_' . $produkt . '_kotvenie">
                                    <label> <input type="radio" name="zabradlie_kotvenie" value="kotvenie z hora">' . $cTranslator->getTranslation(" kotvenie zhora") . '</label>
                                    <label> <input type="radio" name="zabradlie_kotvenie" value="kotvenie z boku">' . $cTranslator->getTranslation(" kotvenie z boku") . '</label>
                                </div>
                                <div style="clear:both"></div>
                                        <div class="type_style">
                                                <div class="paragraph0">' . $cTranslator->getTranslation("Madlo: ") . '</div>
                                                    <div class="paragraph3 radio_left_float">
                                                        <label><input type="radio" name="zabradlie_madlo_prierez" value="kruhovy prierez">' . $cTranslator->getTranslation(" kruhový prierez") . '</label>
                                                        <label><input type="radio" name="zabradlie_madlo_prierez" value="stvorcovy prierez">' . $cTranslator->getTranslation(" štvorcový prierez") . '</label>
                                                        <label><input type="radio" name="zabradlie_madlo_prierez" value="obdlznikovy prierez">' . $cTranslator->getTranslation(" obdĺžnikový prierez") . '</label>
                                                    </div>';

            $select_name = $produkt . 'madlo_'; //pouzita premenna pre name selectu
            echo '<div class="odsadenie">';
            require ('include/form-vyber-materialu.php');
            echo '</div>
                                        </div>';

            echo '
                                        <div class="type_style">
                                            <div class="paragraph0">' . $cTranslator->getTranslation("Výplň: ") . '</div>
                                            <div class="paragraph3 radio_left_float">
                                                <label><input type="radio" name="zabradlie_vypln_prierez" value="kruhovy prierez">' . $cTranslator->getTranslation(" kruhový prierez") . '</label>
                                                <label><input type="radio" name="zabradlie_vypln_prierez" value="stvorcovy prierez">' . $cTranslator->getTranslation(" štvorcový prierez") . '</label>
                                                <label><input type="radio" name="zabradlie_vypln_prierez" value="obdlznikovy prierez">' . $cTranslator->getTranslation(" obdĺžnikový prierez") . '</label>
                                                <label><input type="radio" name="zabradlie_vypln_prierez" value="plosna vypln">' . $cTranslator->getTranslation(" plošná výplň") . '</label>
                                            </div>';

            $select_name = $produkt . 'vypln_'; //pouzita premenna pre name selectu
            echo '<div class="odsadenie">';
            require ('include/form-vyber-materialu.php');
            echo '</div>';

            echo '
                                            <div class="odsadenie">
                                                <div class="paragraph1">' . $cTranslator->getTranslation("Spôsob kotvenia výplne: ") . '</div>
                                                <div style="clear:both"></div>
                                                    <div class="paragraph3 radio_left_float showimage" style="margin-left: 0px;" id="img_' . $produkt . '_vypln_kotvenie">
                                                        <label><input type="radio" name="zabradlie_vypln_kotvenie" value="kotvenie cez stojku">' . $cTranslator->getTranslation(" kotvenie cez stojku") . '</label>
                                                        <label><input type="radio" name="zabradlie_vypln_kotvenie" value="kotvenie z boku stojky">' . $cTranslator->getTranslation(" kotvenie z boku stojky") . '</label>
                                                        <label><input type="radio" name="zabradlie_vypln_kotvenie" value="horizontalne">' . $cTranslator->getTranslation(" horizontálne") . '</label>
                                                        <label><input type="radio" name="zabradlie_vypln_kotvenie" value="vertikalne">' . $cTranslator->getTranslation(" vertikálne") . '</label>
                                                    </div>
                                                    <div style="clear:both;margin:10px 0 0 0"></div>
                                                    <div class="left name showimage" style="width:180px"><label>' . $cTranslator->getTranslation("Zvoľte si počet prútov výplne: ") . '</label></div><div class="left"><input type="text" name="zabradlie_vypln_pocet" placeholder="1-10">' . $cTranslator->getTranslation(" ks") . '</div>
                                                    <div style="clear:both"></div>
                                            </div>
                                        </div>

                                        <div class="type_style">
                                            <div class="paragraph0">' . $cTranslator->getTranslation("Stojka: ") . '</div>
                                                <div class="paragraph3 radio_left_float">
                                                    <label><input type="radio" name="zabradlie_stojka_prierez" value="kruhovy prierez">' . $cTranslator->getTranslation(" kruhový prierez") . '</label>
                                                    <label><input type="radio" name="zabradlie_stojka_prierez" value="stvorcovy prierez">' . $cTranslator->getTranslation(" štvorcový prierez") . '</label>
                                                    <label><input type="radio" name="zabradlie_stojka_prierez" value="obdlznikovy prierez">' . $cTranslator->getTranslation(" obdĺžnikový prierez") . '</label>
                                                </div>';

            $select_name = $produkt . 'stojka_'; //pouzita premenna pre name name selectu
            echo '<div class="odsadenie">';
            require ('include/form-vyber-materialu.php');
            echo '</div>';


            echo '

                                                <div class="odsadenie">
                                                    <div class="paragraph1">' . $cTranslator->getTranslation("Zadajte stavebné rozmery, zvoľte si jednotky rozmerov: ") . '</div>
                                                    <div style="clear:both"></div>
                                                        <div class="paragraph3 radio_left_float" style="margin-left: 40px;">
                                                            <label><input type="radio" name="jedn_rozmerov" value="mm">' . $cTranslator->getTranslation(" mm") . '</label>
                                                            <label><input type="radio" name="jedn_rozmerov" value="cm">' . $cTranslator->getTranslation(" cm") . '</label>
                                                            <label><input type="radio" name="jedn_rozmerov" value="m">' . $cTranslator->getTranslation(" m") . '</label>
                                                        </div>

                                                        <div style="clear:both;margin:10px 0 0 0"></div>
                                                        <div style="margin:0 0 0 40px;" class="left width_resp">
                                                            <div class="left name" ><label>' . $cTranslator->getTranslation("Rozmer A1 = ") . '</label></div><div class="left"><input type="text" name="zabradlie_rozmer_a1" ></div>
                                                            <div style="clear:both"></div>
                                                            <div class="left name"><label>' . $cTranslator->getTranslation("Rozmer A2 = ") . '</label></div><div class="left"><input type="text" name="zabradlie_rozmer_a2" ></div>
                                                            <div style="clear:both"></div>
                                                            <div class="left name"><label>' . $cTranslator->getTranslation("Rozmer A3 = ") . '</label></div><div class="left"><input type="text" name="zabradlie_rozmer_a3" ></div>
                                                            <div style="clear:both"></div>
                                                            <div class="left name"><label>' . $cTranslator->getTranslation("Rozmer A4 = ") . '</label></div><div class="left"><input type="text" name="zabradlie_rozmer_a4" ></div>
                                                            <div style="clear:both"></div>
                                                            <div class="left name"><label>' . $cTranslator->getTranslation("Rozmer A5 = ") . '</label></div><div class="left"><input type="text" name="zabradlie_rozmer_a5" ></div>
                                                            <div style="clear:both"></div>
                                                            <div class="left name"><label>' . $cTranslator->getTranslation("Celková dĺžka zábradlia: ") . '</label></div><div class="left"><input type="text" name="zabradlie_dlzka" ></div>
                                                            <div style="clear:both"></div>
                                                            <div>' . $cTranslator->getTranslation("Pre zadanie ďalších rozmerov vytvorte ďalšiu objednávku.") . '</div>
                                                            <div style="clear:both"></div>
                                                        </div>
                                                        <div id="zabradlia_popis2"></div>
                                                </div>
                                                <div style="clear:both"></div>
                                        </div>';
        }

        echo '  <div style="clear:both"></div>
                                <div class="paragraph0" style="color:#000">' . $cTranslator->getTranslation("Požiadavka na dopravu a montáž:") . '</div>
                                        <div class="paragraph3 radio_left_float">
                                                <label><input type="radio" name="poziadavka_montaz" value="vyroba bez dopravy, bez montaze">' . $cTranslator->getTranslation(" výroba bez dopravy, bez montáže") . '</label>
                                                <label><input type="radio" name="poziadavka_montaz" value="vyroba s dopravou, bez montaze">' . $cTranslator->getTranslation(" výroba s dopravou, bez montáže") . '</label>
                                                <label><input type="radio" name="poziadavka_montaz" value="vyroba s dopravou a s montazou">' . $cTranslator->getTranslation(" výroba s dopravou a s montážou") . '</label>
                                        </div>
                                        <div style="clear:both;margin:0 0 20px 0"></div>';

        if ($produkt == 'atypicky')
            echo '  <div class="left name"><label for="Pocet">' . $cTranslator->getTranslation('Zadajte počet kusov') . ':</label></div>
                                <div class="left"><input type="text" name="atypicky_pocet_ks" id="atypicky_pocet_ks" />' . $cTranslator->getTranslation(" ks") . '</div>
                                <div style="clear:both"></div>';

        echo '  <div class="left name"><label for="Message">' . $cTranslator->getTranslation('Doplňujúce poznámky') . ':</label></div>
                                <div class="left"><textarea name="Message"  id="Message" style="width:300px;"></textarea></div>
                                <div style="clear:both"></div>

                                <div class="left name"><label for="Name">' . $cTranslator->getTranslation('Vaše meno') . ': *</label></div>
                                <div class="left"><input type="text" name="Name" id="Name" /></div>
                                <div style="clear:both"></div>

                                <div class="left name"><label for="Phone">' . $cTranslator->getTranslation('Telefón') . ':</label></div>
                                <div class="left"><input type="text" name="Phone" id="Phone"/></div>
                                <div style="clear:both"></div>

                                <div class="left name"><label for="Email">' . $cTranslator->getTranslation('E-mail') . ': *</label></div>
                                <div class="left"><input type="text" name="Email" id="Email" /></label></div>
                                <div style="clear:both"></div>

                                <p class="udaje">' . $cTranslator->getTranslation('Údaje označené * sú povinné.') . '</p>
                                <input type="submit" name="send" value="' . $cTranslator->getTranslation('Odoslať') . '" class="submit-button" />';

        echo '
                    </form>';

        echo '
                </div>';
        break;

    default:
        $string_content = 'select ' . $_SESSION['lang'] . '_content as content from ' . TABLE_PREFIX . 'menu where menu_id="' . $navigateId . '"';
        $result_content = mysql_query($string_content);
        $row_content = mysql_fetch_array($result_content);

        echo '<div id="mod_cenova_ponuka">';
        echo '<h1>' . Menu::getHyperLinkTextById($navigateId) . '</h1>';
        echo html_entity_decode($row_content['content'], ENT_QUOTES, "UTF-8");

        echo '<div class="clear"></div>';

        echo '<div id="price_offer_content">';
        $string_prod = 'select ' . $_SESSION['lang'] . '_name as name, ' . $_SESSION['lang'] . '_name_seo as name_seo, menu_id from ' . TABLE_PREFIX . 'menu where child_of="22" order by sorter';
        $result_prod = mysql_query($string_prod);
        while ($row_prod = mysql_fetch_array($result_prod)) {
            if ($navigateEnd == $row_prod['menu_id'])
                $class = "price_offer_item_selected";
            else
                $class = "price_offer_item";
            echo '<a href="' . Menu::getHyperLinkByID($navigateId) . '/detail/' . $row_prod['menu_id'] . '"><div class="' . $class . '">' . $row_prod['name'] . '</div></a>';
        }

        echo '</div>';

        echo '</div>';
}
?>

<script type="text/javascript">
    $(document).ready(function() {
        $('.showimage').mouseover(function() {
            var image = $(this).attr('id');
            $('.showimage_div').css("background-image", 'url(images/cenova_ponuka/' + image + '.png)');
            $('.showimage_div').show();
        });
        $('.showimage').mouseout(function() {
            $('.showimage_div').hide();
        });
    });
</script>

<? if (is_numeric($navigateEnd)) { ?>
    <script language="javascript" type="text/javascript">
        var frmvalidator = new Validator("form");
        frmvalidator.addValidation("Email", "maxlen=50", "<?= $cTranslator->getTranslation('Nezadali ste správnu dĺžku e-mailovej adresy', 0); ?>");
        frmvalidator.addValidation("Email", "req", "<?= $cTranslator->getTranslation('Nezadali ste e-mailovú adresu', 0); ?>");
        frmvalidator.addValidation("Email", "email", "<?= $cTranslator->getTranslation('Nezadali korektnú e-mailovú adresu', 0); ?>");
        frmvalidator.addValidation("Name", "req", "<?= $cTranslator->getTranslation('Nezadali ste Vaše meno a priezvisko', 0); ?>");
    </script>

<? } ?>

<?
$moduleContent = ob_get_contents();
ob_clean();
?>