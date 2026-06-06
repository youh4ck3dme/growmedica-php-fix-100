/*
 * admin-functions.js
 * funkcie potrebné k úprave stránky cez frontend
 *  
 */
/**staré funkcie**/
function openPopUp1(id) {
    var w = window.open(rootdir + '/popups/mod_static_content_popup.php?menu_id=' + id, 'c' + id, 'width=600,height=600,resizable=1,scrollbars=1,status=1');
    if (w) {
        w.focus();
    }
}
function openPopUp2(id) {
    var w = window.open(rootdir + '/popups/mod_static_static-content_popup.php?action=update&content_id=' + id, 'c' + id, 'width=900,height=700,resizable=1,scrollbars=1,status=1');
    if (w) {
        w.focus();
    }
}
function openPopUp3(id) {
    var w = window.open(rootdir + '/popups/mod_translation_popup.php?action=update&translation_id=' + id, 'c' + id, 'width=600,height=450,resizable=1,scrollbars=1,status=1');
    if (w) {
        w.focus();
    }
}
/**nové funkcie**/
/*
 * openPopupWindow
 * 
 * PopUp okná potrebné k editácii frontendu stránky administrátorom
 * 
 * 
 */
function openPopupWindow(type, id, width, height) {
    if (type == 'content') {
        var url = rootdir + '/popups/page_content_popup.php?menu_id=' + id;
    } else if (type == 'static-content') {
        var url = rootdir + '/popups/static_content_popup.php?action=update&content_id=' + id;
    } else if (type == 'translation') {
        var url = rootdir + '/popups/translation_popup.php?action=update&translation_id=' + id;
    } else if (type == 'image-processor') {
        var url = id;
    } else {
        return false;
    }
    if (undefined == width) {
        var width = 600;
    }
    if (undefined == height) {
        var height = 700;
    }
    var left = (screen.width / 2) - (width / 2);
    var top = (screen.height / 2) - (height / 2);
    return window.open(url, 'sixadmin_popup_' + type, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=1, resizable=no, copyhistory=no, width=' + width + ', height=' + height + ', top=' + top + ', left=' + left);
}
/*
 * confirmWindow
 * 
 * Confirm box s presmerovaním
 * 
 * 
 */
function confirmWindow(message, ok_url, cancel_url) {


    var msg = message;

    if (message != "") {
        msg = message;
    }

    var object = confirm(msg);

    if (object) {
        goToUrl(ok_url);
    } else {
        if (cancel_url != '')
            goToUrl(cancel_url);
    }
}
/*
 * goToUrl
 * 
 * jednoduché presmerovanie
 * 
 * 
 */
function goToUrl(location) {
    if (location != "")
        self.location = location;
}
/*
 * insertImageToCkeditor
 * 
 * PopUp okná potrebné k editácii frontendu stránky administrátorom
 * 
 * 
 */
function insertImageToCkeditor(src, link, alt, style_class) {
    var CKEDITOR = window.parent.CKEDITOR;
    var ck_instance_name = false;
    for (var ck_instance in CKEDITOR.instances) {
        if (CKEDITOR.instances[ck_instance].focusManager.hasFocus) {
            ck_instance_name = ck_instance;

            if (link != '') {
                image = '<a class="' + style_class + '" href="' + link + '">';
                image += '<img src="' + src + '" alt="' + alt + '" />';
                image += '</a>';
            } else {
                image = '<img class="' + style_class + '" src="' + src + '" alt="' + alt + '" />';
            }

            CKEDITOR.instances[ck_instance].insertHtml(image);

            return false;
            ck_instance_name;
        }
    }
}
/*
 * openPopupWindow
 * 
 * PopUp okná potrebné k editácii frontendu stránky administrátorom
 * 
 * 
 */
function getLangTab(code) {
    $('.nav.nav-tabs > li').each(function (i) {
        $('.nav.nav-tabs > li').not($(this)).removeAttr('class');
    });
    $('#editor-tabs').each(function (i) {
        $('#editor-tabs > div').not($(this)).css('display', 'none');
    });
    $('#tab_lang_' + code).css('display', 'block');
    $('#link_lang_' + code).closest('li').addClass('active');
}
/*
 * 
 * 
 * 
 * 
 * 
 */
function hideFrame(frameName) {
    var e = document.getElementById(frameName);
    if (e) {
        e.style.display = 'none';
        var f = document.getElementById(frameName + '_on');
        if (f) {
            f.style.display = 'block';
        }
        var g = document.getElementById(frameName + '_off');
        if (g) {
            g.style.display = 'none';
        }
    }
}
function showFrame(frameName) {
    var e = document.getElementById(frameName);
    if (e) {
        e.style.display = 'block';
        var f = document.getElementById(frameName + '_off');
        if (f) {
            f.style.display = 'block';
        }
        var g = document.getElementById(frameName + '_on');
        if (g) {
            g.style.display = 'none';
        }
    }
}