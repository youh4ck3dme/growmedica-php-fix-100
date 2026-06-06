<?
require_once("../shared/config.inc.php");
$path = '/photos';
$fullpath = ROOTDIR . $path;
if ($_GET['popup'] == '1') { // Klikateľný náhľad
    $param = getimagesize('..' . $path . '/' . $_GET['image-path']);
} elseif ($_GET['popup'] == '2') { // Malý, neklikateľný náhľad
    $param = getimagesize('..' . $path . '/' . $_GET['image-path']);
} elseif ($_GET['popup'] == '3') { // Veľký, neklikateľný
    $param = getimagesize(str_replace('thumbnail', 'original', '..' . $path . '/' . $_GET['image-path']));
}
$im_str = str_replace('thumbnail', 'thumbail', $fullpath . $_GET['image-path']);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>PicProcessor Popup</title>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> <!--iso-8859-2-->
        <script language="JavaScript" type="text/javascript">
            var width = '<?= ($param[0] + 30) ?>';
            var height = '<?= ($param[1] + 80) ?>';
            window.resizeTo(width, height);
            window.moveTo((screen.width - width) / 2, (screen.height - height) / 2);
        </script>
    </head>
    <body>
        <div class="col-md-12">
            <?
            $image_path = array_reverse(explode('/', $_GET['image-path']));
            if ($_GET['popup'] == '1') {  // Klikateľný náhľad
                echo '<a href="' . $fullpath . '/thumbnail/' . $image_path[0] . '" class="fancybox" rel="' . SEO_TITLE . '" ><img src="' . $fullpath . '/thumbnail/' . $image_path[0] . '" alt="' . SEO_TITLE . '" border="0" /></a>';
            } else if ($_GET['popup'] == '2') {  // Malý, neklikateľný náhľad
                echo '<img src="' . $fullpath . '/thumbnail/' . $image_path[0] . '" alt="' . SEO_TITLE . '" />';
            } else if ($_GET['popup'] == '3') {  // Veľký, neklikateľný
                echo '<img src="' . $fullpath . '/original/' . $image_path[0] . '" alt="' . SEO_TITLE . '" class="photo" />';
            }
            ?>
        </div>
    </body>
</html>