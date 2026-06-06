<?
if($Row['module'] != 'article') 
    echo '<link rel="stylesheet" type="text/css" href="' . fileWithLastChange('css/mod_eshop.css') . '" />';

$catalogue = new Catalogue;
$catalogue->set_catalogue_limit(20);
$catalogue->set_catalogue_order('rand()');
//$catalogue->set_catalogue_menu_id();
$catalogue->set_dph_price_visibility(VAT_VISIBILITY);
if (!empty($_GET['manufacturer'])) {
    $catalogue->set_manufacturer($_GET['manufacturer']);
}
$catalogue->set_filter($setFilter); // new, action, sale, recommend

$products = $catalogue->get_catalogue();
// - //
// PRED KAZDYM POUZITIM TREBA NAJPRV OBJEKT UNSERIALIZOVAT ZO SESSION
// A PO ZMENE OBSAHU OPAT SERIALIZOVAT
$obj_cart = unserialize($_SESSION['serialized_cart']);
//$obj_cart->set_dph_price_visibility($catalogue->get_dph_price_visibility());
/*
if (isset($_POST)) {
    $obj_cart->insert_item($_POST['product_id'], $_POST['color'], $_POST['amount'], $_POST['price_item']);
}
*/
$_SESSION['serialized_cart'] = serialize($obj_cart);
$obj_cart = unserialize($_SESSION['serialized_cart']);
/*
$obj_paginator = new Paginator;

$obj_paginator->set_items_per_page($catalogue->get_catalogue_limit()); //  pocet zobrazenych poloziek na 1 stranke
$obj_paginator->set_items_count($catalogue->get_catalogue_items_count()); //  pocet poloziek v databaze

$obj_paginator->set_params_base(Menu::getHyperLinkById($navigateId)); //base... to ani netreba menit
$obj_paginator->set_params($navigateArrayUrlWithoutBase);
*/
?>
<div id="catalogue" class="carousel<?= (count($products) == 0 ? ' empty' : ''); ?>">
                        <?
                        if (count($products) != 0) {
                            foreach ($products as $rowC) {
                                ?>
                                <div class="col-lg-3 col-md-3 col-sm-3 col-xs-6 col-vs-6 no-gutter">
                                    <div class="item">
                                        <a href="<?= ROOTDIR . '/' . Menu::getHyperLinkById(ESHOP_MAIN_CATEGORY) ?>/produkt/<?= String::SEOFriendlyText($rowC->name) . '/' . $rowC->product_id ?>" title="<?= $rowC->name ?>">
                                            <div class="crust ratio-4_3">
                                                <div class="core">
                                                    <?
                                                    if (is_file("./photos/thumbnail/" . $rowC->image_src)) { ?>
                                                        <img src="photos/thumbnail/<?= $rowC->image_src; ?>" alt="<?= $rowC->name; ?>" /><?
                                                    } else {
                                                        echo '<center>' . $cTranslator->getTranslation('Žiaden obrázok', 0) . '</center>';
                                                        echo '<img src="images/wrapper/no-product-photo.png" alt="" />';
                                                    }
												//ZAKOMENTOVANE ZOBRAZOVANIE PRIZNAKOV PRODUKTU V ZOZNAMOCH	
													/*	
                                                    if($rowC->delivery_time == '1') {
                                                        echo '<div class="delivery-time"><img src="images/wrapper/icon-mame-skladom.png" alt="' . $cTranslator->getTranslation('skladom', 0) . '" /></div>';
                                                    }
                                                    else {
                                                        echo '<div class="delivery-time"><img src="images/wrapper/icon-na-objednavku.png" alt="' . $cTranslator->getTranslation('na objednávku', 0) . '" /></div>';
                                                    }
													*/
                                                    ?>
                                                    <div class="status-container">
                                                        <?
                                                        /*
                                                        if (strtotime(date("Y-m-d H:i")) < strtotime($rowC->date . NEW_PRODUCT_LENGTH)) {
                                                            echo '<span class="new">' . $cTranslator->getTranslation('nový', 0) . '</span>';
                                                        }
                                                        */
													//ZAKOMENTOVANE ZOBRAZOVANIE PRIZNAKOV PRODUKTU V ZOZNAMOCH	
														/*
                                                        if ($rowC->novelty == '1') {
                                                            echo '<span class="new">' . $cTranslator->getTranslation('nový', 0) . '</span>';
                                                        }
                                                        if ($rowC->sale == '1') {
                                                            echo '<span class="sale">' . $cTranslator->getTranslation('výpredaj', 0) . '</span>';
                                                        }
                                                        if ($rowC->recommend == '1') {
                                                            echo '<span class="recommended">' . $cTranslator->getTranslation('odporúčaný', 0) . '</span>';
                                                        }
                                                        if ($rowC->action == '1') {
                                                            echo '<span class="discount' . ($rowC->action == '1' ? ' action' : '') . '">' . $cTranslator->getTranslation('akcia', 0) . '</span>';
                                                        }
														*/
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="directive">
                                                <h2><?= $rowC->name; ?></h2>
                                                <div class="price-box">
                                                    <?
                                                    if (VAT_VISIBILITY === TRUE) {
                                                        echo '<p class="price old">' . (!empty($rowC->price_old) ? number_format(($rowC->price_old / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;' : '') . '</p>';
                                                        echo '<p class="price">' . number_format(($rowC->price / VAT_COEFFICIENT), 2, '.', ' ') . '&nbsp;&euro;</p>';
                                                    }
                                                    else {
                                                        echo '<p class="price old">' . (!empty($rowC->price_old) ? number_format($rowC->price_old, 2, '.', ' ') . '&nbsp;&euro;' : '') . '</p>';
                                                        echo '<p class="price">' . number_format($rowC->price, 2, '.', ' ') . '&nbsp;&euro;</p>';
                                                    }
                                                    echo '<div class="discount-box">';
												//ZAKOMENTOVANE ZOBRAZOVANIE ZLAVY PRODUKTU V ZOZNAMOCH	
                                                   /*
												    if ($rowC->price_old > 0 AND $rowC->price < $rowC->price_old) {
                                                        
                                                            echo '<p class="percentage-discount"><span>' . $cTranslator->getTranslation('Zľava') . '</span><br />' . percentageDiscount($rowC->price, $rowC->price_old, 0) . '<span>%</span></p>';            
                                                        
                                                    }
													*/
                                                    echo '</div>';
                                                    ?>
                                                </div>
                                            </div>
                                        </a>
                                        <?
                                            if($rowC->delivery_time == '1') {
                                                ?>
                                                <div class="add-to-cart" data-product="<?= $rowC->product_id; ?>" data-price="<?= $rowC->price; ?>" data-redirect="<?= ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik/step1" data-cancel="<?= $cTranslator->getTranslation('Pokračovať v nákupe', 0); ?>" data-confirm="<?= $cTranslator->getTranslation('Pokračovať k pokladni', 0); ?>" data-message="<?= $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0); ?>" title="<?= $cTranslator->getTranslation("Kúpiť", 0); ?>"><i class="icon icon-cart"></i></div>
                                                <?
                                            }
                                            else {
											//POVODNA CLASSA DIV-KA ABY MAL KOSIK INU FARBU class="add-to-cart on-request"
                                                ?>
                                                <div class="add-to-cart" data-product="<?= $rowC->product_id; ?>" data-price="<?= $rowC->price; ?>" data-redirect="<?= ROOTDIR . '/' . Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY); ?>/kosik/step1" data-cancel="<?= $cTranslator->getTranslation('Pokračovať v nákupe', 0); ?>" data-confirm="<?= $cTranslator->getTranslation('Pokračovať k pokladni', 0); ?>" data-message="<?= $cTranslator->getTranslation('Produkt bol pridaný do košíka', 0); ?>" title="<?= $cTranslator->getTranslation("Kúpiť", 0) . ' ' . $cTranslator->getTranslation('na objednávku', 0); ?>"><i class="icon icon-cart"></i></div>
                                                <?
                                            }
                                        ?>
                                    </div>
                                </div>
                                <?
                            }
                        } else {
                            echo '<p>' . $cTranslator->getTranslation("V tejto kategórii sa nič nenachádza", 0) . '</p>';
                        }
                        ?>
                    </div>

<script type="text/javascript" src="js/mod_eshop.js?v=20170220"></script>