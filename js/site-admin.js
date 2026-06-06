$(document).ready(function () {
    $.getScript("index.js");
    //
    // iframe
    if ($('.fancybox-iframe').length != 0) {
        $('.fancybox-iframe').fancybox({
            'width': 600,
            'height': 550,
            'centerOnScroll': true,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none',
            'type': 'iframe',
        });
    }
    // iframe END
    //
    // image
    if ($('.fancybox-image').length != 0) {
        $('.fancybox-image').fancybox({
            'centerOnScroll': true,
            'autoScale': false,
            'transitionIn': 'none',
            'transitionOut': 'none'
        });
    }
    // image END
    //
    // datepicker
    if ($('.datepicker').length != 0) {
        $('.datepicker').datepicker({
            format: "yyyy-mm-dd",
            weekStart: 1,
            language: "sk",
            autoclose: true
        });
    }
    $('.datepicker-button').click(function () {
        $('.datepicker').focus();
    });
    // datepicker END
    //
    //
    // ochrana pred opustením stránky bez uloženia
    /*
     if ($('form').length != 0) {
     var unsaved = false;
     $(window).bind('beforeunload', function () {
     if (unsaved) {
     return "Na stránke boli vykonané zmeny. Ak opustíte túto stránku vykonané zmeny nebudú uložené.";
     }
     });
     
     $(document).on('change', ':input, select, textarea', function () {
     unsaved = true;
     });
     $('button').click(function () {
     unsaved = false;
     });
     }
     */
    // ochrana pred opustením stránky bez uloženia END
    //
    // zoraďovanie položiek JQ UI
    if ($('.sortable').length != 0) {
        $(".sortable").sortable({
            placeholder: "ui-state-highlight",
            cursor: 'grab',
            update: function (event, ui) {
                var order = $(".sortable").sortable("toArray");
                $('#sorter').val(order.join(","));
            }
        });
        $(".sortable").disableSelection();
    }
    // zoraďovanie položiek JQ UI END
});
$(window).load(function () {
});
// function
function sortItemA(itemId) { // používané
    var w = window.open('modules/_static_content_sort.php?cp_id=' + itemId, 'sortItemA', 'width=400,height=240');
    if (w) {
        w.focus();
    }
}
function sortItemB(itemId) { // používané
    var w = window.open('modules/_article_sort.php?article_category_id=' + itemId, 'sortItemB', 'width=400,height=240');
    if (w) {
        w.focus();
    }
}
function sortItemC(itemId) { // používané
    var w = window.open('modules/_menu_sort.php?child_of=' + itemId, 'sortItemC', 'width=400,height=240');
    if (w) {
        w.focus();
    }
}
function slideshow(itemId) { // používané
    var w = window.open('modules/_slideshow_prepojenie.php?menu_id=' + itemId, 'slideshow', 'width=400,height=500');
    if (w) {
        w.focus();
    }
}
function confirmAction(message, abort_action, ok_action) { // používané
    var msg = confirm(message);
    if (!msg) {
        if (abort_action == '') {
            return false;
            //this.location;
        } else {
            this.location = abort_action;
        }
    } else {
        document.location.href = ok_action;
    }
}
// _eshop_product.php
function product(itemId) { // používané
    var w = window.open('modules/_eshop_product_sort.php?child_id=' + itemId, 'product', 'width=400,height=500');
    if (w) {
        w.focus();
    }
}
// _eshop_product_content.php
function openWinModels(product_id) { // asi nepoužívané
    newWin = window.open('/modules/_eshop_product_models.php?product_id=' + product_id, 'productModels', 'width=600,height=500');
    newWin.focus();
}
function openWinColors(product_id) { // nepoužíva sa??? bola zakomentovaná
    newWin = window.open('modules/_eshop_product_colors.php?product_id=' + product_id, 'productModels', 'width=600,height=500');
    newWin.focus();
}
function changeFormAction(url) { // používane
    if (typeof url !== 'undefined') {
        $('#update-product').attr('action', url);
    }
}

function OpenImage(image_path, style) {
    if (document.forms['formular'].popup_img[0].checked) {
        var popup = '1';
    }
    if (document.forms['formular'].popup_img[1].checked) {
        var popup = '3';
    }
    if (document.forms['formular'].popup_img[2].checked) {
        var popup = '2';
    }
    var o = window.open('<?= ROOTDIR ?>/popups/popup.php?image_path=' + image_path + '&popup=' + popup, '_blank', 'width=800, height=600');
    o.focus();
}