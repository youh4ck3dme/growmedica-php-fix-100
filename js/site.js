var collapseWidth = 767;

$(document).ready(function () {
    $.getScript("index.js");

    $('#header #navbarNav').on({
        mouseenter: function () {
            $('#overlayBox').fadeIn(300);
        },
        mouseleave: function () {
            $('#overlayBox').fadeOut(300);
        }
    });

    /*
    // autopadding for menu
    var container = $('#menu');
    var container_width = parseInt(container.width());
    var li_count = container.find(' > ul > li').length;
    var li_calc_width = container_width / li_count;

    var actual_link_width = 0;
    var difference = 0;

    //console.log('MENU AUTOPADDING: container_width: ' + container_width);
    //console.log('MENU AUTOPADDING: li_count: ' + li_count);
    //console.log('MENU AUTOPADDING: li_calc_width: ' + li_calc_width);

    container.find(' > ul > li > a').each(function (index) {
        actual_link_width = $(this).width();
        difference = li_calc_width - actual_link_width;

        padding = Math.floor(difference / 2);
        padding = padding - 2;

        //console.log('MENU AUTOPADDING: [' + index + '] actual_link_width: ' + actual_link_width);
        //console.log('MENU AUTOPADDING: [' + index + '] difference: ' + difference);

        if (difference > 0)
        {
            $(this).css('padding', '0px ' + padding + 'px');
            //console.log('MENU AUTOPADDING: [' + index + '] padding: ' + padding);
        }
        else
        {
            $(this).css('padding', '0px 0px');
            //console.log('MENU AUTOPADDING: [' + index + '] padding: 0');
        }
    });
    // autopadding for menu end
    */
    // 
    // messages popup
    if ($('#messages-container').length != '0') {
        bootbox.dialog({
            message: $('#messages-container').find('div').text(),
            title: notice_title,
            buttons: {
                success: {
                    label: close_title
                }
            }
        });
    }
    // messages popup end
    //
    // product added to cart
    if ($('#product-added').length != '0') {
        var c = $('#product-added');
        var msg = c.text();

        //https://stackoverflow.com/questions/68222671/convert-confirm-to-bootstrap-modal-dialog
        (async() => {
            const result = await b_confirm(c.text(), c.data('cancel'), c.data('confirm'));
            if(result)
                window.location.replace(c.data('redirect'));
        })();

        /*
        var c = $('#product-added');
        bootbox.confirm({
            title: '',
            message: c.text(),
            buttons: {
                cancel: {
                    label: c.data('cancel')
                },
                confirm: {
                    label: c.data('confirm')
                }
            },
            callback: function (result) {
                if(result)
                    window.location.replace( c.data('redirect'));
            }
        });
        */
    }
    // fancybox
    if ($('a.gallery-item').length != 0) {
        $("a.gallery-item").fancybox({
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'speedIn': 600,
            'speedOut': 200,
            'overlayShow': true,
            'overlayColor': '#222',
            'centerOnScroll': true
        });
    }
        $(".fancybox").fancybox({
            'transitionIn': 'elastic',
            'transitionOut': 'elastic',
            'speedIn': 600,
            'speedOut': 200,
            'overlayShow': true,
            'overlayColor': '#222',
            'centerOnScroll': true
        });
    // fancybox end 
    // 
    // availability
    /*
    if ($('select#color-select').length != 0) {
        $('select#color-select').change(function () {
            $.ajax({
                type: 'POST',
                url: 'ajax/color-sizes.php',
                data: {product_id: $('#product_id').val(), color_id: $(this).val()}
            }).done(function (result) {
                result = $.parseJSON(result);
                console.log(result);
                if (result[0] == 'empty-values') {
                    alert(result[2]);
                    return false;
                } else if (result[0] == 'options') {
                    $('label#size-container').removeClass('hidden');
                    if ($('select#size-select').length == 0) {
                        $('<select name="size" id="size-select">' + result[3] + '</select>').appendTo($('label#size-container'));
                    } else {
                        $('select#size-select').html(result[3]);
                    }
                    return false;
                } else if (result[0] == 'universal') {
                    $('select#size-select').remove();
                    $(result[3]).appendTo($('label#size-container'));
                    $('label#size-container').addClass('hidden');
                    return false;
                }
            });
        });
    }
    if ($('select#size-select').length != 0) {
        $('select#size-select').change(function () {
            $.ajax({
                type: 'POST',
                url: 'ajax/size-amount.php',
                data: {product_id: $('#product_id').val(), size_id: $(this).val()}
            }).done(function (result) {
                result = $.parseJSON(result);
                if (result[0] == 'empty-values') {
                    alert(result[2]);
                    return false;
                } else {
                    return false;
                }
            });
        });
    }*/
    if ($('#inc-dec-control').length != 0) {
        $(".amount-button").on("click", function () {
            var $button = $(this);
            var oldValue = $button.parent().find("input").val();
            var maxValue = $button.parent().find("input").attr('rel');
            if ($button.text() == "+") {
                if (maxValue <= parseFloat(oldValue)) {
                    var newVal = maxValue;
                } else {
                    var newVal = parseFloat(oldValue) + 1;
                }
            } else {
                // Don't allow decrementing below zero
                if (oldValue > 1) {
                    var newVal = parseFloat(oldValue) - 1;
                } else {
                    newVal = 1;
                }
            }
            $button.parent().find("input").val(newVal);

        });
    }
    // availability end
    //
    $('.add-to-cart').on('click', function(){
        console.log('click');
            $(this).parent().parent().parent().addClass('processing');

            var product_id = $(this).attr('data-product');
            var price_item = $(this).attr('data-price');
            var amount = 1;
            var itemImg = $(this).parent().find('img').eq(0);
            var popup = { redirect: $(this).attr('data-redirect'), 
                            cancel: $(this).attr('data-cancel'), 
                            confirm: $(this).attr('data-confirm'), 
                            message: $(this).attr('data-message')}
            console.log("itemImg: " + itemImg);

            insertItemToCart(product_id, amount, price_item, itemImg, popup);                   

            //$('.processing').delay(800).css('background-color', 'green');
            $('.product.item').removeClass('processing');
        
    });
    /*
    // product added to cart
    if ($('#product-added').length != '0') {
        $('#product-added').animate({
            opacity: '0'
        }, 6000);
    }
    // product added to cart END
    */

    $('li.has-menu > span').on('click', function() {
        console.log('click has-menu');
        let ul = $(this).closest('ul.nav');
        //$('li', ul).removeClass('show');
        $(this).parent('.has-menu').toggleClass('active').find('.has-menu').addClass('active');
    });

    if($.isFunction($.fn.matchHeight)) {
        //$('section.solutions .solution h3').matchHeight({});
        $('ul.main-menu > li').on({
            mouseenter: function () {
                $(' > ul > li > a', this).matchHeight({});
            }
        });
    };

    $('.search-toggler').on('click', function(e) {
        e.preventDefault();
        $('#search-container').slideToggle();
    });

    //
    // google analytics
   /* if (typeof gac !== 'undefined') {
        gaInit(gac);
    }*/
    // google analytics END
    //
    // delivery & payment
    if ($('#step1-form #delivery').length != 0 && $('#step1-form #payment').length != 0) {
        var selected_delivery = $('#delivery input:checked').attr('rel');
        var delivery_array = selected_delivery.split(',');
        //
        // default
        $.each(delivery_array, function () {
            $('#payment label#payment_' + this).css('display', 'inline-block');
        });
        $('#payment label#payment_' + $('#payment input:checked').val()).find('small').delay('500').css('display', 'inline-block');
        //
        // on change
        $('#delivery input').on('change', function () {
            var selected_delivery = $('#delivery input:checked').attr('rel');
            var delivery_array = selected_delivery.split(',');
            $('#payment input').prop('checked', false).attr('checked', false);
            $('#payment label').css('display', 'none');
            $.each(delivery_array, function () {
                $('#payment label#payment_' + this).css('display', 'inline-block');
            });
            //$('#payment label').find('small').css('display', 'none');
        });

        $('#payment input').on('change', function () {
            //$('#payment label').find('small').css('display', 'none');
            $('#payment label#payment_' + $('#payment input:checked').val()).find('small').delay('500').css('display', 'inline-block');
        });
    }
    // payment & shipping END

    /*$('#nav').slimmenu({
        resizeWidth: collapseWidth,
        collapserTitle: ' ',
        animSpeed: 'medium',
        easingEffect: null,
        indentChildren: false,
        childrenIndenter: '&nbsp;'
    });*/

    $('#catalogue.carousel').on('init', function(event, slick){
        // Add accessible label to the listbox track
        slick.$slideTrack.attr('aria-label', 'Produkty v katalógu');
    }).on('init afterChange', function(event, slick){
        slick.$slides.each(function() {
            var isHidden = $(this).attr('aria-hidden') === 'true';
            $(this).find('a, button, select, input').attr('tabindex', isHidden ? '-1' : '0');
        });
        slick.$slideTrack.find('.slick-cloned').each(function() {
            var isHidden = $(this).attr('aria-hidden') === 'true';
            $(this).find('a, button, select, input').attr('tabindex', isHidden ? '-1' : '0');
        });
    }).slick({
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        responsive: [
            {
            breakpoint: 768,
            settings: {
                slidesToShow: 3,
                slidesToScroll: 1
                }
            },
            {
            breakpoint: 630,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
                }
            },
            {
            breakpoint: 480,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });

    $('#features').on('init', function(event, slick){
        // Add accessible label to the listbox track
        slick.$slideTrack.attr('aria-label', 'Partneri a značky');
    }).on('init afterChange', function(event, slick){
        slick.$slides.each(function() {
            var isHidden = $(this).attr('aria-hidden') === 'true';
            $(this).find('a, button, select, input').attr('tabindex', isHidden ? '-1' : '0');
        });
        slick.$slideTrack.find('.slick-cloned').each(function() {
            var isHidden = $(this).attr('aria-hidden') === 'true';
            $(this).find('a, button, select, input').attr('tabindex', isHidden ? '-1' : '0');
        });
    }).slick({
        dots: false,
        infinite: true,
        speed: 300,
        slidesToShow: 3,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        responsive: [
            {
            breakpoint: 768,
            settings: {
                slidesToShow: 2,
                slidesToScroll: 1
                }
            },
            {
            breakpoint: 400,
            settings: {
                slidesToShow: 1,
                slidesToScroll: 1
                }
            }
            // You can unslick at a given breakpoint now by adding:
            // settings: "unslick"
            // instead of a settings object
        ]
    });

    $(window).trigger('resize');    
});
$(window).load(function () {
    if ($('#messages-container').length != 0) {
        $('#messages-container').delay(5000).fadeOut(500);
    }

});

$(window).on('resize', function() {

    footerAllwaysBottom();

    /*
    if($(window).width() > 991) {
        $('body').find('.parent-height').each(function () {
            $(this).height($(this).parent().height());
        });
    };
    */

    /*
    if(collapseWidth < $(window).width()) {
        autoPaddingMenu('#menu', 0); // edge: 0 - bocne okraje, 1 - bez bocnych oktajov
    }
    */

    // if($(window).width() > 767) {
    //     $('.pull-down').each(function() {
    //         var $this = $(this);
    //         $this.css('margin-top', $this.parent().height() - $this.height())
    //     });
    // }

    $('footer .paygates > span').width($('footer .paygates ul').width());

    if($('#catalogue .item').width() < 220) {
        $('#catalogue .item').addClass('narrow');
    }
    else {
        $('#catalogue .item').removeClass('narrow');
    }

});


// function
//
// google analytics 
/*function gaInit(code) {
    (function (i, s, o, g, r, a, m) {
        i['GoogleAnalyticsObject'] = r;
        i[r] = i[r] || function () {
            (i[r].q = i[r].q || []).push(arguments)
        }, i[r].l = 1 * new Date();
        a = s.createElement(o),
                m = s.getElementsByTagName(o)[0];
        a.async = 1;
        a.src = g;
        m.parentNode.insertBefore(a, m)
    })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

    ga('create', code, 'auto');
    ga('send', 'pageview');
}*/
// google analytics END
//
// footer allways bottom
function footerAllwaysBottom() {
    if($('#wrapper').height() < $(window).height())
        $('#content').css('min-height', (( $(window).height() - $('#header').outerHeight(true) - $('footer').outerHeight(true)) + 'px'));
}
// footer allways bottom END
//
//
// autopadding for menu
function autoPaddingMenu(container, edge) {

        if($(window).width() > 767) { //991
            var container = $(container);
            var container_width = parseInt(container.width());
            var li_count = container.find(' > ul > li').length;

            var actual_link_width = 0;
            var liSubWidth = 0;
            var liSubMargin = 0;
            container.find(' > ul > li > a').each(function (index) {
                liSubWidth += $(this).width();
                //console.log('a['+index+'] width: ' + $(this).width());
                liSubMargin += $(this).outerWidth(true) - $(this).outerWidth();
            });
            //console.log('liSubWidth: ' + liSubWidth);
            var difference = container_width - liSubWidth - liSubMargin;
            //console.log('difference: ' + difference);
            var paddingAccurately = (difference / (li_count - edge)) / 2;
            var padding = Math.floor((difference / (li_count - edge)) / 2) - 1;
            //console.log('paddingAccurately: ' + paddingAccurately);
            //console.log('padding: ' + padding);
            widthDifference = (2 * paddingAccurately * (li_count - edge)) - (2 * padding * (li_count - 1));
            //console.log('widthDifference: ' + widthDifference);

            container.find(' > ul > li > a').each(function (index) {
                if(edge == 1) {
                    if(index == 0) $(this).css({'padding-left' : '0px', 'padding-right' : padding + 'px', 'margin-left' : (widthDifference - 2) + 'px'});
                    else if(index == (li_count - 1)) $(this).css({'padding-left' : (padding) + 'px', 'padding-right' : '0px'});
                    else $(this).css({'padding-left' : (padding) + 'px', 'padding-right' : (padding) + 'px'});
                }
                else {
                    $(this).css({'padding-left' : (padding) + 'px', 'padding-right' : (padding) + 'px'});
                }
            });
        }
        else {
            $('#menu > ul > li > a').css({'padding-left' : '15px', 'padding-right' : '15px'});
        }
        // treba dopracovat border-width (padding - border-width)
}
// autopadding for menu end
//
//
// item same height
function itemSameHeight(target, minHeight) {
    var maxHeight = Math.max.apply(null, $(target).map(function () {
        return $(this).height();
    }).get());
    if(maxHeight < minHeight) maxHeight = minHeight;
    $(target).height(maxHeight);
}
// item same height end
//
// square format for element with class square
function makeSquare() {
    $(document).find($('.square')).each(function (index) {
        var itemWidth = $(this).width();
        $(this).css({'width': '100%', 'height': itemWidth + 'px'});
        // console.log(itemWidth);
    });
}
// isquare format for element with class square END
//
// info label same width
function labelSameWidth(target, minWidth) {
    var maxWidth = Math.max.apply(null, $(target).map(function () {
        return $(this).width();
    }).get());
    if(maxWidth < minWidth) maxWidth = minWidth;
    $(target).width(maxWidth + 1);
}
// info label same width end
//

async function b_confirm(msg, bt0, bt1) {
    const modalElem = document.createElement('div')
        modalElem.id = "modal-confirm"
        modalElem.className = "modal"
        modalElem.innerHTML = `
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">             
                    <div class="modal-body fs-6">
                        <p>${msg}</p>
                    </div>
                    <div class="modal-footer" style="border-top:0px">             
                        <button id="modal-btn-descartar" type="button" class="button">${bt0}</button>
                        <button id="modal-btn-aceptar" type="button" class="button">${bt1}</button>
                    </div>
                </div>
            </div>
            `;
    const myModal = new bootstrap.Modal(modalElem, {
            keyboard: false,
            backdrop: 'static'
        });
    myModal.show();

    return new Promise((resolve, reject) => {
        document.body.addEventListener('click', response)

        function response(e) {
            let bool = false;
            if (e.target.id == 'modal-btn-descartar') bool = false;
            else if (e.target.id == 'modal-btn-aceptar') bool = true;
            else return;

            document.body.removeEventListener('click', response);
            document.body.querySelector('.modal-backdrop').remove();
            modalElem.remove();
            myModal.hide();
            resolve(bool);
        }
    });
}