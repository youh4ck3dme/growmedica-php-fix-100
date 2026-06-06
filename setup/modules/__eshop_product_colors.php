<?php 

	error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
	ini_set('display_errors', '1');


	if(file_exists("../../shared/config.inc.php"))
	{
		include("../../shared/config.inc.php");
	}
		
	class photos
	{
	var $error;
	var $image  = array('name', 'extension', 'md5_name');	
	var $silent = false;

	var $image_md5_name = "";

	var $th_w = 170;
	var $th_h = '170';

		function create_category($category_name){
			$sql = "INSERT INTO `" . TABLE_PREFIX . "image_category` (name) VALUES ('".mysql_escape_string($category_name)."');";
			$res = @mysql_query($sql);
			if($res != 0){
				return true;
			}else
				if(!$this->silent){
					print mysql_error();
					print '\n';
				}
				@mysql_free_result($res);
		}
		
		function delete_image($ImageID){
			$sql = 'SELECT imf.file_name AS file_id FROM `' . TABLE_PREFIX . 'image_file` AS imf WHERE 1 AND imf.image_file_id = \''.$ImageID.'\';';
			$res = @mysql_query($sql);
			if($res != 0){
				if(mysql_num_rows($res) > 0){
					$line = mysql_fetch_object($res);
					//if(){
					$sql = 'DELETE FROM `' . TABLE_PREFIX . 'image_file` WHERE 1 AND `image_category_id` <> \'6\' AND `file_name` = \''.$line->file_id.'\';';
					$re2 = @mysql_query($sql);
					if(@mysql_affected_rows($re2) > 0){
						@unlink('../../photos/original/'.$line->file_id);
						@unlink('../../photos/thumbail/'.$line->file_id);					

					header("Location:" . $_SERVER['HTTP_REFERER']);			
					exit;

						return true;
					}
					
				}
			}else
				if(!$this->silent){
					print mysql_error();
				}
				@mysql_free_result($res);
		}
		function delete_image_v2($ImageID){
			$sql = 'SELECT imf.file_name AS file_id FROM `' . TABLE_PREFIX . 'image_file` AS imf WHERE 1 AND imf.image_file_id = \''.$ImageID.'\';';
			$res = @mysql_query($sql);
			if($res != 0){
				if(mysql_num_rows($res) > 0){
					$line = mysql_fetch_object($res);
					
					$sql = 'DELETE FROM `' . TABLE_PREFIX . 'image_file` WHERE 1 AND `image_category_id` = \'6\' AND `file_name` = \''.$line->file_id.'\';';
					@mysql_query($sql);
					@unlink('../../photos/original/'.$line->file_id);
					@unlink('../../photos/thumbail/'.$line->file_id);		
					
					header("Location:" . $_SERVER['HTTP_REFERER']);			
					exit;
					
				}
			}else
				if(!$this->silent){
					print mysql_error();
				}
				@mysql_free_result($res);
		}		
		
		/* FUNCTIONS FOR IMAGE RESIZING */
		function _ratio($old_width, $old_height, $new_width, $new_height){
		// funguje iba ak aspon jeden parameter je vecsi ako minimalna vyska, treba ostetrit ze co ak bude napriklad mensi
			if($old_width > $old_height){
				$ratio = round($old_width  / $new_width);
				$new_w = $new_width;
				$new_h =  ceil($old_height / $ratio);
			}else
				if($old_width < $old_height){
					$ratio = round($old_height	/ $new_height);
					$new_w =  ceil($old_width	/ $ratio);
					$new_h = $new_height;
				}else
					if($old_width == $old_height){
						$ratio = round($old_height / $new_height);
						$new_w = $new_height;
						$new_h = $new_height;					
					}
				$r['w'] = $new_w;	
				$r['h'] = $new_h;
				return $r;
		}
		
			function ratio($old_width, $old_height, $new_width, $new_height){
				if($old_width<$new_width and $old_height<$new_height) {
					$r['w'] = $old_width;
					$r['h'] = $old_height;
				}
				else {
					if($old_width > $old_height){ //obrazok je sirsi ako vyssi
						$ratio = $old_width / $new_width;  //
						$r['w'] = $new_width;
						$r['h'] = round($old_height / $ratio);
					}			
					if($old_width < $old_height
					|| $old_width == $old_height){
						$ratio = $old_height / $new_height;
						$r['h'] = $new_height;
						$r['w'] = round($old_width / $ratio);
					}
				}
				return $r;
			}	
				
		
			function image_jpg($dir="thumbail"){
				$is = imagecreatefromjpeg('../../photos/original/'.$this->image['md5_name']);
					$rt = $this->ratio(imagesx($is), imagesy($is), $this->th_w, $this->th_h);
				$id = imagecreatetruecolor($rt['w'], $rt['h']);
				imagecopyresampled($id, $is, 0, 0, 0, 0, $rt['w'], $rt['h'], imagesx($is), imagesy($is));
				imagejpeg($id, '../../photos/'.$dir.'/'.$this->image['md5_name'], 70);
			}
			function image_gif($dir="thumbail"){
				$is = imagecreatefromgif('../../photos/original/'.$this->image['md5_name']);
					$rt = $this->ratio(imagesx($is), imagesy($is), $this->th_w, $this->th_h);
				$id = imagecreatetruecolor($rt['w'], $rt['h']);
				imagecopyresampled($id, $is, 0, 0, 0, 0, $rt['w'], $rt['h'], imagesx($is), imagesy($is));
				imagegif($id, '../../photos/'.$dir.'/'.$this->image['md5_name'], 100);
			}
			function image_png($dir="thumbail"){
				$is = imagecreatefrompng('../../photos/original/'.$this->image['md5_name']);
					$rt = $this->ratio(imagesx($is), imagesy($is), $this->th_w, $this->th_h);
				$id = imagecreatetruecolor($rt['w'], $rt['h']);
				imagecopyresampled($id, $is, 0, 0, 0, 0, $rt['w'], $rt['h'], imagesx($is), imagesy($is));
				imagepng($id, '../../photos/'.$dir.'/'.$this->image['md5_name'], 100);
			}
		
			function move_image($image_name, $image_category){
				$this->image['extension'] =    strtolower(end(explode('.', $image_name['name'])));
				$this->image['name']      =  reset(explode('.', $image_name['name']));
				$this->image['md5_name']  = md5(time() . rand(10000,11000)).'.'.$this->image['extension'];
				$this->image['md5_name']  = String::SEOFriendlyText($this->image['name']).'_'.md5(time() . rand(10000,11000)).'.'.$this->image['extension'];
				if($this->image['extension'] == 'jpeg'){
					$this->image['extension'] = 'jpg';
				}
				
				if(!copy($image_name['tmp_name'], '../../photos/original/'.$this->image['md5_name'])){
					print 'Can\'t copy image into /images/ directory!\n';
				}else{
					switch(strtolower($this->image['extension'])){
						case 'jpg':{
							$this->th_w = 212;
							$this->th_h = 270;
							$dir = 'thumbail';
							if(empty($this->th_w) or empty($this->th_h)) {
								// proporcne zmensovanie
								$this->image_jpg();
							}
							else {
								// orezavanie
								$this->OrezavanieImage('../../photos/original/'.$this->image['md5_name'], '../../photos/'.$dir.'/'.$this->image['md5_name'],  $this->th_w, $this->th_h);
							}
							$this->th_w = 500;
							$this->th_h = 500;						
							$this->image_jpg("preview");
							$this->th_w = 200;
							$this->th_h = 200;
							$this->image_jpg("preview");							
							
						}	break;
						case 'gif':{
							$this->image_gif();		
							$this->th_w = 212;
							$this->th_h = 270;							
							$this->image_gif("preview");
							$this->th_w = 200;
							$this->th_h = 200;							
							$this->image_jpg("preview");
						}	break;
						case 'png':{
							$this->image_png();						
							$this->th_w = 212;
							$this->th_h = 270;							
							$this->image_png("preview");							
							$this->th_w = 200;
							$this->th_h = 200;							
							$this->image_png("preview");							
						}	break;
					}
					// ulozime informaciu o fotografii do databazy
					$sql = 'INSERT INTO `' . TABLE_PREFIX . 'image_file` (file_name, full_name, image_category_id) VALUES (\''.$this->image['md5_name'].'\', \''.$this->image['name'].'\', \''.$image_category.'\');';
				//	print($sql   . "<br />");
					$res = @mysql_query($sql);
					if($res != 0){
						return true;
					}else{
						if(mysql_errno()){
							print("MySql Error (" . mysql_errno() . "): " 
								. mysql_error());
						}
					}	
				}
			}
			
		// OREZAVANIE NA DANY ROZMER 
		private function OrezavanieImage($obrazok=NULL, $dst_src=NULL, $width, $height)
		{
			$image_stats = getimagesize($obrazok); 
			$imagewidth = $image_stats[0]; 
			$imageheight = $image_stats[1]; 
			$img_type = $image_stats[2]; 

			$dst_img = imagecreatetruecolor($width,$height);
			$src_img = imagecreatefromjpeg($obrazok);				

				if(($imagewidth / $imageheight) > ($width / $height)){
					imagecopyresampled($dst_img,$src_img,0,0,  (imagesx($src_img) - (imagesy($src_img) * $width / $height))/2, 0, $width, $height, (imagesy($src_img) * $width / $height),imagesy($src_img)); 
				}else{ 
					imagecopyresampled($dst_img,$src_img,0,0,0,(imagesy($src_img) - (imagesx($src_img) * $height / $width))/2,    $width, $height,  imagesx($src_img), (imagesx($src_img) * $height / $width)); 		
				}

			imagejpeg($dst_img, $dst_src);
		}
		
		function upload_image($image_name, $image_category){
			switch($image_name['type']){
				case 'image/pjpeg':
				case 'image/jpeg':				
				case 'image/gif':
				case 'image/x-png':
					if($this->move_image($image_name, $image_category)){
						return true;
					}
				break;
				default:{
				}
			}
		}
		function display_categories(){
			$sql = "SELECT image_category_id AS id, name AS name FROM " . TABLE_PREFIX . "image_category WHERE 1";
			$res = @mysql_query($sql);
			if($res != 0){
				while($line = mysql_fetch_object($res)){
					print '<tr><td valign="top"><a>'.$line->name.'</a></td><td align="right" width="41" valign="top">[<a href="javascript:confirmAction(\'Naozaj zmazať položku?\', \'\', \'photos_categories.php?ActionID=1&CategoryID='.$line->id.'\');" onClick="">zmazať</a>]</td></tr>';
				}
				return true;
			}else
				if(!$this->silent){
					print mysql_error();
					print '\n';
				}
		}
		function delete_category($category_id){
			$sql = 'SELECT file_name FROM `' . TABLE_PREFIX . 'image_file` WHERE 1 AND `image_category_id` <> \'6\' AND image_category_id = \''.$category_id.'\';';
			$res = @mysql_query($sql);
			if($res != 0){
				while($line = mysql_fetch_object($res)){
					$sql_2 = 'DELETE FROM `' . TABLE_PREFIX . 'image_file` WHERE 1 AND `image_category_id` <> \'6\' AND file_name = \''.$line->file_name.'\';';
					$res_2 = @mysql_query($sql_2);
					@mysql_free_result($res_2);
					@unlink('./images/original/'.$line->file_name);
					@unlink('./images/thumbail/'.$line->file_name);
				}
				$sql_3 = 'DELETE FROM `' . TABLE_PREFIX . 'image_category` WHERE 1 AND `image_category_id` <> \'6\' AND image_category_id = \''.$category_id.'\';';
				$res_3 = @mysql_query($sql_3);
				@mysql_free_result($res_3);
				return true;
			}else
				if(!$this->silent){
					print mysql_error();
					print '\n';
				}
		}
	}
	


	if(!$user->isAdmin())  {	exit;	}

	if(isset($_POST['color_id']) and $_GET['update'] != 1):
		if(!is_numeric($_POST['product_color_id'])) {
			$queryString = "insert into " . TABLE_PREFIX . "product_color (product_id, color_id) values ('" . $_POST['product_id'] . "','" . $_POST['color_id'] . "');";
			$ResultA = mysql_query($queryString);
			if($ResultA){
				$product_color_id = mysql_insert_id();
					
				// delete univerzal velkost pokial existuje		
				$queryString = "delete from " . TABLE_PREFIX . "product_color where 1 and product_id = '".$_POST['product_id']."' and univerzal = '1';";
				if(!$ResultC = mysql_query($queryString)){
					if(mysql_errno())
						print("MySql Error (" . mysql_errno() . "): "
						 . mysql_error());
				}
				
				// nahram foto ak je priradena
				if(isset($_FILES['image_preview']) and $_FILES['image_preview']['name'] <> ''){
					$object = new photos();

					if(!$object->upload_image($_FILES['image_preview'], 6)){
						print 'Neviem nahrať fotografiu!\n';
					}else{
						$queryString = "update " . TABLE_PREFIX . "product_color set src = '" . $object->image['md5_name'] . "' where 1 and product_color_id = '" . $product_color_id . "';";
						$ResultC = mysql_query($queryString);
					}
					unset($object);
				}	
				
				// priradim univerzalnu velkost
				$queryString = "insert into " . TABLE_PREFIX . "product_type (product_id, product_color_id,name,univerzal) values ('" . $_POST['product_id'] . "', '" . $product_color_id . "', 'Univerzál', '1');";
				$ResultA = mysql_query($queryString);
				if($ResultA){
					print '<h2>Farba bola úspešne pridaná.</h2>';
				}
			}
			else{ print(mysql_error()); }
		}
	endif;
	
	if($_GET['update'] == 1) {
		if($_POST['update_send'] == 1) {
			if(isset($_FILES['image_preview']) and $_FILES['image_preview']['name'] <> ''){
				$object = new photos();

				if(!$object->upload_image($_FILES['image_preview'], 6)){
					print 'Neviem nahrať fotografiu!\n';
				}else{
					$queryString = "update " . TABLE_PREFIX . "product_color set src = '" . $object->image['md5_name'] . "' where 1 and product_color_id = '" . $_POST['product_color_id'] . "';";
					$ResultC = mysql_query($queryString);
				}
				unset($object);
			}	
			header("Location:" . ROOTDIR . "/setup/modules/_eshop_product_colors.php?product_id=" . $_POST['product_id'] );			
			exit;
		}
		
		$queryString = "select * from " . TABLE_PREFIX . "product_color where 1 and product_id = '" . $_GET['product_id'] . "' and product_color_id = '" . $_GET['product_color_id'] . "';";
		$Result = mysql_query($queryString);
		if($Result){
			if(mysql_num_rows($Result) == 1) {
				$Row = mysql_fetch_assoc($Result);
			}
		}
	
	?>
		<div>
			<form method="POST" enctype="multipart/form-data" action="">
				<table width="100%">
					<tr>	
						<td>Farba:</td>
						<td><select name="color_id" disabled="disabled">
								<option value=""></option>
								<?
									$query_farba = "select color_id as id, name as name from " . TABLE_PREFIX . "color where 1;";
									echo $query_farba;
									$result_farba = mysql_query($query_farba);
									while($row_farba = mysql_fetch_object($result_farba))
									{
										echo '<option value="'.$row_farba->id.'" '.(($row_farba->id == $Row['color_id']) ? 'selected="selected"' : '').'>'.$row_farba->name.'</option>';
									}
								?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Fotografia:</td> 
						<td><input name="image_preview" type="file" id="image_preview" /></td>
					</tr>
					<tr>	
						<td>&nbsp;</td>
						<td align="left">			
							<input name="product_id" type="hidden" value="<?= $_GET['product_id']; ?>" />
							<input name="product_color_id" type="hidden" value="<?= $_GET['product_color_id']; ?>" />
							<input name="update_send" type="hidden" value="1" />
							<input type="submit" value="Upraviť" />
						</td>
					</tr>
				</table>
			</form>
		</div>
	<?
	}

	if($_GET['delete']==1):
		$queryString = "delete from " . TABLE_PREFIX . "product_color where 1 and product_color_id = '" . $_GET['product_color_id'] . "';";
		$ResultB = mysql_query($queryString);
		if($ResultB):
			// delete priradene velkosti
			$queryString = "delete from " . TABLE_PREFIX . "product_type where 1 and product_color_id = '" . $_GET['product_color_id'] . "';";
			$ResultB = mysql_query($queryString);
			if($ResultB){}
			
			//zistenie ci to nebola posledna "neuniverzalna" velkost ak ano,tak je potrebne pridat univerzalnu velkost
			$queryString = "select product_color_id from " . TABLE_PREFIX . "product_color where 1 and product_id = '" . $_GET['product_id'] . "';";
			$ResultB = mysql_query($queryString);
			if(mysql_num_rows($ResultB) == 0) {
				// priradim univerzalnu farbu
				$queryString = "insert into " . TABLE_PREFIX . "product_color (product_id, name,univerzal) values ('" . $_GET['product_id'] . "', 'Univerzál', '1');";
				$ResultA = mysql_query($queryString);
				if($ResultA){
					$product_color_id = mysql_insert_id();
				
					// priradim univerzalnu velkost
					$queryString = "insert into " . TABLE_PREFIX . "product_type (product_id, product_color_id,name,univerzal) values ('" . $_POST['product_id'] . "', '" . $product_color_id . "', 'Univerzál', '1');";
					$ResultA = mysql_query($queryString);
					if($ResultA){ }
				}
			}
		
			header("Location:" . $_SERVER['HTTP_REFERER']);
			exit;
		else:
			print(mysql_error());
		endif;
	endif;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= DEFAULTTITLENAME ?> - Veľkosti</title>
