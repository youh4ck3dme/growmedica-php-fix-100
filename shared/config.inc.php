<?php

// SixAdmin vs 3.0
session_start();
error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
ini_set('display_errors', '0');
// ini_set("log_errors", 1);
// ini_set('error_log', __DIR__.'/errors.log');

header('Cache-Control: max-age=31536000');

if (isset($_SERVER['HTTP_USER_AGENT']) AND ( strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false))
    header('X-UA-Compatible: IE=edge,chrome=1');

// zistenie ci sme na mobile alebo na desktope
$useragent = $_SERVER['HTTP_USER_AGENT'];
if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
    $_SESSION['pref']['isMobile'] = '1';
} else {
    $_SESSION['pref']['isMobile'] = '0';
}
// Load local environment variables if .env.local exists
if (file_exists(dirname(__DIR__) . '/.env.local')) {
    $lines = file(dirname(__DIR__) . '/.env.local', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (empty($line) || strpos($line, '#') === 0) {
            continue;
        }
        if (strpos($line, '=') !== false) {
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            if (getenv($name) === false) {
                putenv("$name=$value");
                $_ENV[$name] = $value;
                $_SERVER[$name] = $value;
            }
        }
    }
}

if (getenv('DB_HOSTNAME')) {
    define("DB_HOSTNAME", getenv('DB_HOSTNAME'));
    define("DB_USERNAME", getenv('DB_USERNAME'));
    define("DB_PASSWORD", getenv('DB_PASSWORD'));
    define("DB_DATABASE", getenv('DB_DATABASE'));
    define("ROOTDIR", getenv('ROOTDIR'));
    define("TABLE_PREFIX", getenv('TABLE_PREFIX'));
    define("DEVELOPMENT", true);
    define("PUBLIC_RELEASE", false);
} else {
    switch ($_SERVER['SERVER_NAME']) {
        case "growmedical.sk":
        case "www.growmedical.sk":
            define("DB_HOSTNAME", 'localhost');
            define("DB_USERNAME", 'c1growmedical');
            define("DB_PASSWORD", getenv('DB_PASSWORD') ?: 'c1growmedical_prod_password_redacted');
            define("DB_DATABASE", 'c1growmedical');
            define("ROOTDIR", 'https://' . $_SERVER['SERVER_NAME']);
            define("TABLE_PREFIX", 'objednavaj_');
            define("DEVELOPMENT", true);
            define("PUBLIC_RELEASE", true);
            break;
        case "growmedica.sk":
        case "www.growmedica.sk":
        case "growmedica.com":
        case "www.growmedica.com":
            define("DB_HOSTNAME", 'localhost');
            define("DB_USERNAME", 'c1growmedical');
            define("DB_PASSWORD", getenv('DB_PASSWORD') ?: 'c1growmedical_prod_password_redacted');
            define("DB_DATABASE", 'c1growmedical');
            define("ROOTDIR", 'https://' . $_SERVER['SERVER_NAME']);
            define("TABLE_PREFIX", 'growmedica_');
            define("DEVELOPMENT", true);
            define("PUBLIC_RELEASE", true);
            break;
        default:
            define("DB_HOSTNAME", 'localhost');
            define("DB_USERNAME", 'sqlsixnet');
            define("DB_PASSWORD", getenv('DB_PASSWORD') ?: 'sqlsixnet_local_password_redacted');
            define("DB_DATABASE", 'c1objednavaj');
            define("ROOTDIR", 'http://localhost/objednavaj');
            define("TABLE_PREFIX", 'objednavaj_');
            define("DEVELOPMENT", true);
            define("PUBLIC_RELEASE", false);
            break;
    }
}
//
// Nastavenia systému
define('HEUREKA_API_KEY', 'd2b8d8befd5cf1ee7b7037ea04ae6a3b'); 
define('HEUREKA_API_EMAIL', 'info@growmedica.sk'); 
// Identifikátor v službe Merchant Center
define('MERCHANT_ID', '5342529629');

