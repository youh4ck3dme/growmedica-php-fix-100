<?
require_once('../shared/classes/class.eshop.php');
/*
//scrape images from url

$html = file_get_contents('http://www.website.any');
preg_match_all( '|<img.*?src=[\'"](.*?)[\'"].*?>|i',$html, $matches );
echo $matches[ 1 ][ 0 ];

<img alt="this field is variable" title="this one too" itemprop="photo" border="0" style="width:608px;" src="imgurl.jpg">
preg_match('/<img[^>]*itemprop="photo"[^>]*src="([^"]+)">/',$source,$matches);



$page = file_get_contents('http://example.com/images.php');
$doc = new DOMDocument(); 
$doc->loadHTML($page);
$images = $doc->getElementsByTagName('img'); 
foreach($images as $image) {
    echo $image->getAttribute('src') . '<br />';
}

*/

switch ($_GET['action']) {
    case 'upload': {
            ?>
            <div id="leftMenu">
                <h2>Import</h2>
                <p>Import produktov od dodávateľa</p>
                <?
                if($_SESSION['imp_no']) {
                	?>
	                <h3><?= getSuplier($_SESSION['suplier_id']); ?></h3>
	                <p><?= date_format(date_create($_SESSION['imp_no']),"j. n. Y H:i:s") . '<br />cenový koeficient: ' . $_SESSION['price_ratio']; ?></p>
	                <?
	            }
	            ?>
                <ul class="side-menu">
                    <li><a<?= ($_GET['group'] == 'exist_current-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist_current-1">Produkty v eshope</a></li>
                    <li><a<?= ($_GET['group'] == 'exist_alter-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist_alter-1">Produkty v eshope s iným dod.</a></li>
	                <li><a<?= ($_GET['group'] == 'exist-sellout' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist-sellout">Produkty nenájdené v importe</a></li>
	                <li><a<?= ($_GET['group'] == 'exist-unmarked' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist-unmarked">Produkty bez kódu</a></li>
                    <li><a<?= ($_GET['group'] == 'promo-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=promo-1">Promo na sklade</a></li>
                    <li><a<?= ($_GET['group'] == 'promo-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=promo-0">Promo nedostupné</a></li>
                    <li><a<?= ($_GET['group'] == 'news-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=news-1">Novinky na sklade</a></li>
                    <li><a<?= ($_GET['group'] == 'news-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=news-0">Novinky nedostupné</a></li>
                    <li><a<?= ($_GET['group'] == 'eol-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=eol-1">EOL na sklade</a></li>
                    <li><a<?= ($_GET['group'] == 'eol-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=eol-0">EOL nedostupné</a></li>
                    <li><a<?= ($_GET['group'] == 'other-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=other-1">Ostatné na sklade</a></li>
                    <li><a<?= ($_GET['group'] == 'other-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=other-0">Ostatné nedostupné</a></li>
                </ul>
            </div>
            <div id="moduleContent">
                <h1>Import</h1>
                <p>test</p>
                <?
                	if($_POST['source_link']) {
                		/*
                		libxml_use_internal_errors(true);

						$doc = simplexml_load_string($_POST['source_link']);
						$xml = explode("\n", $_POST['source_link']);

						if (!$doc) {
						    $errors = libxml_get_errors();

						    foreach ($errors as $error) {
						        echo display_xml_error($error, $xml);
						    }

						    libxml_clear_errors();
						}*/

						libxml_use_internal_errors(true);
						$xml = simplexml_load_string($_POST['source_link']);
						if (!$xml) {
						    echo "Failed loading XML\n";
						    foreach(libxml_get_errors() as $error) {
						        echo "\t", $error->message;
						    }
						}
						$importFileName = $_POST['source_link'];
                	}
                	else {
                		$importFileName = $_FILES['source_file']['name'];
                		if($_FILES['source_file']['error'] > 0) {
	                		$error = 1;
	                		echo '<div class="error">Pri nahrávaní došlo k chybe.</div>';
	                	}
	                	if($_FILES['source_file']['type'] != 'text/xml') {
	                		$error = 1;
	                		echo '<div class="error">Nepodporovaný typ súboru.</div>';
	                	}
	                	/*if($_FILES['source_file']['size'] > 500000) {
	                		die('File uploaded exceeds maximum upload size.');
	                	}*/
	                	if(!$error)
	                		$xml = simplexml_load_file($_FILES['source_file']['tmp_name']);
                	}
                	$query = 'SELECT suplier FROM ' . TABLE_PREFIX . 'product_suplier WHERE 1 AND suplier_id = ' . $_POST['suplier_id'];
                	if ($result = mysql_query($query)) {
                		if (mysql_num_rows($result) == 1) {
                			$suplier = mysql_fetch_row($result);
                		}
                	} else {
                		die(mysql_error());
                	}
                	mysql_free_result($result);

                	echo '<p>Import produktov od dodávateľa <strong class="red">' . $suplier[0] . '</strong>, zadaný cenový koeficient: <strong class="red">' . $_POST['price_ratio'] . '</strong></p>';

                	if(!$error) {
                		mysql_query('TRUNCATE TABLE ' . TABLE_PREFIX . 'product_import;');
                		echo '<p>Načítanie importného súboru &ldquo;<strong class="red">' . $importFileName . '</strong>&rdquo; bolo úspešné.</p>';
                		//echo 'no: ' . $xml->count();
                		$_SESSION['imp_no'] = date("Y-m-d H:i:s");
                		$_SESSION['suplier_id'] = $_POST['suplier_id'];
                		$_SESSION['price_ratio'] = $_POST['price_ratio'];
                		//unset($_SESSION['imp_no']);

	                	$i = 0;
						foreach($xml->children() as $product) {
							unset($queryInclude1);
							if(is_numeric(preparePrice($product->dac_price))) {
								if($product->images->img) {
								    foreach($product->images->img as $image) {
									    $queryInclude1 .= $image . ';';
									}
									$queryInclude1 = rtrim($queryInclude1, ';');
								}
								$query = 'INSERT INTO ' . TABLE_PREFIX . 'product_import 
														(suplier_id, 
														partno, 
														code_ean, 
														eol, 
														name, 
														category, 
														dac_price, 
														fd_price, 
														price_koef, 
														stock, 
														manufacturer, 
														url, 
														promo, 
														news, 
														images, 
														imp_no)
												VALUES (' . $_POST['suplier_id'] . ',
														"' . $product->partno . '",
														"' . $product->ean . '",
														"' . $product->eol . '",
														"' . mysql_real_escape_string($product->name) . '",
														"' . mysql_real_escape_string($product->category) . '",
														' . preparePrice($product->dac_price) . ',
														' . preparePrice($product->fd_price) . ',
														' . round((preparePrice($product->dac_price) * $_POST['price_ratio'] * 1.2), 2 ) . ',
														' . $product->stock . ',
														"' . mysql_real_escape_string($product->manufacturer) . '",
														"' . mysql_real_escape_string($product->url) . '",
														"' . $product->promo . '",
														"' . $product->news . '", 
														' . ($queryInclude1 ? '"' . mysql_real_escape_string($queryInclude1) . '"' : 'NULL') . ',
														"' . $_SESSION['imp_no'] . '")';
								if (!$result = mysql_query($query)) {
									if (mysql_errno())
										print("MySql Error (" . mysql_errno() . "): " . mysql_error());
								}
								else {
									$i++;
								}
							}
						}
						echo '<p>Bolo načítaných <strong class="red">' . $i . '</strong> produktov.</p>';
						echo '<p>&nbsp;</p>';

						// kategory
						/*
						$query = 'SELECT category FROM ' . TABLE_PREFIX . 'product_import
									WHERE 1 
									AND imp_no = "' . $_SESSION['imp_no'] . '"
									GROUP BY category 
									ORDER BY category ASC';
						if($result = mysql_query($query)) {
							echo '<p>Kategórie: <strong>' . mysql_num_rows($result) . '</strong></p>';
							echo '<ul>';
							while ($row = mysql_fetch_object($result)) {
								echo '<li>' . $row->category . '</li>';
							}
							echo '</ul>';
						}
						else {
							echo 'Error (' . mysql_errno() . '): ' . mysql_error();
						}*/


						// produkty, ktoré sú v databáze
						/*
						$query = 'SELECT p.product_id, pi.pi_id, pi.name, pi.category, p.code_ean, p.*, pi.* FROM ' . TABLE_PREFIX . 'product_import AS pi 
									JOIN ' . TABLE_PREFIX . 'product AS p ON(p.code_ean = pi.code_ean OR p.code_suplier = pi.partno) 
									WHERE 1 
									AND pi.imp_no = "' . $_SESSION['imp_no'] . '"
									AND p.suplier_id = ' . $_POST['suplier_id'];
						if($result = mysql_query($query)) {
							?>
							<h3>Produkty, ktoré už sú v eshope: <strong><?= mysql_num_rows($result); ?></strong></h3>
							<form method="post" enctype="multipart/form-data" name="update_products" id="update_products" action="index.php?module=eshop_import&action=update&group=exist_current-1">
								<table class="tableform">
									<thead>
										<tr>
											<th></th>
											<th>počet</th>
											<th>ID</th>
											<th>EAN</th>
											<th>kat.</th>
											<th>názov</th>
											<th>cena</th>
										</tr>
									</thead>
									<tbody>
								<?
								$exist = [];
								$i = 0;
								while ($row = mysql_fetch_object($result)) {
									$i++;
									array_push($exist, $row->pi_id);
									?>
									<tr>
										<td>
											<input type="checkbox" name="pi_id[<?= $i; ?>]" value="<?= $row->pi_id; ?>" />
											<input type="hidden" name="product_id[<?= $i; ?>]" value="<?= $row->product_id; ?>" />
										</td>
										<td style="color: <?= ($row->stock > 0 ? 'green' : 'red'); ?>; text-align: right"><?= $row->stock; ?></td>
										<td style="text-align: right;"><?= $row->product_id; ?></td>
										<td style="text-align: right;"><?= $row->code_ean; ?></td>
										<td><?= $row->category; ?></td>
										<td><?= $row->name; ?></td>
										<td style="text-align: right;"><?= number_format($row->price, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
									</tr>
									<?
								}
								?>	
									</tbody>
								</table>
								<input name="update" id="update" type="submit" value="Aktualizovať" />
							</form>
							<div class="clear"></div>
							<hr />
							<?
							mysql_free_result($result);
							unset($row);
						}
						else {
							echo 'Error (' . mysql_errno() . '): ' . mysql_error();
						}
						*/

						// produkty, ktoré sú v databáze
						listPaired('exist_current-1', TRUE);

						// produkty, ktoré sú v databáze, ale majú iného dodávateľa (podľa EAN kódu)
						/*
						echo $queryExistCurrent = 'SELECT p.product_id, pi.pi_id, pi.name, pi.category, p.sk_name, p.code_ean, p.price, pi.dac_price, pi.price_koef, p.suplier_id FROM ' . TABLE_PREFIX . 'product_import AS pi 
									JOIN ' . TABLE_PREFIX . 'product AS p ON(p.code_ean = pi.code_ean OR p.code_suplier = pi.partno) 
									WHERE 1 
									AND pi.imp_no = "' . $_SESSION['imp_no'] . '"
									AND p.suplier_id != ' . $_POST['suplier_id'] . '
									AND p.price > pi.price_koef';
						if($result = mysql_query($queryExistCurrent)) {
							?>
							<h3>Produkty podľa EAN kódu, ktoré už sú v eshope, majú iného dodávateľa a lepšiu cenu: <strong><?= mysql_num_rows($result); ?></strong></h3>
							<form method="post" enctype="multipart/form-data" name="update_products" id="update_products" action="index.php?module=eshop_import&action=update&group=exist_alter-1">
								<table class="tableform">
									<thead>
										<tr>
											<th></th>
											<th>počet</th>
											<th>ID</th>
											<th>EAN</th>
											<th>kat.</th>
											<th>názov</th>
											<th>cena eshop</th>
											<th>cena dod. x koef.</th>
											<th>rozd.</th>
											<th>terajší dodávateľ</th>
										</tr>
									</thead>
									<tbody>
								<?
								$existAlter = [];
								$i = 0;
								while ($row = mysql_fetch_object($result)) {
									$i++;
									array_push($existAlter, $row->pi_id);
									?>
									<tr>
										<td>
											<input type="checkbox" name="pi_id[<?= $i; ?>]" value="<?= $row->pi_id; ?>" />
											<input type="hidden" name="product_id[<?= $i; ?>]" value="<?= $row->product_id; ?>" />
										</td>
										<td style="color: <?= ($row->stock > 0 ? 'green' : 'red'); ?>; text-align: right"><?= $row->stock; ?></td>
										<td style="text-align: right;"><?= $row->product_id; ?></td>
										<td style="text-align: right;"><?= $row->code_ean; ?></td>
										<td><?= $row->category; ?></td>
										<td><?= $row->name; ?></td>
										<td style="text-align: right;"><?= number_format($row->price, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
										<td style="text-align: right;"><?= number_format($row->price_koef, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
										<td style="text-align: right;"><?= number_format(($row->price - $row->price_koef), 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
										<td><?= $row->suplier_id . ' | ' . getSuplier($row->suplier_id); ?></td>
									</tr>
									<?
								}
								?>	
									</tbody>
								</table>
								<input name="alter" type="hidden" value="1" />
								<input name="updateS" id="updateS" type="submit" value="Aktualizovať" />
							</form>
							<div class="clear"></div>
							<hr />
							<?
							mysql_free_result($result);
							$_SESSION['existCurrent'] = $exist;
							
							$_SESSION['existAlter'] = $existAlter;
							unset($row);
						}
						else {
							echo 'Error (' . mysql_errno() . '): ' . mysql_error();
						}*/


						// produkty, ktoré sú v databáze, ale majú iného dodávateľa (podľa EAN kódu)
						listPaired('exist_alter-1', TRUE);						
						listSellOut('exist-sellout', TRUE);
						listSellOut('exist-unmarked', TRUE);


						// produkty nezaradené
						//echo 'session exist: ' . implode(',', $_SESSION['exist']) . ' (' . count($_SESSION['exist']) . ')';
						//array_merge($exist, $existAlter);
						$query = 'SELECT * FROM ' . TABLE_PREFIX . 'product_import
									WHERE 1 
									AND imp_no = "' . $_SESSION['imp_no'] . '"
									AND pi_id NOT IN (' . implode(',', $_SESSION['exist']) . ')';
						if($result = mysql_query($query)) {
							echo '<p>Produkty, ktoré ešte nie sú zaradené: <strong>' . mysql_num_rows($result) . '</strong></p>';
							$noexist = [];
							while ($row = mysql_fetch_object($result)) {
								array_push($noexist, $row->pi_id);
							}
							mysql_free_result($result);
						}
						else {
							echo 'Error (' . mysql_errno() . '): ' . mysql_error();
						}
						$_SESSION['noexist'] = $noexist;
						

						// produkty nezaradené - promo, na sklade
						listUnpaired('promo-1');
						// produkty nezaradené - promo, nedostupné
						listUnpaired('promo-0');
						// produkty nezaradené - novinky, na sklade
						listUnpaired('news-1');
						// produkty nezaradené - novinky, nedostupné
						listUnpaired('news-0');
						// produkty nezaradené - výpredaj (EOL), na sklade
						listUnpaired('eol-1');
						// produkty nezaradené - výpredaj (EOL), nedostupné
						listUnpaired('eol-0');
						// produkty nezaradené - ostatné, na sklade
						listUnpaired('other-1');
						// produkty nezaradené - ostatné, nedostupné
						listUnpaired('other-0');
					}
					//unset($_SESSION['imp_no']);
                ?>
            </div>
            <?
        };
        break;
    case 'update': {
    		?>
            <div id="leftMenu">
                <h2>Import</h2>
                <p>Import produktov od dodávateľa</p>
                <h3><?= getSuplier($_SESSION['suplier_id']); ?></h3>
                <p><?= date_format(date_create($_SESSION['imp_no']),"j. n. Y H:i:s") . '<br />cenový koeficient: ' . $_SESSION['price_ratio']; ?></p>
                <ul class="side-menu">
                    <li><a<?= ($_GET['group'] == 'exist_current-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist_current-1">Produkty v eshope</a></li>
                    <li><a<?= ($_GET['group'] == 'exist_alter-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist_alter-1">Produkty v eshope s iným dod.</a></li>
	                <li><a<?= ($_GET['group'] == 'exist-sellout' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist-sellout">Produkty nenájdené v importe</a></li>
	                <li><a<?= ($_GET['group'] == 'exist-unmarked' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist-unmarked">Produkty bez kódu</a></li>
                    <li></li>
                    <li><a<?= ($_GET['group'] == 'promo-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=promo-1">Promo na sklade</a></li>
                    <li><a<?= ($_GET['group'] == 'promo-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=promo-0">Promo nedostupné</a></li>
                    <li><a<?= ($_GET['group'] == 'news-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=news-1">Novinky na sklade</a></li>
                    <li><a<?= ($_GET['group'] == 'news-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=news-0">Novinky nedostupné</a></li>
                    <li><a<?= ($_GET['group'] == 'eol-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=eol-1">EOL na sklade</a></li>
                    <li><a<?= ($_GET['group'] == 'eol-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=eol-0">EOL nedostupné</a></li>
                    <li><a<?= ($_GET['group'] == 'other-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=other-1">Ostatné na sklade</a></li>
                    <li><a<?= ($_GET['group'] == 'other-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=other-0">Ostatné nedostupné</a></li>
                </ul>
            </div>
    		<div id="moduleContent">
                <h1>Import</h1>
    			<?
    			//echo '<pre>' . var_dump($_POST['product_id']) . '</pre>';
    			//echo '<pre>' . var_dump($_POST['pi_id']) . '</pre>';
    			//echo '<pre>' . var_dump($_POST['price']) . '</pre>';
    			if($_POST['pi_id'] AND is_array($_POST['pi_id'])) {
    				foreach ($_POST['pi_id'] as $key => $value) {
	    				//echo 'pi_id: ' . $value . ' - product_id: ' . $_POST['product_id'][$key] . '<br />alter: ' . $_POST['alter'] . ', ' . $_SESSION['suplier_id'] . '<br />';
	    				$query = 'SELECT * FROM ' . TABLE_PREFIX . 'product_import WHERE 1 AND pi_id = ' . $value;
	    				if($result = mysql_query($query)) {
	    					if(mysql_num_rows($result) == 1) {
	    						$row = mysql_fetch_object($result);
	    						if($_POST['insert'] AND $_POST['insert'] == "1") {
	    							$insert_query = 'INSERT INTO ' . TABLE_PREFIX . 'product (
	    														sk_name, 
	    														sk_name_seo, 
	    														code_ean, 
	    														code_suplier, 
	    														url_suplier, 
	    														suplier_id, 
	    														date, 
	    														sale, 
	    														available, 
	    														delivery_time,
	    														manufacturer_id, 
	    														price) 
														VALUES ("' . mysql_real_escape_string($row->name) . '", 
																"' . String::SEOFriendlyText($row->name) . '", 
																"' . $row->code_ean . '",
																"' . $row->partno . '",
																"' . $row->url . '",
																' . $_SESSION['suplier_id'] . ',
																NOW(),
																"' . $row->eol . '",
																"0", 
																"' . ($row->stock > 0 ? '1' : '0') . '",
																' . (insertManufacturer($row->manufacturer) ? insertManufacturer($row->manufacturer) : 'NULL') . ',
																"' . $row->price_koef . '");';
									if(!mysql_query($insert_query))
										echo 'Error (' . mysql_errno() . '): ' . mysql_error();
									$product_id = mysql_insert_id();
									if($row->images) {
										
									}
	    						}
	    						else {
	    							//echo '<p>* ' . $key . ' | ' . $_POST['price'][$key] . '</p>';
		    						$update_query = 'UPDATE ' . TABLE_PREFIX . 'product 
		    										SET code_suplier = "' . $row->partno . '", '
		    											. ($_POST['price'][$key] ? ' price = "' . $row->price_koef . '", ' : '') . ' 
		    											delivery_time = "' . ($row->stock > 0 ? '1' : '0') . '", 
		    											url_suplier = "' . $row->url . '" 
		    											' . ($row->news == "1" ? ', recommend = "' . $row->news . '" ' : '') . '
		    											' . ($row->eol == "1" ? ', sale = "' . $row->eol . '" ' : '') 
		    											 . (($_POST['alter'] AND $_POST['alter'] == "1") ? ', suplier_id = ' . $_SESSION['suplier_id'] . ' ' : '') . '
		    										WHERE product_id = "' . $_POST['product_id'][$key] . '" ;';
		    						if(!mysql_query($update_query))
										echo 'Error (' . mysql_errno() . '): ' . mysql_error();
		                    		$product_id = $_POST['product_id'][$key];
		                    	}
	                    		updateStock($product_id, $row->stock);
	                    		unset($product_id);
								done($value);
	    					}
	    					else {
	    						echo '<p>error: mysql_num_rows: ' . mysql_num_rows($result) . ' ()</p>';
	    					}
		    				mysql_free_result($result);
		    				unset($row);
		    			}
		    			else {
		    				echo 'Error (' . mysql_errno() . '): ' . mysql_error();
		    			}
	    			}
	    		}

	    		if($_POST['remove'] AND $_POST['product_id'] AND is_array($_POST['product_id'])) {
	    			//echo 'remove: ' . implode($_POST['product_id'], ',') . '<br />';
	    			$delete_query = 'DELETE FROM ' . TABLE_PREFIX . 'product 
		    						SET available = "0" 
		    						WHERE product_id IN(' . implode($_POST['product_id'], ',') . ');';
		            if(!mysql_query($delete_query))
		            	echo 'Error (' . mysql_errno() . '): ' . mysql_error();
	    		}
	    		if($_POST['dontshow'] AND $_POST['product_id'] AND is_array($_POST['product_id'])) {
	    			//echo 'do not show: ' . implode($_POST['product_id'], ',') . '<br />';
	    			$update_query = 'UPDATE ' . TABLE_PREFIX . 'product 
		    						SET available = "0" 
		    						WHERE product_id IN(' . implode($_POST['product_id'], ',') . ');';
		            if(!mysql_query($update_query))
		            	echo 'Error (' . mysql_errno() . '): ' . mysql_error();
	    		}
	    		
	    		if($_GET['group'] AND strpos($_GET['group'], 'exist') !== FALSE) {
	    			if(strpos($_GET['group'], 'sellout') !== FALSE OR strpos($_GET['group'], 'unmarked') !== FALSE) {
						listSellOut($_GET['group']);
					}
					else {
						listPaired($_GET['group']);
					}
	    		}
	    		else {
    				listUnpaired($_GET['group']);
    			}
    			?>
    		</div>

            <script>
            	$(document).ready(function () {

            		$('.checkAll').on('click', function() {/*
            			if($(this).attr('data-check') == 'price')
            				$('tr.selected input[data-check="' + $(this).attr('data-check') + '"]:checkbox').prop('checked', this.checked);
            			else
            				$('input[data-check="' + $(this).attr('data-check') + '"]:checkbox').prop('checked', this.checked);*/

            			$('input[data-check="' + $(this).attr('data-check') + '"]:checkbox:not(:disabled)').prop('checked', this.checked);
            		});

            		$('table.importer input:checkbox').on('change', function(){
            			$('table.importer tbody').find('input[data-check="pi_id"]:checked, input[data-check="product_id"]:checked').each(function(){
            				var $tr = $(this).closest('tr');
            				$tr.addClass('selected');
            				$('input[data-check="price"]', $tr).removeAttr('disabled');
	            			//console.log('id: ' + $(this).val());
	            		});
            			$('table.importer tbody').find('input[data-check="pi_id"]:not(:checked), input[data-check="product_id"]:not(:checked)').each(function(){
            				var $tr = $(this).closest('tr');
            				$tr.removeClass('selected');
            				$('input[data-check="price"]', $tr).attr('disabled', 'disabled');
	            		});

            		});
            		

            		$('.confirm').on('click', function() {
            			var txt;
					    var r = confirm("Press a button!");
					    if (r == true) {
					        txt = "You pressed OK!";
					    } else {
					        txt = "You pressed Cancel!";
					    }
            		});

            	});
            </script>
    		<?
    	};
    	break;
    default: {
            ?>
            <div id="leftMenu">
                <h2>Import</h2>
                <p>Import produktov od dodávateľa</p>
                <?
                if($_SESSION['imp_no']) {
                	?>
	                <h3><?= getSuplier($_SESSION['suplier_id']); ?></h3>
	                <p><?= date_format(date_create($_SESSION['imp_no']),"j. n. Y H:i:s") . '<br />cenový koeficient: ' . $_SESSION['price_ratio']; ?></p>
	                <ul class="side-menu">
	                    <li><a<?= ($_GET['group'] == 'exist_current-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist_current-1">Produkty v eshope</a></li>
	                    <li><a<?= ($_GET['group'] == 'exist_alter-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist_alter-1">Produkty v eshope s iným dod.</a></li>
	                    <li><a<?= ($_GET['group'] == 'exist-sellout' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist-sellout">Produkty nenájdené v importe</a></li>
	                    <li><a<?= ($_GET['group'] == 'exist-unmarked' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=exist-unmarked">Produkty bez kódu</a></li>
	                    <li><a<?= ($_GET['group'] == 'promo-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=promo-1">Promo na sklade</a></li>
	                    <li><a<?= ($_GET['group'] == 'promo-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=promo-0">Promo nedostupné</a></li>
	                    <li><a<?= ($_GET['group'] == 'news-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=news-1">Novinky na sklade</a></li>
	                    <li><a<?= ($_GET['group'] == 'news-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=news-0">Novinky nedostupné</a></li>
	                    <li><a<?= ($_GET['group'] == 'eol-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=eol-1">EOL na sklade</a></li>
	                    <li><a<?= ($_GET['group'] == 'eol-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=eol-0">EOL nedostupné</a></li>
	                    <li><a<?= ($_GET['group'] == 'other-1' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=other-1">Ostatné na sklade</a></li>
	                    <li><a<?= ($_GET['group'] == 'other-0' ? ' class="selected"' : ''); ?> href="./index.php?module=eshop_import&action=update&group=other-0">Ostatné nedostupné</a></li>
	                </ul>
	                <?
	            }
	            ?>
            </div>
            <div id="moduleContent">
                <h1>Import produktov</h1>
                <form method="post" enctype="multipart/form-data" name="import_products" id="import_products" action="index.php?module=eshop_import&action=upload">
                    <table summary="" border="0" cellspacing="0" cellpadding="2" class="tableform">
                        <tr>
                            <th colspan="3">&nbsp;</th>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr>
                        	<td style="width: 180px;">Dodávateľ</td>
                        	<td style="width: 360px;">
	                        	<select name="suplier_id" id="suplier_id">
	                        		<option value=""></option>
	                        		<?
	                        			$query_combo = 'SELECT suplier_id, suplier FROM ' . TABLE_PREFIX . 'product_suplier WHERE 1 ORDER BY suplier ASC';
	                        			if ($result_combo = mysql_query($query_combo)) {
	                        				while ($row_combo = mysql_fetch_object($result_combo)) {
	                        					echo '<option value="' . $row_combo->suplier_id . '" ' . (($row_combo->suplier_id == $_POST["suplier_id"]) ? 'selected="selected"' : '') . '>' . $row_combo->suplier . '</option>';
	                        				}
	                        			}
	                        		?>
	                        	</select>
	                        	<div class="error red"></div>
	                        </td>
	                        <td><span class="tooltip">Vyberte dodávateľa</span></td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr class="conditional" style="display:none;">
                        	<td>Cenový koeficient</td>
                        	<td>
                        		<input type="text" id="price_ratio" name="price_ratio" />
                        		<div class="error red"></div>
                        	</td>
                        	<td></td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr class="conditional" style="display:none;">
                        	<td>Zdroj</td>
                        	<td>
                        		<input type="text" id="source_link" name="source_link" />
                        	</td>
                        	<td></td>
                        </tr>
                        <tr class="conditional" style="display:none;">
                        	<td></td>
                        	<td>
                        		<input type="file" id="source_file" name="source_file" />
                        		<div class="error red"></div>
                        	</td>
                        	<td></td>
                        </tr>
                        <tr>
                            <td colspan="3">&nbsp;</td>
                        </tr>
                        <tr class="conditional" style="display:none;">
                            <td>&nbsp;</td>
                            <td colspan="2">
                                <input name="upload" id="submit" type="submit" value="Nahrať" />
                            </td>
                        </tr>
                    </table>
                </form>
                <script>
                	var supliers = ["1"]; // "Apcom Slovakia s.r.o.", 
				    $(document).ready(function () {

				    	$('.select_all').on('click', function(){
							console.log('click');       
					    });

				    	$('#suplier_id').on('change', function() {
				    		if(jQuery.inArray($('#suplier_id').val(), supliers) !== -1) {
				            	console.log('ok');
				            	$('#suplier_id').siblings().empty();
				            	$('tr.conditional').show(150);
				            }
				            else {
				            	console.log('no');
				            	$('#suplier_id').siblings().html('Vybraný dodávateľ nemá nakonfigurovaný import.');
				            	$('tr.conditional').hide(150);
				            }
				    	});


				        $("#submit").click(function () {

				            var suplier = $('#suplier_id');
				            if (suplier.val() === '') {
				            	suplier.siblings().html('Vyberte dodávateľa!');
				                //alert('Vyberte dodávateľa!');
				                $('#suplier_id').focus();

				                return false;
				            }
				            else {
				            	$('#suplier_id').siblings().empty();
				            }
				            //else return;

				            if($('#price_ratio').val() == '') {
				            	$('#price_ratio').siblings().html('Zadajte cenový koeficient!');
				            	//alert("empty input file");
				            	$('#price_ratio').focus();
				            	return false;
				            }
				            else {
				            	$('#price_ratio').siblings().empty();
				            }

				            var file = $('#source_file').val();
				            if(file == '') {
				            	$('#source_file').siblings().html('Vyberte importný súbor! (XML)');
				            	//alert("empty input file");
				            	$('#source_file').focus();
				            	return false;
				            }
				            else {
				            	$('#source_file').siblings().empty();
				            }
				        });

						
						
         
				    });
				</script>
            </div>
            <?php
        };
        break;
}

function getSuplier($suplier_id) {
	$query = 'SELECT suplier FROM ' . TABLE_PREFIX . 'product_suplier WHERE suplier_id = ' . $suplier_id;
	if ($result = mysql_query($query)) {
		$row = mysql_fetch_object($result);
		return $row->suplier;
	}
	else
		echo 'Error (' . mysql_errno() . '): ' . mysql_error();
}
function getManufacturer($manufacturer_id) {
	if(is_numeric($manufacturer_id)) {
		$query = 'SELECT sk_name AS name FROM ' . TABLE_PREFIX . 'manufacturer WHERE manufacturer_id = ' . $manufacturer_id;
		if ($result = mysql_query($query)) {
			$row = mysql_fetch_object($result);
			return $row->name;
		}
		else
			echo 'Error (' . mysql_errno() . '): ' . mysql_error();
	}
	else
		return FALSE;
}
function updateStock($product_id, $stock) {

	$query = 'SELECT product_type_id FROM ' . TABLE_PREFIX . 'product_type WHERE univerzal="1" AND product_id="' . $product_id . '";';
	if($result = mysql_query($query)) {
		if (mysql_num_rows($result) == 1) {
			$row = mysql_fetch_object($result);
			$update_query = 'UPDATE ' . TABLE_PREFIX . 'product_type SET pocet = "' . $stock . '" WHERE product_type_id = "' . $row->product_type_id . '" ;';
			mysql_query($update_query);
		}
		elseif (mysql_num_rows($result) == 0) {
			$row = mysql_fetch_object($result);
			$insert_query = 'INSERT INTO ' . TABLE_PREFIX . 'product_type (product_id, name, univerzal, pocet) 
							VALUES ("' . $product_id . '", "Univerzálna", 1, ' . $stock . ');';
			mysql_query($insert_query);
		}
		mysql_free_result($result);
		unset($row);
	}
	else {
		echo 'Error (' . mysql_errno() . '): ' . mysql_error();
	}
}
function done($pi_id) {
	$update_query = 'UPDATE ' . TABLE_PREFIX . 'product_import SET done = "1" WHERE pi_id = "' . $pi_id . '";';
	mysql_query($update_query);
}
function countDone($group) {
	$param = explode('-', $group);
	if(strpos($group, 'exist') !== FALSE) {
		$queryInc = 'AND pi_id IN (' . implode(',', $_SESSION['exist']) . ') 
					AND p.suplier_id ' . (strpos($group, 'current') !== FALSE ? '= ' . $_SESSION['suplier_id'] : '!= ' . $_SESSION['suplier_id'] . ' AND p.price > pi.price_koef');
	}
	else {
		$queryInc = (count($_SESSION['noexist']) > 0 ? 'AND pi_id IN (' . implode(',', $_SESSION['noexist']) . ') ' : '') . ' AND ' . ($param[0] == 'other' ? 'news = "0" AND promo = "0" AND eol = "0"' : $param[0]) . ' = "1" AND stock ' . ($param[1] == '1' ? '>' : '=') . ' 0';
	}
	
	$query = 'SELECT pi_id FROM ' . TABLE_PREFIX . 'product_import AS pi 
				JOIN ' . TABLE_PREFIX . 'product AS p ON(p.code_ean = pi.code_ean OR p.code_suplier = pi.partno) 
				WHERE 1 
				AND imp_no = "' . $_SESSION['imp_no'] . '" ' .
				$queryInc . '
				AND done = "1"';
	if($result = mysql_query($query)) {
		return mysql_num_rows($result);
	}
	else {
		echo 'Error (' . mysql_errno() . '): ' . mysql_error();
	}
}
function insertManufacturer($manufacturer_name) {

	if(trim($manufacturer_name) != '') {
		$query = 'SELECT manufacturer_id FROM ' . TABLE_PREFIX . 'manufacturer WHERE 1 AND LOWER(sk_name) = "' . trim(strtolower($manufacturer_name)) . '";';
		if($result = mysql_query($query)) {
			if (mysql_num_rows($result) == 1) {
				$row = mysql_fetch_object($result);
				return $row->manufacturer_id;
			}
			else {
				$insert_query = 'INSERT INTO ' . TABLE_PREFIX . 'manufacturer (sk_name) 
								VALUES ("' . mysql_real_escape_string(trim($manufacturer_name)) . '");';
				if($result = mysql_query($insert_query)) {
					return mysql_insert_id();
				}
				else {
					echo 'Error (' . mysql_errno() . '): ' . mysql_error();
				}
			}
			mysql_free_result($result);
			unset($row);
		}
		else {
			echo 'Error (' . mysql_errno() . '): ' . mysql_error();
		}
	}
	else {
		return FALSE;
	}
}
// produkty, ktoré sú v databáze
function listPaired($group, $make = FALSE) {
	$param = explode('-', $group);
	$heading = (strpos($group, 'current') ? 'Produkty, ktoré už sú v eshope' : 'Produkty podľa EAN kódu, ktoré už sú v eshope, majú iného dodávateľa a lepšiu cenu');

	$query = 'SELECT p.product_id, p.sk_name, p.code_ean, p.price, p.suplier_id AS current_suplier_id, pi.* FROM ' . TABLE_PREFIX . 'product_import AS pi 
			JOIN ' . TABLE_PREFIX . 'product AS p ON(p.code_ean = pi.code_ean OR p.code_suplier = pi.partno) 
			WHERE 1 
			AND pi.imp_no = "' . $_SESSION['imp_no'] . '" 
			AND (TRIM(p.code_ean) != "" AND TRIM(p.code_suplier) != "")
			AND p.suplier_id ' . (strpos($group, 'current') !== FALSE ? '= ' . $_SESSION['suplier_id'] : '!= ' . $_SESSION['suplier_id'] . ' AND p.price > pi.price_koef');
	if($result = mysql_query($query)) {
		?>
		<h3><?= $heading; ?>: <strong class="red"><?= mysql_num_rows($result); ?></strong></h3>
		<?
		if($make) {
			$list = [];
			//$existAlter = [];
			while ($row = mysql_fetch_object($result)) {
				array_push($list, $row->pi_id);
				//array_push($existAlter, $row->pi_id);
			}
			if(strpos($group, 'current')) {
				$_SESSION['exist'] = $list;
			}
			else {
				$_SESSION['existAlter'] = $list;
				$_SESSION['exist'] = array_merge($_SESSION['exist'], $list);
			}
		}
		else {
			?>
			<form method="post" enctype="multipart/form-data" name="update_products" id="update_products" action="index.php?module=eshop_import&action=update&group=<?= $group; ?>">
				<table class="tableform importer">
					<thead>
						<tr>
							<th><input type="checkbox" class="checkAll" data-check="pi_id" /></th>
							<th>počet</th>
							<th>ID</th>
							<th>výrobca</th>
							<th>EAN</th>
							<th>kat.</th>
							<th>názov</th>
							<th>eshop cena</th>
							<th>VO cena x koef.</th>
							<th>roz.</th>
							<th>aktualizovať cenu<input type="checkbox" class="checkAll" data-check="price" /></th>
							<?= (strpos($group, 'alter') ? '<th>súč. dodávateľ</th>' : ''); ?>
						</tr>
					</thead>
					<tbody>
						<?
						$i = 0;
						while ($row = mysql_fetch_object($result)) {
							$i++;
							?>
							<tr <?= ($row->done == "1" ? ' style="opacity: 0.5;"' : ''); ?>>
								<td>
									<?
									if($row->done == "1") {
										echo '<span class="done">x</span>';
									}
									else {
										?>
										<input type="checkbox" name="pi_id[<?= $i; ?>]" data-check="pi_id" value="<?= $row->pi_id; ?>" />
										<input type="hidden" name="product_id[<?= $i; ?>]" value="<?= $row->product_id; ?>" />
										<?
									}
									?>
								</td>
								<td style="color: <?= ($row->stock > 0 ? 'green' : 'red'); ?>; text-align: right"><?= $row->stock; ?></td>
								<td style="text-align: right;"><?= $row->product_id; ?></td>
								<td><?= $row->manufacturer; ?></td>
								<td style="text-align: right;"><?= $row->code_ean; ?></td>
								<td><?= $row->category; ?></td>
								<td><?= $row->name; ?></td>
								<td style="text-align: right;"><?= number_format($row->price, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
								<td style="text-align: right;"><?= number_format($row->price_koef, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
								<td style="color: <?= (($row->price_koef - $row->price) > 0 ? 'red' : ''); ?>; text-align: right"><?= number_format(($row->price - $row->price_koef), 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
								<td>
									<?
									if($row->done != "1") {
										?>
										<input type="checkbox" name="price[<?= $i; ?>]" data-check="price" <?= (($row->price_koef - $row->price) > 0 ? ' checked="checked"' : ''); ?> disabled="disabled" />
										<?
									}
									?>
								</td>
								<?= (strpos($group, 'alter') ? '<td title="' . $row->current_suplier_id . '">' . getSuplier($row->current_suplier_id) . '</td>' : ''); ?>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
				<?= (strpos($group, 'alter') ? '<input name="alter" type="hidden" value="1" />' : ''); ?>
				<input name="update" id="update" type="submit" value="Aktualizovať" />
			</form>
			<div class="clear"></div>
			<hr />
			<?
		}
		mysql_free_result($result);
		//$_SESSION['exist'] = $exist;
		//$_SESSION['existAlter'] = $existAlter;
		//array_merge($exist, $existAlter);
		unset($row);
	}
	else {
		echo 'Error (' . mysql_errno() . '): ' . mysql_error();
	}	
}

// produkty, ktoré sú v databáze
function listSellOut($group, $make = FALSE) {

	$heading = (strpos($group, 'sellout') ? 'Produkty v eshope, ktoré sa nenachádzajú v importe' : 'Produkty v eshope bez EAN kódu a objednávacieho kódu');

	$query = 'SELECT product_id, sk_name AS name, code_ean, price, suplier_id, manufacturer_id FROM ' . TABLE_PREFIX . 'product 
			WHERE 1 
			AND suplier_id = ' . $_SESSION['suplier_id'] . 
			(strpos($group, 'sellout') ? ' AND (code_ean NOT IN (SELECT code_ean FROM ' . TABLE_PREFIX . 'product_import 
																WHERE 1 
																AND suplier_id = ' . $_SESSION['suplier_id'] . ' 
																AND imp_no = "' . $_SESSION['imp_no'] . '") 
												AND code_suplier NOT IN (SELECT partno FROM ' . TABLE_PREFIX . 'product_import 
																		WHERE 1 
																		AND suplier_id = ' . $_SESSION['suplier_id'] . ' 
																		AND imp_no = "' . $_SESSION['imp_no'] . '")
												) '
										: ' AND (TRIM(code_ean) = "" 
												AND TRIM(code_suplier) = "" 
												)'
										);

	if($result = mysql_query($query)) {
		?>
		<h3><?= $heading; ?>: <strong class="red"><?= mysql_num_rows($result); ?></strong></h3>
		<?
		if($make) {
			$list = [];
			//$existAlter = [];
			while ($row = mysql_fetch_object($result)) {
				array_push($list, $row->product_id);
				//array_push($existAlter, $row->pi_id);
			}
			if(strpos($group, 'sellout') !== FALSE) {
				$_SESSION['sellout'] = $list;
			}
			else {
				$_SESSION['unmarked'] = $list;
			}
		}
		else {
			?>
			<form method="post" enctype="multipart/form-data" name="update_products" id="update_products" action="index.php?module=eshop_import&action=update&group=<?= $group; ?>">
				<table class="tableform importer">
					<thead>
						<tr>
							<th><input type="checkbox" class="checkAll" data-check="product_id" /></th>
							<th>ID</th>
							<th>výrobca</th>
							<th>EAN</th>
							<th>názov</th>
							<th>eshop cena</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i = 0;
						while ($row = mysql_fetch_object($result)) {
							$i++;
							?>
							<tr>
								<td>
									<?
									if($row->done == "1") {
										echo '<span class="done">x</span>';
									}
									else {
										?>
										<input type="checkbox" name="product_id[<?= $i; ?>]" data-check="product_id" value="<?= $row->product_id; ?>" />
										<?
									}
									?>
								</td>
								<td style="text-align: right;"><?= $row->product_id; ?></td>
								<td><?= getManufacturer($row->manufacturer_id); ?></td>
								<td style="text-align: right;"><?= $row->code_ean; ?></td>
								<td><?= $row->name; ?></td>
								<td style="text-align: right;"><?= number_format($row->price, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
				<input name="dontshow" id="dontshow" type="submit" value="Nezobraziť" />
				<input name="remove" id="remove" type="submit" value="Zmazať" onclick="return confirm('Naozaj chcete zmazať označené produkty z eshopu?')" />
			</form>
			<div class="clear"></div>
			<hr />
			<?
		}
		mysql_free_result($result);
		//$_SESSION['exist'] = $exist;
		//$_SESSION['existAlter'] = $existAlter;
		//array_merge($exist, $existAlter);
		unset($row);
	}
	else {
		echo 'Error (' . mysql_errno() . '): ' . mysql_error();
	}	
}

function listUnpaired($group) {
	$param = explode('-', $group);
	$queryInc = 'AND ' . ($param[0] == 'other' ? 'news = "0" AND promo = "0" AND eol = "0"' : $param[0]) . ' = "1" AND stock ' . ($param[1] == '1' ? '>' : '=') . ' 0';
	$heading = 'Nezaradené - ' . $param[0] . ', ' . ($param[1] == '1' ? 'na sklade' : 'nedostupné');

	$query = 'SELECT * FROM ' . TABLE_PREFIX . 'product_import
				WHERE 1 
				AND imp_no = "' . $_SESSION['imp_no'] . '" ' . 
				(count($_SESSION['noexist']) > 0 ? 'AND pi_id IN (' . implode(',', $_SESSION['noexist']) . ') ' : '') .
				$queryInc . '
				ORDER BY manufacturer ASC';
	if($result = mysql_query($query)) {
		?>
		<h3><?= $heading; ?>: <strong class="red"><?= mysql_num_rows($result); ?></strong> (z toho importované: <strong class="green"><?= countDone($group); ?></strong>)</h3>
		<?
		if($_GET['action'] == 'update') {
			?>
			<form method="post" enctype="multipart/form-data" name="update_products" id="update_products" action="index.php?module=eshop_import&action=update&group=<?= $group; ?>">
				<table class="tableform importer">
					<thead>
						<tr>
							<th><input type="checkbox" class="checkAll" data-check="pi_id" /></th>
							<th>počet</th>
							<th>výrobca</th>
							<th>EAN</th>
							<th>kat.</th>
							<th>názov</th>
							<th>VO cena</th>
							<th>cena x koef.</th>
						</tr>
					</thead>
					<tbody>
						<?
						$i = 0;
						while ($row = mysql_fetch_object($result)) {
							$i++;
							?>
							<tr>
								<td>
									<?
									if($row->done == "1") {
										echo '<span class="done">x</span>';
									}
									else {
										?>
										<input type="checkbox" class="pi_id" name="pi_id[<?= $i; ?>]" data-check="pi_id" value="<?= $row->pi_id; ?>" />
										<?
									}
									?>
								</td>
								<td style="color: <?= ($row->stock > 0 ? 'green' : 'red'); ?>; text-align: right"><?= $row->stock; ?></td>
								<td><?= $row->manufacturer; ?></td>
								<td><?= $row->code_ean; ?></td>
								<td><?= $row->category; ?></td>
								<td><?= $row->name; ?></td>
								<td style="text-align: right;"><?= number_format($row->dac_price, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
								<td style="text-align: right;"><?= number_format($row->price_koef, 2, ',', '&nbsp;'); ?>&nbsp;&euro;</td>
							</tr>
							<?
						}
						?>
					</tbody>
				</table>
				<input name="insert" type="hidden" value="1" />
				<input name="update" type="submit" value="Aktualizovať" />
			</form>
			<div class="clear"></div><hr />
			<?
		}
		mysql_free_result($result);
	}
	else {
		echo 'Error (' . mysql_errno() . '): ' . mysql_error();
	}
}
?>