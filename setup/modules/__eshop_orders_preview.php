<?php
	require_once("../../shared/config.inc.php");
	
	
	if(!isset($_GET['objednavka'])){
		$_SESSION['objednavka'] = 1;
	}
	if($_GET['objednavka'] == 1){
		$_SESSION['objednavka'] = 1;
	}else{
		unset($_SESSION['objednavka']);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= PROJECT_NAME ?> - moje online objednávky - náhľad objednávky</title>
<style type="text/css" media="print">
	h1 {
	}
	#noprint {
		display: none;
	}
	a {
	}
	a:hover {
	}
</style>
<style type="text/css" media="screen">
	h1 {
		color: red;
		font-size: 21px;
		font-weight: bold;
	}
	#noprint {
	}
	a {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #333333;
	text-decoration: none;
	}
	a:hover {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: 11px;
	color: #666666;
	text-decoration: underline;
	}
</style>
</head>

<body>
<?php
	$sql = "SELECT * FROM " . TABLE_PREFIX . "order WHERE 1 AND order_id = '".$_GET['order_id']."'";
	$re1 = @mysql_query($sql);
	if($re1){
		$line = @mysql_fetch_object($re1);
	}else{
		print mysql_error();
	}
	@mysql_free_result($re1);
?>
<table border="0" cellpadding="0" cellspacing="0" style="width: 672px;">
	<tr id="noprint">
		<td style="padding-left: 13px;"><h1>Náhľad <?= ((isset($_SESSION['objednavka']))? "objednávky" : "faktúry") ?></h1></td>
	</tr>
	<tr id="noprint">
		<td align="right" style="padding-right: 33px;"><img src="../../images/admin/eshop_admin/button-tlacit.gif" alt="tlačiť objednávku" width="49" height="45" border="0" onclick="javascript:window.print();" style="cursor: pointer;"><img src="../../images/admin/eshop_admin/button-zatvorit.gif" alt="zatvoriť okno" width="49" height="45" border="0" onclick="javascript:window.close();" style="cursor: pointer;"></td>
	</tr>
	<tr id="noprint">
		<td>&nbsp;</td>
	</tr>	
	<tr>
		<td><?= $line->order_preview ?></td>
	</tr>
	<tr>
		<td id="noprint">&nbsp;</td>
	</tr>
	<tr id="noprint">
		<td align="right" style="padding-right: 33px;"><img src="../../images/admin/eshop_admin/button-tlacit.gif" alt="tlačiť objednávku" width="49" height="45" border="0" onclick="javascript:window.print();" style="cursor: pointer;"><img src="../../images/admin/eshop_admin/button-zatvorit.gif" alt="zatvoriť okno" width="49" height="45" border="0" onclick="javascript:window.close();" style="cursor: pointer;"></td>
	</tr>
</table>
</body>
</html>