define('STATUS_CAREER', '0'); // 0 = skrytý, 1 = zobrazený
define('STATUS_NEWSLETTER', '0'); // 0 = úplne skrytý, 1 = export emailových adries, 2 = úplne funkčný
define('STATUS_ESHOP', '1'); // 0 = skrytý, 1 = zobrazený
define('STATUS_SETTINGS', '1'); // 0 = skrytý, 1 = zobrazený
define('PRODUCT_SEARCH', '1'); // vyhľadávanie medzi produktami
define('ADVANCED_META', '0'); // skladá META v hlavičke z DB
//define('DOCUMENTROOT', '/var/www/sixadmin-new/'); // PDW FILE BROWSER ABSOLUTE URL
//
// veľkosti uploadovaných obrázkov
// TRANSFORM_TYPE: adaptive_resize (w,h), best_fit (w,h), fit_to_width (w), fit_to_height (h)
define('GALLERY_TRANSFORM_TYPE', 'best_fit');
define('THUMBS_DIMENSIONS', '250, 250'); // používa sa pri fotogalérii
define('PRODUCT_THUMBS_DIMENSIONS', '300, 400'); // thumnaily pre produkty
define('PRODUCT_COLOR_DIMENSIONS', '100, 100'); // veľkosť vzorky farby produktu
define('PREVIEW_DIMENSIONS', '640, 340'); // preview
define('ORIGINAL_DIMENSIONS', '1800, 1000'); // maximálna veľkosť uploadovaneho img
//define('PRODUCT_COLOR_DIMENSIONS', '200, 200'); // maximálna veľkosť obrázka farby

define('NEWS_PREVIEW_CHAR_LIMIT', '160'); //
//
define('MAX_ATTACHMENT_SIZE', '29360128'); // maximalna veľkosť prílohy, 1MB = 1048576
//
define('USER_ACCOUNT_MANAGE_ID', '58'); // id stránky s modulom "Správa uživateľského účtu" (slúži na odhlásenie z newslettra)
define('NEWS_ID', '308'); // ID news stránky
define('SEARCH_PAGE_ID', '85'); // ID stránky vyhľadávania
define('NOT_FOUND_PAGE_ID', '87'); // ID stránky vyhľadávania
//
define('ESHOP_MAIN_CATEGORY', '60'); // Hlavná kategória e-shopu
define('VAT_COEFFICIENT', '1.2'); // DPH koeficient. slúži na vypočet ceny s DPH
define('VAT_VISIBILITY', FALSE); // 1/0 nastavenie zobrazenia DPH
define('NEW_PRODUCT_LENGTH', '+ 5 week'); // doba počas ktorej je produkt označený ako nový
define('REGISTRATION_DISCOUNT', '0'); // zľava za registráciu, defaultne vypnutá. nutné nastaviť v DB _user "registration_discount" na "1" pri uživateľovy, zľava na prvú objednávku v percentách
define('FREE_DELIVERY_LIMIT', 60); // hodnota nad ktorým je doručenie zdarma
// pre vypnutie hlášky nastav 0
//
// globalne premenne
define("META_TITLE_LINKER", ' / ');
define('n', "\n"); // new line

