<?
// DEFINICIA PREMENNYCH
// definovanie potrebnych externych suborov
$css_file = '<link rel="stylesheet" type="text/css" href="' . fileWithLastChange('css/mod_' . $Row['module'] . '.css') . '" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
$js_file = ''; /* <script type="text/javascript" src="js/mod_xxc.js"></script> */
$footer_js_file = '';
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

// vykonanie akcii spojenych s odoslanim
// if(isset($_POST['send'])){
// nacitanie obsahu modulu
ob_start();

// VYKONANIE AKCII
/*?>
	<div class="container">
		<div class="row mosaic">
			<?
			$links = array(Menu::getHyperlinkById(228), 
							Menu::getHyperlinkById(227),  
							Menu::getHyperlinkById(229), 
							Menu::getHyperlinkById(230),
							Menu::getHyperlinkById(ESHOP_MAIN_CATEGORY) . '?filter=sale',
							Menu::getHyperlinkById(36));
			$titles = array(Menu::getHyperlinkTextById(228), 
							Menu::getHyperlinkTextById(227), 
							Menu::getHyperlinkTextById(229), 
							Menu::getHyperlinkTextById(230),
							$cTranslator->getTranslation('výpredaj', 1),
							str_replace('í', '<i class="icon icon-i-symbol"></i>', $cTranslator->getTranslation('Blog', 0)));
			$pictograms = array('women', 
							'men', 
							'children', 
							'apple',
							'sale',
							'blog');
			for($i = 0; $i < 6; $i++) {
				?>
				<div class="col-md-4 col-sm-6 col-xs-12">
					<div class="item" style="background-image: url(images/wrapper/<?= $pictograms[$i]; ?>.png);">
						<a href="<?= $links[$i]; ?>"><?= $titles[$i]; ?></a>
					</div>
				</div>
				<?
			}
			?>
		</div>
	</div><? */ ?>
	<h1 class="visually-hidden"><?= $h1Expanded; ?></h1>
	<div class="container">
		<h2 class="section-head">
			<a href="<?php echo Menu::getHyperlinkById(60); ?>?ff[]=action" class="d-block">
				<?= $cTranslator->getTranslation('akcie a zľavy', 1); ?>
			</a>
		</h2>
	</div>
		
	<div class="container narrower">
		<?
		$setFilter = 'action'; // new, action, sale, recommend
		include('include/inc_eshop_catalog.php');
        ?>
	</div>

	<div class="container">
		<h2 class="section-head">
			<a href="<?php echo Menu::getHyperlinkById(60); ?>?ff[]=recommend" class="d-block">
				<?= $cTranslator->getTranslation('odporúčaný', 1); ?>
			</a>			
		</h2>
	</div>
	<div class="container narrower">
		<?
		$setFilter = 'recommend'; // new, action, sale, recommend, novelty
		include('include/inc_eshop_catalog.php');
        ?>
	</div>

	<div class="top">
		<h2><?= $cTranslator->getTranslation('Top značky'); ?></h2>
		<? include('include/inc_manufacturers.php'); ?>
	</div>
	<?
	
	
	include_once('include/inc_article.php');
	include_once('include/inc_small_banners.php');
    ?>



<?
$moduleContent = ob_get_contents();
ob_clean();
?>