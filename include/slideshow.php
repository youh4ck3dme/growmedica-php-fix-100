<?
$slideshow_print = false;
$sql = "SELECT slideshow FROM " . TABLE_PREFIX . "menu WHERE menu_id ='" . $navigateId . "';";
$query = @mysql_query($sql);
$row = mysql_fetch_array($query);



if ($row['slideshow'] == 1) {

    if ($navigateEnd == "41") {
        $queryStringSlideshow = "select * from " . TABLE_PREFIX . "menu_slideshow_prepojenie as p left join " . TABLE_PREFIX . "menu_slideshow as s on (p.menu_slideshow_id  = s.menu_slideshow_id ) where 1 and p.menu_id = '" . $navigateId . "' and (s.menu_slideshow_id='31' or s.menu_slideshow_id='32') order by p.sorter ASC;";
        $ResultSlideshow = mysql_query($queryStringSlideshow);
    } else {
        $queryStringSlideshow = "select * from " . TABLE_PREFIX . "menu_slideshow_prepojenie as p left join " . TABLE_PREFIX . "menu_slideshow as s on (p.menu_slideshow_id  = s.menu_slideshow_id ) where 1 and p.menu_id = '" . $navigateId . "' and s.menu_slideshow_id!='31' and s.menu_slideshow_id!='32' order by p.sorter ASC;";
        $ResultSlideshow = mysql_query($queryStringSlideshow);
    }

    if ($ResultSlideshow) {
        if (mysql_num_rows($ResultSlideshow) > 0) {
            $slideshow_print = true;
            $slides_count = 0;
            while ($Slideshow = mysql_fetch_array($ResultSlideshow)) {
                if ($Slideshow['src'] != NULL) {
                    $slides_count = $slides_count + 1;
                    $SlideshowVal['src'][] = $Slideshow['src'];
                    $SlideshowVal['name'][] = $Slideshow[$_SESSION['lang'] . '_name'];
                    $SlideshowVal['popis'][] = $Slideshow[$_SESSION['lang'] . '_popis'];
                    $SlideshowVal['link'][] = $Slideshow['link'];
                }
            }
            $numSlideshow = $slides_count;
        } else
            $slideshow_print = false;
    } else
        die(mysql_error());

    if ($slideshow_print == true) {
        if ($numSlideshow != 0) {
            ?>
            <script src="js/jquery.cycle.min.js" type="text/javascript" defer></script>
            <script type="text/javascript">
                $(document).ready(function () {
                    $('#slider-container').cycle({
                        pager: '#control-panel',
                        fit:           1,
                        height:        'auto',
                        width:        '100%',
                        timeout:       4000,
                        fx:            'fade',
                        prev: '#prev_slide',
                        next: '#next_slide'
                    });
                });
            </script>

            <?
            $string_child = 'select child_of from ' . TABLE_PREFIX . 'menu where menu_id="' . $navigateId . '"';
            $result_child = mysql_query($string_child);
            $row_child = mysql_fetch_array($result_child);
            ?>
            <div id="slider">
                <div id="slider-container">
                    <?php
                    for ($i = 1; $i <= $numSlideshow; $i++) {
                        ?>
                        <div id="slide_<?= $i; ?>" class="slide" style="background-image: url(<?= ROOTDIR . '/photos/slideshow/' . $SlideshowVal['src'][$i - 1]; ?>);">
                            <div class="container">
                                <div class="col-md-4 col-md-offset-7 col-sm-4 col-sm-offset-7 col-xs-6">
                                    <?
                                    if (!empty($SlideshowVal['popis'][$i - 1]) AND trim($SlideshowVal['popis'][$i - 1]) != '') {
                                        ?>
                                        <div class="slide-text">
                                            <div class="slide-content">
                                                <div class="popis"><?= $SlideshowVal['popis'][$i - 1]; ?></div>
                                                <?
                                                if($SlideshowVal['link'][$i - 1] AND $SlideshowVal['link'][$i - 1] > 1) {
                                                    ?>
                                                    <a href="<?= Menu::getHyperlinkById($SlideshowVal['link'][$i - 1]); ?>" class="button read-more"><?= $cTranslator->getTranslation('čítaj viac'); ?></a>
                                                    <?
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <?
                                    }                                    
                                    ?>
                                </div>
                            </div><!--
                            <div class="container slideimage">
                                <span class="helper"></span>
                                <img src="photos/slideshow/<?= $SlideshowVal['src'][$i - 1]; ?>" alt="<?= $SlideshowVal['name'][$i - 1]; ?>" />
                            </div>-->
                        </div>
                        <?
                    }
                    ?>
                </div>
                <div id="control-panel"></div>
                <?
                if ($numSlideshow > 1) {
                    ?>
                    <div id="slider_nav">
                        <div class="container">
                            <a href="#" id="prev_slide" aria-label="Predchádzajúci slide"><i class="icon icon-left-open-big"></i></a>
                            <a href="#" id="next_slide" aria-label="Nasledujúci slide"><i class="icon icon-right-open-big"></i></a>
                        </div>
                    </div>
                    <?
                }
                ?>
            </div>
            <?
        }
    }
}
?>