switch ($_SERVER['SERVER_NAME']) {
    case "itechcom.eu":
    case "www.itechcom.eu":
        define("DEFAULT_LANG", 'en'); // predvolený jazyk
        define("PAGE", 'itechcom'); // konstanta stranky - napr. pre logo...
        define("PROJECT_NAME", 'itechcom'); // definica nazvu projektu (administracne rozhranie ...)
        define("PROJECT_TITLE", 'itechcom'); // definicia nazvu porjektu ( ukoncenie title )
        define("SEO_TITLE", 'itechcom'); // definicia seo prvku ( vyuzivane pri neutralnych alt, title ... )
        $fromAddress = "info@itechcom.eu";
        $fromName = "itechcom.eu";
        break;
    case "growmedica.sk":
    case "www.growmedica.sk":
        define("DEFAULT_LANG", 'sk'); // predvolený jazyk
        define("PAGE", 'growmedica'); // konstanta stranky - napr. pre logo...
        define("PROJECT_NAME", 'GrowMedica.sk'); // definica nazvu projektu (administracne rozhranie ...)
        define("PROJECT_TITLE", 'GrowMedica.sk'); // definicia nazvu porjektu ( ukoncenie title )
        define("SEO_TITLE", 'GrowMedica.sk'); // definicia seo prvku ( vyuzivane pri neutralnych alt, title ... )
        $fromAddress = "info@growmedica.sk";
        $fromName = "GrowMedica";
        break;
    case "growmedica.com":
    case "www.growmedica.com":
        define("DEFAULT_LANG", 'en'); // predvolený jazyk
        define("PAGE", 'growmedica'); // konstanta stranky - napr. pre logo...
        define("PROJECT_NAME", 'GrowMedica'); // definica nazvu projektu (administracne rozhranie ...)
        define("PROJECT_TITLE", 'GrowMedica'); // definicia nazvu porjektu ( ukoncenie title )
        define("SEO_TITLE", 'GrowMedica'); // definicia seo prvku ( vyuzivane pri neutralnych alt, title ... )
        $fromAddress = "info@growmedica.com";
        $fromName = "GrowMedica";
        break;
    default:
        define("DEFAULT_LANG", 'sk'); // predvolený jazyk
        define("PAGE", 'growmedica'); // konstanta stranky - napr. pre logo...
        define("PROJECT_NAME", 'GrowMedica.sk'); // definica nazvu projektu (administracne rozhranie ...)
        define("PROJECT_TITLE", 'GrowMedica.sk'); // definicia nazvu porjektu ( ukoncenie title )
        define("SEO_TITLE", 'GrowMedica.sk'); // definicia seo prvku ( vyuzivane pri neutralnych alt, title ... )
        $fromAddress = "info@growmedica.sk";
        $fromName = "growmedica.sk";
        break;
}
//
// jazykove nastavenia

$meta["sk"]['PAGE_TITLE'] = "GrowMedica";
// $meta["sk"]['DESCRIPTION'] = 'Sixadmin description'; // odporúčaná dlžka 150 znakov
// $meta["sk"]['KEYWORDS'] = 'Sixadmin keywords';
$meta["en"]['PAGE_TITLE'] = "GrowMedica";
// $meta["en"]['DESCRIPTION'] = 'Sixadmin description'; // odporúčaná dlžka 150 znakov
// $meta["en"]['KEYWORDS'] = 'Sixadmin keywords';


$emailAddress = array("kajo.szaffko@gmail.com"); // xxx@sixnet.sk, sixnet@sixnet.sk


// adresa na ktoru sendReport odosle spravu
$supportAddress = "sixnet@sixnet.sk";

/////////////////////////////// SMTP /////////////////////////////////////////////
define('SMTP_HOST', 'smtp.sixnet.sk');
define('SMTP_USERNAME', 'webform@growmedica.sk');
define('SMTP_PASSWORD', 'gftcsHFR7Z4@');

include 'SIXNET_SMTP/function_smtp.php';
/////////////////////////////// SMTP END//////////////////////////////////////////

$connId = mysql_pconnect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
if ($connId) {
    mysql_select_db(DB_DATABASE);
    mysql_query("SET character SET utf8;");
    mysql_query("SET names utf8;");
}

global $cLanguage;
require_once("classes/class.language.php");
$cLanguage = new cLanguage();

$languages = $cLanguage->getLanguageCodes();
//
// nastaví defaultny jazyk
if (!isset($_SESSION['lang']) OR ! in_array($_SESSION['lang'], $languages)) {
    $_SESSION['lang'] = DEFAULT_LANG;
}
// nastavenie jazyka z url
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    //header('Location: ' . ROOTDIR);
    //exit;
}
// nastavi google analytics + META
if (!isset($_SESSION['pref']['ga']) OR ! isset($_SESSION['pref'][$_SESSION['lang'] . '_meta_title']) OR ! isset($_SESSION['pref'][$_SESSION['lang'] . '_meta_keywords']) OR ! isset($_SESSION['pref'][$_SESSION['lang'] . '_meta_description'])) {
    $result = mysql_query('SELECT general_meta_title_' . $_SESSION['lang'] . ', general_meta_keywords_' . $_SESSION['lang'] . ', general_meta_description_' . $_SESSION['lang'] . ', google_analytics FROM ' . TABLE_PREFIX . 'settings WHERE 1;');
    $meta = mysql_fetch_assoc($result);
    mysql_free_result($result);
    if (!isset($_SESSION['pref']['ga'])) {
        if (!empty($meta['google_analytics']))
            $_SESSION['pref']['ga'] = $meta['google_analytics'];
    }
    $_SESSION['pref'][$_SESSION['lang'] . '_meta_title'] = $meta['general_meta_title_' . $_SESSION['lang']];
    $_SESSION['pref'][$_SESSION['lang'] . '_meta_keywords'] = $meta['general_meta_keywords_' . $_SESSION['lang']];
    $_SESSION['pref'][$_SESSION['lang'] . '_meta_description'] = $meta['general_meta_description_' . $_SESSION['lang']];
}
$meta[$_SESSION['lang']]['PAGE_TITLE'] = $_SESSION['pref'][$_SESSION['lang'] . '_meta_title'];
$meta[$_SESSION['lang']]['DESCRIPTION'] = $_SESSION['pref'][$_SESSION['lang'] . '_meta_description'];
$meta[$_SESSION['lang']]['KEYWORDS'] = $_SESSION['pref'][$_SESSION['lang'] . '_meta_keywords'];
// nastaví defaultny jazyk END

