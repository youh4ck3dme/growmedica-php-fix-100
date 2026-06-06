function opakovana() {
	switch_page('right');
	timer = window.setTimeout("opakovana()", 3 * 1000); 
}

// funkcia meni polozky na hlavnej stranke
function switch_page(direction)
{  
  window.clearTimeout(timer);
  timer = null;

  // maximalny pocet stranok, ktore sa daju prepinat
  var max_page_id = $("#numberOfPage").val();
  
  var page_id = parseFloat($("#page_id").val());
  
  // zistim hodnotu kontrolneho divu
  var control_div = $("#control_div").val();


  // pokial je hodnota true moze nastat prepnutie stranok
  if(control_div == "true")
  {  
    // ak je hodnota v page_id prazdna
    if(isNaN(page_id))
    {
      page_id = 1;
    }
    
    // ak page_id nesplna dany rozsah
    if((page_id > max_page_id) || (page_id < 1))
    {
      page_id = 1;
    }
    
    if(direction=="right")
    {
      if(page_id == max_page_id)
      {
        page_id = 1;
        old_page = max_page_id;
        new_page = 1;
      }
      else
      {
        old_page = page_id;
        page_id = page_id + 1;
        new_page = page_id;
      }  
    }
    
    if(direction=="left")
    {
      if(page_id == 1)
      {
        page_id = max_page_id;
        old_page = 1;
        new_page = max_page_id;
      }
      else
      {
        old_page = page_id;
        page_id = page_id - 1;
        new_page = page_id;
      }  
    }
    
    // vymazem obsah skryteho divu obsahujuceho id stranky a vlozim don id aktualnej stranky
    $("#page_id").val(page_id);
    
    if(new_page != old_page)
    {
      // prepnem stranky
      switch_page_content(old_page, new_page);
      // zmenim class aktivnemu cislu
      activ_panel_number(new_page);
    }
  }
}

// funkcia prepina stranky na hlavnej stranke
// vstupom je nova stranka, ktora ma byt zobrazena.. staru stranku zistim zo skryteho divu na stranke
function panel_switch_page(new_page)
{
  window.clearTimeout(timer);
  timer = null;

  // zistim aktualnu stranku
  var old_page = parseFloat($("#page_id").val());
  
  // zistim hodnotu kontrloneho divu
  var control_div = $("#control_div").val();
  
  // pokial je hodnota true moze nastat prepnutie stranok
  if(control_div == "true")
  { 
    // ak je hodnota v old_page prazdna (jedna sa o uvodnu stranku)
    if(isNaN(old_page))
    {
      old_page = 1;
    }
      
    // pokial sa neklika na rovnaku stranku, tak sa stranky vymenia
    if((old_page != new_page))
    {
      // do skyteho divu vlozim id novo otvorenej stranky
      $("#page_id").val(new_page);
      
      // zavolam funkciu, ktora prepina stranky
      switch_page_content(old_page, new_page);
      
      // zmenim class aktivnemu cislu
      activ_panel_number(new_page);
    }    
  }
}

// funkcia zvyraznuje aktivne cislo v paleny s vyberom moznosti na uvodnej stranke
function activ_panel_number(activ_number)
{
  // odoberem classu vsetkym cislam
  $("#number_panel a").removeClass("activ_number");
  // pridam classu aktivnemu cislu    
  $("#number_"+activ_number).addClass("activ_number");
}


// funkcia meni texty na stranke
function switch_page_content(old_page, new_page)
{
  // do kontloneho divu vlozim hodnotu false, ktora signalizuje nedokoncenie fade efektu
  $("#control_div").val("false");
		
  // skryvam obrazok na stranke  
  //$("div.main_img_"+old_page).fadeOut('100', function() {
  //  $("div.main_img_"+new_page).fadeIn('100');
  //});

  // skryjem staru stranku a po jej skryti zvyraznim novy text
  $("#slide_"+old_page).fadeOut('1000', function() {
    $("#slide_"+new_page).fadeIn('1000');
    
    // do kontrloneho divu vlozim hodnotu true, ktora signalizuje dokoncenie fade efektu
    $("#control_div").val("true");
	
  });
}