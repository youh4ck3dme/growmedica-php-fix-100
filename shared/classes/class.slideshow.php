<?php

class slideshow {
	var $menuLevel = -1;
	
	function navmenu_init($dataFile)
	{

	}
	
	function getCategoriesMultiSelect()
	{
		global $_POST;
		$this->menuLevel++;
		$queryString = "select menu_slideshow_id as id, sk_name as name from " . TABLE_PREFIX . "menu_slideshow where 1 order by sk_name;";
		$Result = mysql_query($queryString);
		if($Result){
			while($Row = mysql_fetch_object($Result)){   
				print "<option value=\"" . $Row->id . "\"" . ((in_array($Row->id, $_POST['category_id']))? " selected=\"selected\"": "") . ">" . $this->_spacer() . $Row->name . "</option>";
			}
		}else{
			die(mysql_error());
		}
		@mysql_free_result($Result);
		$this->menuLevel--;
	}
	
	function _spacer()
	{
		for($i=0; $i<=$this->menuLevel; $i++){
			$Result .= "&nbsp;&nbsp;";
		}
		return $Result;
	}
}
$slideshow = new slideshow();
?>
