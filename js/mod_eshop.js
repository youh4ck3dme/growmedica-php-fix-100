var ajaxUrl = rootdir + '/ajax/';

$(document).ready(function () {
    $('.drop-down').each(function() {
        $(this).attr('data-height', $(this).outerHeight());
    });
    $('.drop-down-toggler > span').on('click', function(e) {
        let h = $(this).outerHeight();
        let d = $(this).closest('.drop-down');
        d.toggleClass('dropped');
        curHeight = d.height(),
        autoHeight = d.css('height', 'auto').height();
        if(d.hasClass('dropped')) {            
            d.height(curHeight).animate({height: (autoHeight + h)}, 300);            
        }
        else {
            d.height(autoHeight).animate({height: d.data('height')}, 300);
        }
    });
/*
	$('.add-to-cart').click(function() {
        //console.log('click');
        $(this).parent().parent().parent().addClass('processing');

        var product_id = $(this).attr('data-product');
        var price_item = $(this).attr('data-price');
        var amount = 1;
        var itemImg = $(this).parent().find('img').eq(0);
        //console.log("itemImg: " + itemImg);

        var inStock = true;
        $('#shopping-cart-info div').each(function() {
            if(parseInt($(this).attr('data-pid')) == product_id && parseInt($(this).attr('data-stock')) < (parseInt($(this).attr('data-amount')) + amount)) {
                console.log('not in inStock');
                //$(this).attr('data-amount', parseInt($(this).attr('data-amount')) + amount);
                inStock = false;
            }
        });
        if(inStock) {
            insertItemToCart(product_id, amount, price_item, itemImg);
        }
        else {
            bootbox.dialog({
                closeButton: false,
                message: $('#msgs .max-amount').text(),
                title: $('#msgs .notice-title').text(),
                buttons: {
                    success: {
                        label: $('#msgs .close-title').text(),
                        callback: function() {
                            $("input#amount").val($("input#amount").attr('rel'));
                        }
                    }
                }
            });
        }

        

    //    $('.processing').delay(800).css('background-color', 'green');
        $('.product.item').removeClass('processing');
    });*/

    if($.isFunction($.fn.matchHeight)) {
        $('.cart-item .name').matchHeight({});
    };

    
    $('.changer [name="update_item"]').on('change', function (e) {
        e.preventDefault();
        var amount = $(this).val();
        //console.log(eshopdir + '/kosik/update_item/' + $(this).data('key') + '?amount=' + amount);
        document.location.href = eshopdir + '/kosik/update_item/' + $(this).data('key') + '?amount=' + amount;
    });
});


function insertItemToCart(product_id, amount, price_item, itemImg, popup) {
	//console.log(ajaxUrl + 'insert_item_to_cart.php');
    $.ajax({
    	type: 'POST',
    	url: ajaxUrl + 'insert_item_to_cart.php',
    	data: {product_id: product_id, 
    		amount: amount, 
            price_item: price_item
    		}
            }).done(function (result) {
                result = $.parseJSON(result);
                console.log(result);
                if (result[0] == 'empty-values') {
                    alert(result[2]);
                    return false;
                } 
                else if (result[0] == 'options') {
                    $('label#size-container').removeClass('hidden');
                    if ($('select#size-select').length == 0) {
                        $('<select name="size" id="size-select">' + result[3] + '</select>').appendTo($('label#size-container'));
                    } else {
                        $('select#size-select').html(result[3]);
                    }
                    return false;
                } 
                else if (result[0] == 'universal') {
                    $('select#size-select').remove();
                    $(result[3]).appendTo($('label#size-container'));
                    $('label#size-container').addClass('hidden');
                    return false;
                }
                else if (result[0] == 'added') {
                /*    $('html, body').animate({
                        'scrollTop' : $("$('#shopping-cart-preview .basket").position().top
                    });*/
                    var flyingTo = $('#header .shopping-cart');
                    flyToElement($(itemImg), flyingTo);
                    $('.shopping-cart .cart.value > span').text(result[1]);
                    $('.shopping-cart .cart.quantity > span').text(result[2]);
                    //console.log("cart_count: " + result[1]);
                    //console.log("cart_quantity: " + result[2]);
                    
                    updateShoppingCartInfo(product_id, amount);

                    bootbox.confirm({
                        title: '',
                        message: popup.message,
                        buttons: {
                            cancel: {
                                label: popup.cancel
                            },
                            confirm: {
                                label: popup.confirm
                            }
                        },
                        callback: function (result) {
                            if(result)
                                window.location.replace( popup.redirect);
                        }
                    });

                    return false;
                }
            });
}


