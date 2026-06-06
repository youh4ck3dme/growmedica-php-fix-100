<?php
	class UploadImage
	{
		var $oldImage;
		var $newImage;
		var $newImageExtension;
		var $newImageName;
	
		var $imageCreateFrom;

		var $oldWidth;
		var $oldHeight;
	
		var $fileitem_name;
		
		// ORIGINAL 
		var $originalDirectory 	= "../photos/original_1/";
		
		// PREVIEW
		var $previewDirectory 	= "../photos/preview_1/";
		var $previewWidth;
		var $previewHeight;
		
		// THUMBAIL
		var $thumbailDirectory 	= "../photos/thumbail_1/";
		var $thumbWidth;
		var $thumbHeight;
		
		// INFORMACIE O PHOTO
		var $photo_category_id;
		var $name;
		var $description;
		var $owner;
		var $sorter;
		
		function UploadFile($fileitem){
			
			if(!$this->check_extension($this->fileitem_name)){
                            print_r($this->fileitem_name);
				print "-ERR: Unknown file format!";
			}else{
				$this->nameOfFileHelp = explode('.',$this->fileitem_name);		
				$this->newImage = String::SEOFriendlyText($this->nameOfFileHelp[0]).'_'.md5(time()).".".$this->newImageExtension;
				
				if(!copy($fileitem, $this->originalDirectory.$this->newImage)){
					print "-ERR: Can't copy file to temporary directory!";
				}else{
					
					// PREVIEW 
					if(empty($this->previewWidth) or empty($this->previewHeight)) {
						// proporcne zmensovanie
						$this->ProporcneImage($this->originalDirectory.$this->newImage, $this->previewDirectory.$this->newImage,  $this->previewWidth, $this->previewHeight);
					}
					else {
						// orezavanie
						$this->OrezavanieImage($this->originalDirectory.$this->newImage, $this->previewDirectory.$this->newImage,  $this->previewWidth, $this->previewHeight);
					}
					
					// THUMBAIL 
					if(empty($this->thumbWidth) or empty($this->thumbHeight)) {
						// proporcne zmensovanie
						$this->ProporcneImage($this->originalDirectory.$this->newImage, $this->thumbailDirectory.$this->newImage,  $this->thumbWidth, $this->thumbHeight);
					}
					else {
						// orezavanie
						$this->OrezavanieImage($this->originalDirectory.$this->newImage, $this->thumbailDirectory.$this->newImage,  $this->thumbWidth, $this->thumbHeight);
					}
					
					//	vlozime zaznam do databazy
					$queryString = "insert into " . TABLE_PREFIX . "photo_images 
							(photo_category_id, menu_id, name, description, src, image_type, owner, date, sorter) 
						values 
							('".$this->photo_category_id."', '" . $this->menu_id . "','" . $this->name . "', '" . $this->description . "', '" . $this->newImage . "', '" . $this->newImageExtension. "', '" . $this->owner . "', NOW(), '" . $this->sorter . "');";
							
					if(!$Result = mysql_query($queryString))
						if(mysql_errno())
							print("MySql Error (" . mysql_errno() . "): " 
								. mysql_error());
				}
			}
		}

		function RemoveFileEx($fname){
			@unlink($this->originalDirectory.$fname);
			@unlink($this->previewDirectory.$fname);
			@unlink($this->thumbailDirectory.$fname);
			
			$queryString = "DELETE FROM " . TABLE_PREFIX . "photo_images WHERE src = '".$fname."';";
			if(!$result = mysql_query($queryString))
				if(mysql_errno())
					print("MySql Error (" . mysql_errno() . "): " 
						. mysql_error());
		}

		function proper_img_effort($filename) 
		{
			$extension = strtolower(end(explode(".", $filename)));

			switch($extension) 
			{
				case "jpg":
				case "jpeg":
					$output[0] = 'imagecreatefromjpeg';
					$output[1] = 'imagejpeg';
					$output[2] = 100;
				break;

				case "png":
					$output[0] = 'imagecreatefrompng';
					$output[1] = 'imagepng';
					$output[2] = 9;
				break;

				case "gif":
					$output[0] = 'imagecreatefromgif';
					$output[1] = 'imagegif';
					$output[2] = '';
				break;

				case "wbmp":
					$output[0] = 'imagecreatefromwbmp';
					$output[1] = 'imagewbmp';
					$output[2] = '';
				break;
			}
			return $output;
		}

		function check_extension($filename){
                    print_r($filename);
			$extension = strtolower(end(explode(".", $filename)));
			switch($extension){
				case "jpg":
				case "gif":
				case "png":
				case "jpeg":
				case "wbmp":
					$this->newImageExtension = $extension;
					return true;
				break;
			}
		}		
		
	// OREZAVANIE NA DANY ROZMER 
		function OrezavanieImage($obrazok=NULL, $dst_src=NULL, $width, $height)
		{
			$proper_img_effort = UploadImage::proper_img_effort($obrazok);

			$image_stats = getimagesize($obrazok); 
			$imagewidth = $image_stats[0]; 
			$imageheight = $image_stats[1]; 
			$img_type = $image_stats[2]; 

			$dst_img = imagecreatetruecolor($width,$height);
			$src_img = call_user_func($proper_img_effort[0], $obrazok);				

				if(($imagewidth / $imageheight) > ($width / $height)){
					imagecopyresampled($dst_img,$src_img,0,0,  (imagesx($src_img) - (imagesy($src_img) * $width / $height))/2, 0, $width, $height, (imagesy($src_img) * $width / $height),imagesy($src_img)); 
				}else{ 
					imagecopyresampled($dst_img,$src_img,0,0,0,(imagesy($src_img) - (imagesx($src_img) * $height / $width))/2,    $width, $height,  imagesx($src_img), (imagesx($src_img) * $height / $width)); 		
				}

			call_user_func_array($proper_img_effort[1], array($dst_img, $dst_src));
		}
	
	// PROPORCNE ZMENSOVANIE NA DANY ROZMER			
		private function ratioHeight($height) {
			$pomer = $this->oldHeight / $height;
			$this->newHeight = $height;
			$this->newWidth = round($this->oldWidth / $pomer);
		}
		
		private function ratioWidth($width) {
			$pomer = $this->oldWidth / $width;
			$this->newWidth = $width;
			$this->newHeight = round($this->oldHeight / $pomer);
		}
		
		private function ProporcneImage($obrazok=NULL, $dst_src=NULL, $width, $height)
		{
			$proper_img_effort = UploadImage::proper_img_effort($obrazok);

			$image_stats = getimagesize($obrazok); 
			$this->oldWidth = $image_stats[0]; 
			$this->oldHeight = $image_stats[1]; 
			$this->img_type = $image_stats[2]; 
			
			if(empty($width)) {
				// bola zadana pevna vyska
				if($this->oldHeight > $height) {
					$this->ratioHeight($height);
					
					$in = imagecreatetruecolor($this->newWidth, $this->newHeight);	
    				$im = call_user_func($proper_img_effort[0], $obrazok);				
    				
					imagecopyresampled($in, $im, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->oldWidth, $this->oldHeight);
    				if(!empty($proper_img_effort[2]))
    				{
    					call_user_func_array($proper_img_effort[1], array($in, $dst_src, $proper_img_effort[2]));
    				}
    				else
    				{
    					call_user_func_array($proper_img_effort[1], array($in, $dst_src));
    				}    					
				}
				else {
					 copy($obrazok, $dst_src);
				}
			}
			else {
				// bola zadana pevna srika
				if($this->oldWidth > $width) {
					$this->ratioWidth($width);
					
					$in = imagecreatetruecolor($this->newWidth, $this->newHeight);	
    				$im = call_user_func($proper_img_effort[0], $obrazok);				
    				
					imagecopyresampled($in, $im, 0, 0, 0, 0, $this->newWidth, $this->newHeight, $this->oldWidth, $this->oldHeight);
    				if(!empty($proper_img_effort[2]))
    				{
    					call_user_func_array($proper_img_effort[1], array($in, $dst_src, $proper_img_effort[2]));
    				}
    				else
    				{
    					call_user_func_array($proper_img_effort[1], array($in, $dst_src));
    				}
				}
				else {
					 copy($obrazok, $dst_src);
				}
			}
		}

		public function getProporcie($img, $des_width = 0, $des_height = 0)
		{
			$output = array('file error','file error');	

			if(file_exists($img))
			{
				$output = array('dimension error','dimension error');

				$dimensions = getimagesize($img);
				$width = $dimensions[0];
				$height = $dimensions[1];

				if($des_height == 0)
				{
					$ratio = $width / $des_width;
					$output[0] = $des_width;
					$output[1] = round($height / $ratio);
				}

				if($des_width == 0)
				{
					$ratio = $height / $des_height;
					$output[0] = round($width / $ratio);
					$output[1] = $des_height;
				}

				// pre pripad, ze chceme dat fotku do stvorca 
				// a potrebujeme aby ani jeden z rozmerov nebol mensi nez je strana stvorca
				if($des_width > 0 && $des_height > 0)
				{
					$ratio = $width / $des_width;
					$output[0] = $des_width;
					$output[1] = round($height / $ratio);

					if($output[0] < $des_width)
					{
						$ratio = $output[0] / $des_width;
						$output[0] = $des_width;
						$output[1] = round($output[1] / $ratio);
					}

					if($output[1] < $des_height)
					{
						$ratio = $output[1] / $des_height;
						$output[0] = round($output[0] / $ratio);
						$output[1] = $des_width;
					}
				}
			}
			return $output;
		}
	}
?>