$test_payment = false;

define('GP_MERCHANT_NUMBER', 7322248354);

if ($test_payment) {
    define('GP_PUBLIC_KEY_FILE', __DIR__.'/../webpay/test/gpe.signing_test.pem');
    define('GP_PRIVATE_KEY_FILE', __DIR__.'/../webpay/test/gpwebpay-pvk.key');
    define('GP_PRIVATE_KEY_PASS', 'bSDFT8By6dceVugJ7UnWak');
    define('GP_WEBPAY_URL', 'https://test.3dsecure.gpwebpay.com/pgw/order.do');
} else {
    define('GP_PUBLIC_KEY_FILE', __DIR__.'/../webpay/prod/gpe.signing_prod.pem');
    define('GP_PRIVATE_KEY_FILE', __DIR__.'/../webpay/prod/gpwebpay-pvk.key');
    define('GP_PRIVATE_KEY_PASS', 'bSDFT8By6dceVugJ7UnWak');
    define('GP_WEBPAY_URL', 'https://3dsecure.gpwebpay.com/pgw/order.do');
}

// trustpay
define('TRUSTPAY', TRUE);
define('TRUSTPAY_KEY', '9Ug1LwIh26NbKBZ6BABDwyExAGPwa53s');
define('TRUSTPAY_AID', '2107867498');
define('TRUSTPAY_CURRENCY', 'EUR'); //'EUR'
define('TRUSTPAY_URL_SUCCESS', 'https://www.growmedica.sk/sk/eshop/kosik?tps=success');
define('TRUSTPAY_URL_CANCEL', 'https://www.growmedica.sk/sk/eshop/kosik?tps=cancel');
define('TRUSTPAY_URL_ERROR', 'https://www.growmedica.sk/sk/eshop/kosik?tps=error');
define('TRUSTPAY_URL_NOTIFICATION', 'https://www.growmedica.sk/sk/eshop/kosik?tps=notofication');
define('TRUSTPAY_PAYMENT_ID', '6'); // na vymazanie
define('TRUSTPAY_TRANSFER_PAYMENT_ID', '6');
define('TRUSTPAY_CREDIT_PAYMENT_ID', '7');
// trustpay END
//
global $cTranslator;
require_once("classes/class.language_translator.php");
$cTranslator = new Translator();

global $user;
require_once("classes/class.user.php");
$user = new User();

//global $navigation;
require_once("classes/class.navigation.php");
//$navigation = new Navigation();

require_once("classes/lib.debug.php");
require_once("classes/class.Exceptions.php");
//require_once("classes/class.image.php");
require_once("classes/class.message.php");
require_once("classes/class.string.php");
require_once("classes/class.menu.php");
require_once("classes/class.html.php");
require_once("classes/class.database.php");
require_once("classes/class.send_mail.php");
require_once("classes/class.eshop.php");
//require_once("classes/class.Paginator.php");
require_once("function.php");
//
// simple image
require_once('classes/class.image.php');
try {
    $db = new PDO('mysql:host='.DB_HOSTNAME.';dbname='.DB_DATABASE, DB_USERNAME, DB_PASSWORD);
    $db->exec("set names utf8");
    $db->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );


} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
}
