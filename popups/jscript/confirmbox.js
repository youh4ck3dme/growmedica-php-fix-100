	    /*************************************************************************************************/
		/* pouzitie funkcie ConfirmBox                                                                   */
		/* Parametre: message = text ktory sa zobrazi v pop-up okne                                      */
		/*                                                                                               */  		
		/*************************************************************************************************/
		function ConfirmBox(message)
		{
			var msg = "Naozaj si želáte odstránit túto položku?";

				if(message != "")
				{
				  msg = message;
				}
					
			var Obj = confirm(msg);
			
	 		if(!Obj)
			{
			  return false;
			}
			else
			  return true;	
			  
		}
		
		function GetUrl(location)
		{
			if(location != "")
			  self.location = location;		
		}
		
		function ConfirmBoxAc(message, Url_Ok, Url_Cancel)
		{
			var Obj = ConfirmBox(message);
			  
			  if(Obj)
			    GetUrl(Url_Ok);
			  else
			    GetUrl(Url_Cancel);
				
		}
		
		/*************************************************************************************************/