function flyToElement(flyer, flyingTo) {
    var $func = $(this);
    var divider = 5;
    var flyerClone = $(flyer).clone();
    $(flyerClone).css({position: 'absolute', top: $(flyer).offset().top + "px", left: $(flyer).offset().left + "px", opacity: 1, 'z-index': 10000, width: $(flyer).width() + "px", height: $(flyer).height() + "px"});
    $('body').append($(flyerClone));
    var gotoX = $(flyingTo).offset().left + ($(flyingTo).width() / 2) - ($(flyer).width()/divider)/2;
    var gotoY = $(flyingTo).offset().top + ($(flyingTo).height() / 2) - ($(flyer).height()/divider)/2;
     
    $(flyerClone).animate({
        opacity: 0.4,
        left: gotoX,
        top: gotoY,
        width: $(flyer).width()/divider,
        height: $(flyer).height()/divider
    }, 1000,
    function () {
        $(flyingTo).fadeOut('fast', function () {
            $(flyingTo).fadeIn('fast', function () {
                $(flyerClone).fadeOut('fast', function () {
                    $(flyerClone).remove();
                });
            });
        });
    });
}


function checkThatTheProductIsInStock(product_id, amount) {
    amount = amount || 1;
    console.log('amount: ' + amount);

    var inStock = true;
    $('#shopping-cart-info div').each(function() {
        if(parseInt($(this).attr('data-pid')) == product_id && parseInt($(this).attr('data-stock')) < (parseInt($(this).attr('data-amount')) + amount)) {
            console.log('not in inStock');
            //$(this).attr('data-amount', parseInt($(this).attr('data-amount')) + amount);
            inStock = false;
        }
    });
    if(!inStock) {
        bootbox.dialog({
            closeButton: false,
            message: $('#msgs .max-amount').text(),
            title: $('#msgs .notice-title').text(),
            buttons: {
                success: {
                    label: $('#msgs .close-title').text(),
                    callback: function() {
                        $("input#amount").val($("input#amount").attr('rel'));
                    }
                }
            }
        });
    }
    return inStock;
}
function updateShoppingCartInfo(product_id, amount){

    amount = amount || 1;
    console.log('n: ' + getNumberOfProductInStock(product_id));
    stock = parseInt(getNumberOfProductInStock(product_id));
    console.log('stock: ' + stock);

    var inCart = false;

    $('#shopping-cart-info div').each(function() {
        //console.log('data-pid: ' + $(this).attr('data-pid') + ' | ' + product_id);
        if(parseInt($(this).attr('data-pid')) == product_id) {
            $(this).attr('data-amount', parseInt($(this).attr('data-amount')) + amount);
            $(this).attr('data-stock', stock);
            inCart = true;
        }
    });

    if(!inCart) {
        $('<div />', {
            'data-pid': product_id,
            'data-stock': stock, 
            'data-amount': amount
        }).appendTo('#shopping-cart-info');
    }
}
function getNumberOfProductInStock(product_id){

    var result = $.ajax({
        type: 'POST',
        url: ajaxUrl + 'get-number-of-product-in-stock.php',
        data: {
            product_id: product_id
        },
        async: false,
        success: function (response) {
            response = $.parseJSON(response);
            //console.log('response: ' + response);
        }
    }).responseText;

    result = $.parseJSON(result);
    if (result[0] == 'ok') {
        //console.log('result: ' + result[1]);
        result = result[1];
    }
    else {
        //console.log('result: false');
        result = false;
    }
    return result;
}

/*
$(function(){
                $("#moveitButton").click(function() {
                    doit();
                })

            });

            function doit() {
                var div2Pos = $("#div2").position();
                var div2Width = $("#div2").css("width");
                var div2Height = $("#div2").css("height");
                $("#div1").animate({left:div2Pos.left, width:div2Width, height:div2Height}, 1000);          
            }
*/


/*
http://www.codexworld.com/fly-to-cart-effect-using-jquery/
https://codyhouse.co/gem/quick-add-to-cart/

*/