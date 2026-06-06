<?
	// DEFINICIA PREMENNYCH
	// definovanie potrebnych externych suborov
		$css_file = '<link rel="stylesheet" type="text/css" href="css/mod_messagebox.css" />'; // <link rel="stylesheet" type="text/css" href="css/mod_xxx.css" />
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
		
		$_SESSION['userPrefs']['prodMnozstvoNaStrane'] = 10;		

	// vykonanie akcii spojenych s odoslanim
	if(isset($_POST['send'])){}
	
	// nacitanie obsahu modulu
	ob_start();

	// VYKONANIE AKCII
	switch($navigateArrayUrlWithoutBase[0]) {
		case "insert":

      if($_POST['msg_send'] == 'y')
      {
        $query = 'INSERT INTO '.TABLE_PREFIX.'board (email, date, nick, text, ip, lang) VALUES ("'.$_POST['email'].'",
                                                                                          "'.date('Y-m-d H:i:s').'",
                                                                                          "'.$_POST['name'].'",
                                                                                          "'.$_POST['message'].'",
                                                                                          "'.$_SERVER['REMOTE_ADDR'].'",
                                                                                          "'.$_SESSION['lang'].'")';
        mysql_query($query);
        header('Location: '.ROOTDIR.'/'.Menu::getHyperLinkById($navigateId));
      }

    ?>
                  <p><strong>
                    <?= $cTranslator->getTranslation("Vitajte v našom odkazovači a nechajte nám odkaz.",0) ?>
                  </strong> </p>

                  <form method="post">
                    <table>
                      <tr>
                        <td><label for="name"><?= $cTranslator->getTranslation('Vaše meno',0); ?>: </label></td>
                        <td><input type="text" name="name" id="name" style="width:300px;" /></td>
                      </tr>
                      <tr>
                        <td><label for="email"><?= $cTranslator->getTranslation('Vaša e-mailová adresa',0); ?>: </label></td>
                        <td><input type="text" name="email" id="email" style="width:300px;" /></td>
                      </tr>
                      <tr>
                        <td><label for="message"><?= $cTranslator->getTranslation('Odkaz',0); ?>: </label></td>
                        <td><textarea name="message" id="message" style="height:150px; width:300px;" ></textarea></td>
                      </tr>
                      <tr>
                        <td></td>
                        <td><input type="hidden" value="y" name="msg_send"/>
                          <input type="submit" name="submit" id="submit" value="<?= $cTranslator->getTranslation('Odoslať',0); ?>" /></td>
                      </tr>
                    </table>
                  </form>    
    <?
			break; 
			
		case "edit":
			break; 
		
		case "detail":
			break; 
		
		case "delete":
			if($user->isAdmin()) {
				mysql_query("delete from ".TABLE_PREFIX."board where id='".$navigateEnd."' limit 1");
				header('Location: '.ROOTDIR.'/'.Menu::getHyperLinkById($navigateId));
			}
			break; 
		default:
				if($id>1){?>
                <a href="<? $i=$id-1; echo "$filename?strana=$i"; ?>" class="<? echo $class_href;?>" >&lt;&lt;</a> 
                <? }?>
                
                <?
                for($i=($interval-1)*$ukazat+1;$i<($interval-1)*$ukazat+1+$ukazat;$i++)
                {
                    if($i<=$pocet)
                    {
                        if($i!=$id){
                        ?>&nbsp;<a href="<? echo "$filename?strana=$i"; ?>" class="<? echo $class_href;?>"><? echo $i; ?></a> <?
                        }else{
                        ?>&nbsp;<font class="<? echo class_id ?>"><? echo $i?></font><?
                        }
                        
                    }
                
                    else break;
                }
                ?>
                
                
                <? if($id<$pocet){?>
                &nbsp;<a href="<? $i=$id+1; echo "$filename?strana=$i"; ?>" class="<? echo $class_href;?>">&gt;&gt;</a> 
                <? } ?>
            
                  <p><strong>
                    <?= $cTranslator->getTranslation("Vitajte v našom odkazovači a nechajte nám odkaz.",0) ?>
                  </strong> </p>
                  <p align="right"><a href="<?= Menu::getHyperLinkByID($navigateId); ?>/insert/"><strong><?= $cTranslator->getTranslation("Pridaj odkaz",0) ?></strong></a></p>
                  <? 
            $sql = "select count(id) as pocet from ".TABLE_PREFIX."board where lang = '".$_SESSION['lang']."' order by id desc";
            //echo $sql;	  
            $result=mysql_query($sql);
            $row=mysql_fetch_object($result);    
                    
            
            $sql = "select id, email, date_format(date, '%d.%m.%Y') as date, nick, text, ip from ".TABLE_PREFIX."board where lang= '".$_SESSION['lang']."' order by id desc";
            tabulator1($sql);
			
			$result=mysql_query($sql . $limit .";");
            //echo $sql;
            while($row=mysql_fetch_object($result)){
                            if(empty($row->email)) $zahlavie="<b>$row->nick</b>";
                            else $zahlavie="<a href='mailto:$row->email'><b>$row->nick</b></a>";
                            ?>
                  <table width="100%" border="0" cellpadding="3" cellspacing="0">
                    <tr>
                      <th><?
                  if($user->isAdmin()) echo "<span style='float:right'><a href='".Menu::getHyperLinkByID($navigateId)."/delete/$row->id'><img src='img/ikony/delete.gif' alt='DELETE' title='DELETE' border=0 ></a></span>";
                  ?>
                          <?php echo $zahlavie ?><br />
                          <font size="1">
                            <?= $cTranslator->getTranslation("PRIDANÉ",0) ?>
                            : <? echo $row->date ?></font>
                      </th>
                    </tr>
                    <tr>
                      <td class="text"><? echo $row->text ?> <br />
                      </td>
                    </tr>
                  </table>
                  <br />
                  <? } ?>
                  <p class="inputbutton" align="center">
                    <? // ukaz_zoznam($pocet,$zobrazit,$_GET['strana'],"pismobold","pismomale",Menu::getHyperLinkByID(8)) 
                    print tabulator_zobrazeny($sql,$_GET['param']);?>
                  </p>
                  <p align="right"><a href="<?= Menu::getHyperLinkByID($navigateId); ?>/insert/"><strong><?= $cTranslator->getTranslation("Pridaj odkaz",0) ?></strong></a></p>
                <?	
			
	}
	
$moduleContent = ob_get_contents();
ob_clean();
?>