<style type="text/css">
	body, form {
		margin: 0px;
		padding: 0px;
	}
</style>
<script type="text/javascript" src="../../shared/js/colorpicker/jscolor.js"></script>
<script type="text/javascript" src="../../shared/js/functions.js"></script>
<script type="text/javascript" src="../../shared/js/script.js"></script>
<link href="../../shared/thickbox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../shared/js/thickbox/jquery.js"></script>
<script type="text/javascript" src="../../shared/js/thickbox/thickbox.js"></script>
<script type="text/javascript">
	function confirmAction(message, abort_action, ok_action){
	var msg = confirm(message);
		if(!msg){
			if(abort_action == ''){
				//return false;
				this.location;
			}else{
				this.location = abort_action;
			}
		}else{
			document.location.href = ok_action;
		}
	}

</script>
</head>

<body>
<strong>Veľkosti</strong><br />
<? if($_GET['update'] != 1) { ?>
<div>
	<form method="POST" enctype="multipart/form-data" action="">
		<table width="100%">
			<tr>	
				<td>Farba:</td>
				<td><select name="color_id">
						<option value=""></option>
								<?
									$query_farba = "select color_id as id, name as name from " . TABLE_PREFIX . "color where 1;";
									echo $query_farba;
									$result_farba = mysql_query($query_farba);
									while($row_farba = mysql_fetch_object($result_farba))
									{
										echo '<option value="'.$row_farba->id.'">'.$row_farba->name.'</option>';
									}
								?>
					</select>
				</td>
			</tr>
			<tr>
				<td>Fotografia:</td> 
				<td><input name="image_preview" type="file" id="image_preview" /></td>
			</tr>
			<tr>	
				<td>&nbsp;</td>
				<td align="left">			
					<input name="product_id" type="hidden" value="<?= $_GET['product_id']; ?>" />
					<input type="submit" value="Pridať" />
				</td>
			</tr>
		</table>
	</form>
</div>
<? } ?>
<div style="height:256px; overflow: auto;">
	<br />
	<table style="width: 100%;" border="0" cellpadding="0" cellspacing="2">
		<tr><td><strong>Názov</strong></td><td><strong>Veľkosti</strong></td><td><strong>Img</strong></td><td></td></tr>
	<?php
	$queryString = "select * from " . TABLE_PREFIX . "product_color as pc join ".TABLE_PREFIX."color as c USING(color_id) where 1 and product_id = '" . $_GET['product_id'] . "' and univerzal = '0';";
		$Result = mysql_query($queryString);
		if($Result):
			if(mysql_num_rows($Result) > 0) {
				while($Row = mysql_fetch_assoc($Result)):
					print '
						<tr>
						<td style="height: 18px; text-align:center; background-color: #' . $Row['code'] . '">' . $Row['name'] . '</td>
						<td>';
							$queryStringVelkosti = "SELECT * FROM " . TABLE_PREFIX . "product_type WHERE 1 AND product_color_id = '" . $Row['product_color_id'] . "' and univerzal = '0';";
							if(!$ResultVelkosti = mysql_query($queryStringVelkosti)){ print 'Chyba vyberu veľkostí z DB .';} 
							else {
								while($RowVelkosti = mysql_fetch_assoc($ResultVelkosti)){
									print $RowVelkosti['name'].', ';
								}
							}
					print '</td>
						<td>';
							if(!empty($Row['src']))
								print '<a href="../../photos/preview/'.$Row['src'].'" rel="thickbox" class="thickbox"><img src="../../photos/preview/'.$Row['src'].'" style="height:30px; width:auto;" /></a>';
					print '</td>
						<td align="right">
							<a href="_eshop_product_models.php?product_id='.$_GET['product_id'].'&amp;product_color_id='.$Row['product_color_id'].'">Pridať veľkosti</a>
							<a href="_eshop_product_colors.php?product_id='.$_GET['product_id'].'&amp;product_color_id='.$Row['product_color_id'].'&amp;update=1">Upraviť</a> &nbsp; 
							<a href="javascript:;" onclick="javascript:confirmAction(\'Naozaj zmazať položku?\', \'\', \'_eshop_product_colors.php?delete=1&amp;product_color_id=' . $Row['product_color_id'] . '&amp;product_id=' . $_GET['product_id'] . '\');">Zmazať</a></td>
						</tr>
					';
				endwhile;
			}
			else {
				$queryString = "select * from " . TABLE_PREFIX . "product_color where 1 and product_id = '" . $_GET['product_id'] . "' and univerzal = '1';";
				$Result = mysql_query($queryString);
				if($Result) {
					if(mysql_num_rows($Result) == 1) {
						$Row = mysql_fetch_assoc($Result);
						print '<tr><td></td><td>';
							$queryStringVelkosti = "SELECT * FROM " . TABLE_PREFIX . "product_type WHERE 1 AND product_color_id = '" . $Row['product_color_id'] . "' and univerzal = '0';";
								if(!$ResultVelkosti = mysql_query($queryStringVelkosti)){ print 'Chyba vyberu veľkostí z DB .';} 
								else {
									while($RowVelkosti = mysql_fetch_assoc($ResultVelkosti)){
										print $RowVelkosti['name'].', ';
									}
								}
						print '</td><td></td></tr>';
						print '<tr><td colspan="3" align="right"><a href="_eshop_product_models.php?product_id='.$_GET['product_id'].'&amp;product_color_id='.$Row['product_color_id'].'&amp;univerzalna_velkost=1">Pridať veľkosti k univerzálnej farbe</a></a>';
					}
					else { print 'ERROR: vyber univerzalnej farby'; }
				}
			}
		else:
			print(mysql_error());
		endif;
	?>
	</table>
</div>
</body>
</html>
