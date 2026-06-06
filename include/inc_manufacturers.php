<?
$query = 'SELECT * FROM ' . TABLE_PREFIX . 'manufacturer 
            WHERE 1 
            AND (logo IS NOT NULL AND logo != "") 
            AND (logo_background IS NOT NULL AND logo_background != "") 
            ORDER BY sorter, sk_name ASC';
if($result = mysql_query($query)) {
    
        ?>
        <div class="premium-features diagonal-dark">            
            <div class="container">
                <div id="page-items-container">
                        <div id="features" class="page-items">
                            <?
                                while ($row = mysql_fetch_object($result)) {
                                    ?>
                                        <div class="item">
                                            <a href="<?= Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '?manufacturer=' . $row->manufacturer_id; ?>" title="" data-image="images/manufacturers/thumbnail/<?= $row->logo_background; ?>" style="background-image: url(images/manufacturers/thumbnail/<?= $row->logo_background; ?>);">
                                                <div class="crust ratio-1_1">
                                                    <div class="core">
                                                        <img class="whitened" src="images/manufacturers/thumbnail/<?= $row->logo; ?>" alt="logo <?= $row->sk_name; ?>" />
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    <?
                                }
                                mysql_free_result($result);                           
                            ?>
                        </div>
                    
                </div>
                <div class="cf"></div>
            </div>
        </div>
        <?
    
}
else
    echo 'Error (' . mysql_errno() . '): ' . mysql_error();
?>