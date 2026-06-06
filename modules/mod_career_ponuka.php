<?
// DEFINICIA PREMENNYCH
	// definovanie potrebnych externych suborov
	$css_file = ''; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
	$js_file  = ''; /* <script type="text/javascript" src="js/mod_xxc.js"></script> */
	$MODULE_HEADER = $css_file.$js_file;
	
	// definovanie potrebnych inline js action // uvadzat bez oznacenia <script>
	$inline_js = ''; /* $(function() { $( "#datepicker" ).datepicker(); }); */	
	$MODULE_INLINE_JS = $inline_js;
	
	// titulka (ak je prazdna, pouzije sa titulka zo struktury SQL) 	
	$MODULE_TITLE = "";
	
	// seo prvky daneho modulu
	$MODULE_DESCRIPTION = "";
	$MODULE_KEYWORDS = "";
	
	// 
	$_SESSION['userPrefs']['prodMnozstvoNaStrane'] = 8;		

	// vykonanie akcii spojenych s odoslanim

	// nacitanie obsahu modulu
	ob_start();

	// VYKONANIE AKCII
	switch($navigateArrayUrlWithoutBase[0]) {
		case "insert":	break; 
			
		case "edit":	break; 
		
		case "detail":
			$queryStringArticle = "select a.*, date_format(a._date, '%d.%m.%Y') as _date, ac.".$lang."_name_seo as aname_seo from " . TABLE_PREFIX . "article as a join " . TABLE_PREFIX . "article_category as ac using (article_category_id) where 1 and publish = 1 and a.article_id = '" . $navigateEnd . "' limit 1;";
			if(!$ResultArticle = mysql_query($queryStringArticle)){
				if(mysql_errno())
					print("MySql Error (" . mysql_errno() . "): " 
						. mysql_error() . "<br />");
			}
			else{ 
				$RowArticle = mysql_fetch_assoc($ResultArticle);
				
				print '
					<div id="article-detail">
						<h1>' . $RowArticle[$_SESSION['lang'].'_name'] . '</h1>
						<div class="preview">' . strip_tags($RowArticle[$_SESSION['lang'].'_preview'],"<p><a><img>") . '</div>
						<div class="text">'. $RowArticle[$_SESSION['lang'].'_article'] . '</div>
						<strong>'. $cTranslator->getTranslation("Dátum:") . '</strong> ' . $RowArticle['_date']  .' | 
						<a href="'.Menu::getHyperLinkByID(13).'">'. $cTranslator->getTranslation("Zoznam ponúk") . '</a> | 
						<a href="'.Menu::getHyperLinkByID(15).'?pozicia='.$navigateEnd.'">'. $cTranslator->getTranslation("Reagovať na ponuku") . '</a> | 
						<a href="javascript:;" onclick="javascript:window.print();">'. $cTranslator->getTranslation("Vytlačiť") . '</a>
					</div>';
			}
			break; 
		
		case "delete":
			break; 
		default:
			$queryStringArticle = "select a.*, 
									date_format(a._date, '%d.%m.%Y') as _date, 
									a.".$_SESSION['lang']."_name_seo as aname_seo, 
									ac.".$lang."_name as acname 
									from " . TABLE_PREFIX . "article as a join " . TABLE_PREFIX . "article_category as ac using (article_category_id) where 1 and publish = 1 and a.".$_SESSION['lang']."_name_seo!='' and article_category_id='1' order by sorter asc, a._date desc";
									
			//print $queryStringArticle;
			
			tabulator1($queryStringArticle);
			
			$ResultArticle = mysql_query($queryStringArticle . $limit .";");
			
			if(!$ResultArticle){
				print mysql_error();
			}else{
				
				if(mysql_num_rows($ResultArticle)>0){
					print '<div id="article-zoznam">';
						//print "<h1>".$Row[$_SESSION['lang']."_name"]."</h1><br />";
					
						while($RowArticle = mysql_fetch_assoc($ResultArticle)){
							print '<div class="article">
									<h2><a href="./' . $_SESSION['lang'] . '/' . $menuUrl . '/detail/' . $RowArticle['aname_seo'] . '/'.$RowArticle['article_id'].'" >' . $RowArticle[$_SESSION['lang'].'_name'] . '</a></h2>
									<div class="text" >'.strip_tags($RowArticle[$_SESSION['lang'].'_preview'],"<img>") .'</div>';
								
								if($user->isAdmin()) { 
									print '
									<div class="footer">
										<img src="images/icons/edit.gif" alt="' . SEO_TITLE . '" /> <a href="setup/index.php?module=article&amp;action=update&amp;article_id=' . $RowArticle["article_id"] . '" target="_blank">Upravit článok</a>&nbsp;&nbsp;<img src="images/icons/delete.gif" alt="' . SEO_TITLE . '" /> <a href="javascript:;" onclick="javascript:ConfirmBoxAc(\'Naozaj si želáte odstránit tento clánok?\', \'setup/index.php?module=article&amp;action=delete&amp;main_page=1&amp;article_id=' . $RowArticle['article_id'] . '\', \'\');">Zmazat</a>
									</div>'; 
								}
					
							print '<p>&nbsp;</p></div>';	
						}
						
						if(mysql_num_rows($ResultArticle)>$_SESSION['userPrefs']['prodMnozstvoNaStrane']){
							print tabulator_zobrazeny($queryStringArticle, $_GET['param']);
						}
						
					print '</div>';
				}else{
					echo '<h2>'.$cTranslator->getTranslation("Momentálne nemáme žiadne voľné pozície.").'</h2>';
				}
			}
	}
	
	echo '<br /><hr /><br />';
	echo vsprintf(getContentByLabel('kariera-ponuka-footer'), '<a href="'.Menu::getHyperLinkById(15).'" class="button">'.$cTranslator->getTranslation("vyplniť náš kariérny formulár").'</a>').'<br>';
	
	$moduleContent = ob_get_contents();
	ob_clean();
?>