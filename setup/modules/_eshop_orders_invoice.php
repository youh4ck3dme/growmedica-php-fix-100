<?
require_once("../../shared/config.inc.php");

if (!$user->isAdmin()) {
    header('Content-Type: text/html; charset=utf-8');
    echo 'prístup obmedzený';
    exit;
}
if (empty($_GET['order_id'])) {
    header('Content-Type: text/html; charset=utf-8');
    echo 'Nebolo zadané ID objednávky';
    exit;
}
?>
<!DOCTYPE>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8' />
        <title><?= PROJECT_NAME ?> - faktúra č.: </title>
        <?
        if ($_GET['action'] == 'print') {
            echo '<script type="text/javascript">window.onload=function(){self.print();}</script>';
        }
        ?>
    </head>
    <body>
        <?
        $invoice = Cart::generate_invoice($_GET['order_id']);
        echo $invoice;
        ?>
    </body>
</html>
<?
die();

?>