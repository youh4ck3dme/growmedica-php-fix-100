// JavaScript Document
function MM_swapImgRestore() { //v3.0
    var i, x, a = document.MM_sr;
    for (i = 0; a && i < a.length && (x = a[i]) && x.oSrc; i++)
        x.src = x.oSrc;
}

function MM_preloadImages() { //v3.0
    var d = document;
    if (d.images) {
        if (!d.MM_p)
            d.MM_p = new Array();
        var i, j = d.MM_p.length, a = MM_preloadImages.arguments;
        for (i = 0; i < a.length; i++)
            if (a[i].indexOf("#") != 0) {
                d.MM_p[j] = new Image;
                d.MM_p[j++].src = a[i];
            }
    }
}

function MM_findObj(n, d) { //v4.01
    var p, i, x;
    if (!d)
        d = document;
    if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
        d = parent.frames[n.substring(p + 1)].document;
        n = n.substring(0, p);
    }
    if (!(x = d[n]) && d.all)
        x = d.all[n];
    for (i = 0; !x && i < d.forms.length; i++)
        x = d.forms[i][n];
    for (i = 0; !x && d.layers && i < d.layers.length; i++)
        x = MM_findObj(n, d.layers[i].document);
    if (!x && d.getElementById)
        x = d.getElementById(n);
    return x;
}

function MM_swapImage() { //v3.0
    var i, j = 0, x, a = MM_swapImage.arguments;
    document.MM_sr = new Array;
    for (i = 0; i < (a.length - 2); i += 3)
        if ((x = MM_findObj(a[i])) != null) {
            document.MM_sr[j++] = x;
            if (!x.oSrc)
                x.oSrc = x.src;
            x.src = a[i + 2];
        }
}

function showElement(elId) {
    var e = document.getElementById(elId);
    if (e) {
        e.style.display = 'block';
    }

}
function hideElement(elId) {
    var e = document.getElementById(elId);
    if (e) {
        e.style.display = 'none';
    }

}

function addOption(obj, text, value, selected) {
    if (obj != null && obj.options != null) {
        obj.options[obj.options.length] = new Option(text, value, false, selected);
    }
}
function ConfirmBox(message)
{
    var msg = "Naozaj si zelate odstranit túto polozku?";

    if (message != "") {
        msg = message;
    }

    var Obj = confirm(msg);

    if (!Obj) {
        return false;
    }
    else
        return true;

}

function GetUrl(location)
{
    if (location != "")
        self.location = location;
}

function ConfirmBoxAc(message, Url_Ok, Url_Cancel)
{
    var Obj = ConfirmBox(message);

    if (Obj)
        GetUrl(Url_Ok);
    else
        GetUrl(Url_Cancel);
}
function skryto(skryid) {
    /*	if(navigator.userAgent.indexOf("MSIE")>-1)
     document.getElementById(id).style.display = 'table-row';
     else   */
    document.getElementById(skryid).style.display = 'none';
}
function toggle(id) {
    /*	if (document.getElementById(id).style.display == 'block') document.getElementById(id).style.display ='none'; else */
    if (navigator.userAgent.indexOf("MSIE") > -1)
        document.getElementById(id).style.display = 'table-row';
    else
        document.getElementById(id).style.display = 'block';
}
function redirection()
{
    f = window.open("");
    var el_select = document.getElementById("sites");
    f.location.href = el_select.options[el_select.selectedIndex].value;
}


function MM_jumpMenu(targ, selObj, restore) { //v3.0
    eval(targ + ".location='" + selObj.options[selObj.selectedIndex].value + "'");
    if (restore)
        selObj.selectedIndex = 0;
}

function MM_jumpMenuGo(selName, targ, restore) { //v3.0
    var selObj = MM_findObj(selName);
    if (selObj)
        MM_jumpMenu(targ, selObj, restore);
}

function MM_swapImgRestore() { //v3.0
    var i, x, a = document.MM_sr;
    for (i = 0; a && i < a.length && (x = a[i]) && x.oSrc; i++)
        x.src = x.oSrc;
}

