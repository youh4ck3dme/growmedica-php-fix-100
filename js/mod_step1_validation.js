function step1_validate()
{
	var fname = document.getElementById('fname').value;
	var lname = document.getElementById('lname').value;
	var address1 = document.getElementById('address1').value;
	var state1 = document.getElementById('state1').value;
	var city1 = document.getElementById('city1').value;
	var psc1 = document.getElementById('psc1').value;
	var phone = document.getElementById('phone').value;
	var mail = document.getElementById('mail').value;
	var readTerms = document.getElementById('readTerms').checked;

	if(fname == '') {
		alert("Zabudli ste zadať Vaše meno");
		return false;	
	}
	
	if(lname == '') {
		alert("Zabudli ste zadať Vaše priezvisko");
		return false;	
	}

	if(address1 == '') {
		alert("Zabudli ste zadať Vašu adresu");
		return false;	
	}

	if(state1 == '') {
		alert("Zabudli ste zadať štát");
		return false;	
	}

	if(city1 == '') {
		alert("Zabudli ste zadať mesto");
		return false;	
	}

	if(psc1 == '') {
		alert("Zabudli ste zadať PSČ");
		return false;	
	}

	if(phone == '') {
		alert("Zabudli ste zadať Vaše telefónne číslo");
		return false;	
	}

	if(mail == '') {
		alert("Zabudli ste zadať Vašu e-mailovú adresu");
		return false;	
	}

	mail_filter = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
	if(mail_filter.test(mail)) {
	}
	else {
		alert("Musíte zadať e-mailovú adresu");
		return false;			
	}

	if(readTerms == false) {
		alert("Zabudli ste potvrdiť súhlas s obchodnými podmienkami");
		return false;	
	}

	
}