function MM_preloadImages() { //v3.0
    var d = document;
    if (d.images) {
        if (!d.MM_p)
            d.MM_p = new Array();
        var i, j = d.MM_p.length, a = MM_preloadImages.arguments;
        for (i = 0; i < a.length; i++)
            if (a[i].indexOf("#") != 0) {
                d.MM_p[j] = new Image;
                d.MM_p[j++].src = a[i];
            }
    }
}

function MM_findObj(n, d) { //v4.01
    var p, i, x;
    if (!d)
        d = document;
    if ((p = n.indexOf("?")) > 0 && parent.frames.length) {
        d = parent.frames[n.substring(p + 1)].document;
        n = n.substring(0, p);
    }
    if (!(x = d[n]) && d.all)
        x = d.all[n];
    for (i = 0; !x && i < d.forms.length; i++)
        x = d.forms[i][n];
    for (i = 0; !x && d.layers && i < d.layers.length; i++)
        x = MM_findObj(n, d.layers[i].document);
    if (!x && d.getElementById)
        x = d.getElementById(n);
    return x;
}

function MM_swapImage() { //v3.0
    var i, j = 0, x, a = MM_swapImage.arguments;
    document.MM_sr = new Array;
    for (i = 0; i < (a.length - 2); i += 3)
        if ((x = MM_findObj(a[i])) != null) {
            document.MM_sr[j++] = x;
            if (!x.oSrc)
                x.oSrc = x.src;
            x.src = a[i + 2];
        }
}

function checkRegistration(thisForm) {
    var fudaje = "Kontaktné údaje: nezadali ste ";
    if (thisForm.password1.value != thisForm.password2.value) {
        alert('Heslá sa nezhodujú!');
        return false;
    }
    var fudaje = "Kontaktné údaje: nezadali ste ";
    if (thisForm.fname.value == "" || thisForm.lname.value == "") {
        alert('Nezadali ste vaše meno alebo priezvisko!');
        return false;
    } else {
        if (thisForm.address1.value == "") {
            alert(fudaje + 'adresu!');
            return false;
        } else {
            if (thisForm.city1.value == "") {
                alert(fudaje + 'mesto!');
                return false;
            } else {
                if (thisForm.psc1.value == "") {
                    alert(fudaje + 'psc!');
                    return false;
                } else {
                    //	kontrolujeme ze ak je zadana firma tak ci je zadane ico/ dic a ine potrebne data
                    var re = new RegExp("^[^.]+(\.[^.]+)*@([^.]+[.])+[a-z]{2,4}$");
                    if (!re.test(thisForm.email.value)) {
                        alert('Nezadali ste korektnú emailovú adresu!');
                        thisForm.email.focus();
                        return false;
                    } else {
                        return true;
                    }
                }
            }
        }
    }
}
function MM_validateForm() { //v4.0
    if (document.getElementById) {
        var i, p, q, nm, test, num, min, max, errors = '', args = MM_validateForm.arguments;
        for (i = 0; i < (args.length - 2); i += 3) {
            test = args[i + 2];
            val = document.getElementById(args[i]);
            if (val) {
                nm = val.name;
                if ((val = val.value) != "") {
                    if (test.indexOf('isEmail') != -1) {
                        p = val.indexOf('@');
                        if (p < 1 || p == (val.length - 1))
                            errors += '- ' + nm + ' must contain an e-mail address.\n';
                    } else if (test != 'R') {
                        num = parseFloat(val);
                        if (isNaN(val))
                            errors += '- ' + nm + ' musí obsahovat císlo.\n';
                        if (test.indexOf('inRange') != -1) {
                            p = test.indexOf(':');
                            min = test.substring(8, p);
                            max = test.substring(p + 1);
                            if (num < min || max < num)
                                errors += '- ' + nm + ' musí obsahovat císlo v rozsahu ' + min + ' a ' + max + '.\n';
                        }
                    }
                } else if (test.charAt(0) == 'R')
                    errors += '- ' + nm + ' is required.\n';
            }
        }
        if (errors)
            alert('Došlo k chybe pri výpocte:\n' + errors);
        document.MM_returnValue = (errors == '');
    }
}
