;
'use strict';


$(window).on('load', function () {
    var $preloader = $('#p_prldr'),
        $svg_anm = $preloader.find('.svg_anm');
    $svg_anm.fadeOut();
    // $preloader.delay(100).fadeOut('slow');
    $preloader.fadeOut('slow');
});

/*
$('.profile-account__flex input').not( $('#submit') ).attr('disabled', 'disabled');

$('.profile-account__flex input#submit').on('click', function () {
    $(this).attr('type', 'submit');
    $('.profile-account__flex input').removeAttr('disabled').trigger( 'focus' );
    $('#updateForm').slideDown();
    $('.profile-account__flex input').css('border-color', '#282828');
});
*/


$('.openProfile').click(function () {
    $(this).slideUp('fast');
    $('.profile-header').slideDown();
});

$('.closeProfile').click(function () {
    $('.profile-header').slideUp();
    $('.openProfile').slideDown('fast');
});

// 203 change function name, 213 - add 3 str

var headerHeight = $('#header').height();
var profileHeight = $('.profile-header').height();
var footerHeight = $('#footer').height();
var heightDialogs = footerHeight + profileHeight + headerHeight + 2;
var heightMsg = footerHeight + profileHeight + headerHeight + 80;


$('.chat-dialogs').css('height', 'calc(100vh - ' + heightDialogs + 'px)');
$('#msgArea').css('height', 'calc(100vh - ' + heightMsg + 'px)');

// header
var signEl = $('.header__sign');
var btnEl = $('.header__mybtn-wrapper');

$(signEl).clone().prependTo('.header__menu-list');
$(btnEl).appendTo('.header__menu-list');
$("#toggle-menu").click(function () {

    $(this).toggleClass("on");
    $(".header__menu-list").toggleClass('active');
    $(".header__logo-wrapper").toggleClass('hide');

    $(".cart").toggleClass('hide');
    $("header > .sign").toggleClass('hide');
    $("body").children().not('header, .header--white').toggleClass('blur');
});

// eof header


// wine-card.html page
$(window).scroll(function () {
    var scrollWineCard = $(window).scrollTop();
    var wineCardHeight = $('.wine-card').height();
    if (scrollWineCard > wineCardHeight) {
        $('.header-wine-card').addClass('active');
    } else if (scrollWineCard < wineCardHeight) {
        $('.header-wine-card').removeClass('active');
    }
});

var bottleHeight = $('#wineCardBottle').height();
var blockHeight = $('.wine-card__flex-item').height();
var bottomProp = (blockHeight - bottleHeight) / 2;
$('.wine-card-description').css('bottom', bottomProp);
// eof wine-card.html page

// stars init (for rate)

$(function () {

    $("#rateYo").rateYo({
        rating: 4,
        normalFill: 'rgb(197,197,197)',
        ratedFill: '#000',
        starSvg: '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32">' +
        '<path d="M32 12.408l-11.056-1.607-4.944-10.018-4.944 10.018-11.056 1.607 8 7.798-1.889 11.011 9.889-5.199 9.889 5.199-1.889-11.011 8-7.798z"></path>' +
        '</svg>',
        fullStar: true
    });

});


$('#scroll-blocks').scrollspy({target: '#navbar-example'})


$(window).scroll(function () {
    var darkItem = $('.pageScroll__menu-item-dark').hasClass('active');
    // console.log(darkItem);
    if (darkItem == true) {
        $('.pageScroll__menu').addClass('pageScroll__menu-dark');
    } else {
        $('.pageScroll__menu').removeClass('pageScroll__menu-dark');
    }
});


$(function () {

    //STEPS SCRIPT START
    //Steps form fullscreen search start
    var $searchTrigger = $(".js-steps-search-trigger");
    $searchTrigger.on("click", function () {
        $(".steps-search").fadeIn(200);
        $(".drop-form__input").focus();
    });
    $(".js-steps-search__close").on("click", function () {
        $(".steps-search").fadeOut(200);
    });
    //Steps form fullscreen search end

    //STEPS TITLES MATCH HEIGHT START
    // $.fn.equalHeights = function () {
    //     var maxHeight = 0,
    //         $this = $(this);
    //
    //     $this.each(function () {
    //         var height = $(this).innerHeight();
    //
    //         if (height > maxHeight) {
    //             maxHeight = height;
    //         }
    //     });
    //
    //     return $this.css("height", maxHeight);
    // };
    //
    // $(".steps-item__figure").equalHeights();
    //$(".steps-item__midtitle").equalHeights();
    //STEPS TITLES MATCH HEIGHT END


    //STEPS SCRIPT END


});

// to fix btn on LEARN page

$(window).scroll(function () {
    var offset = $(document).scrollTop();
    var windowWidth = $('body').width();

    if (windowWidth > 992 && offset >= 242) {
        $("#toFix").addClass('btn-fixed');
    } else {
        $("#toFix").removeClass('btn-fixed');
    }
});

// page: become-a-partner

/*var form = $("#example-form");
form.validate({
    errorPlacement: function errorPlacement(error, element) {
        element.before(error);
    },
    rules: {
        confirm: {
            equalTo: "#password"
        }
    }
});
form.children("div").steps({
    headerTag: "h3",
    bodyTag: "section",
    transitionEffect: "slideLeft",
    onStepChanging: function (event, currentIndex, newIndex) {
        form.validate().settings.ignore = ":disabled,:hidden";
        return form.valid();
    },
    onFinishing: function (event, currentIndex) {
        form.validate().settings.ignore = ":disabled";
        return form.valid();
    },
    onFinished: function (event, currentIndex) {
        alert("Submitted!");
    }
});*/


$('.actions ul li:nth-child(2)').addClass('next-step mybtn');
$('.actions ul li:nth-child(3)').addClass('next-step mybtn');

function filePreviewNext(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.become-content-label-wrapper img').remove();
            $('#uploadForm').before('<img class="uploadPhoto" src="' + e.target.result + '" width="140" height="140"/>');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

$("#file").change(function () {
    filePreviewNext(this);
});

function filePreview(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            $('.profile-account__update-photo img').remove();
            $('#updateForm').before('<img class="uploadPhoto" src="' + e.target.result + '" width="95" height="95"/>');
        }
        reader.readAsDataURL(input.files[0]);
        $(input).closest('form').submit();
    }
}

$("#app_user_profile_avatar_imageFile_file").change(function () {
    filePreview(this);
});

$().ready(function () {
    if ($(".profile-account").size() > 0) {
        var url = location.href;
        var idx = parseInt(url.indexOf("#"));

        if (idx > 0) {
            var hash = idx != -1 ? url.substring(idx + 1) : "";
            $("a.basic-text[href='#" + hash + "']").trigger('click');
        }

    }
});

// CART.HTML page
$(function () {

    // add taste wine
    var productID;
    $('.add-test').on('click', function () {
        productID = $(this).attr('product-id');
        // $('#tasteWine').attr('product-id', productID);
        // $('#removeWine').attr('product-id', productID);
    });

    $('.add-test-handler').click(function () {
        // addTest.removeAttr('data-target');
        var that = $('.add-test[product-id="' + $(this).parents('.modal').attr('product-id') + '"]');
        $(that).removeAttr('data-target');

        var totalPrice = parseInt($('.cart-total-price').text().slice(1).replace(/,/g, ''));
        if (!$(that).closest('.cart-wrapper-wine').hasClass('active')) {
            // Add Bottles Values into Total Price
            totalPrice += parseInt($(that).closest('.cart-wrapper').find('.advanced-price-wrap').text());
            totalPrice = number_format(totalPrice, 2);
            $('.cart-total-price').html('$' + totalPrice);
            // console.log($(that).closest('.cart-wrapper-wine').find('.block-wrap__quantity-input-advanced .ui-spinner-input').spinner( "value"));
            if ($(that).closest('.cart-wrapper-wine').find('.block-wrap__quantity-input-advanced .ui-spinner-input').spinner( "value") == 0){
                $(that).closest('.cart-wrapper-wine').find('.block-wrap__quantity-input-advanced .ui-spinner-input').spinner( "value", 1 );
            }
            /*console.log($(that).closest('.cart-wrapper-wine').find('.block-wrap__quantity-input-advanced .ui-spinner-up'))
            $(that).closest('.cart-wrapper-wine').find('.block-wrap__quantity-input-advanced .ui-spinner-up').trigger('click');*/
        } else {
            totalPrice -= parseInt($(that).closest('.cart-wrapper').find('.advanced-price-wrap').text());
            $('.cart-total-price').html('$' + totalPrice);
        }

        $(that).closest('.cart-wrapper-wine').toggleClass('active');

        // creating new el for mobile version
        if ($(window).width() < 480) {
            $(that).closest('.cart-wrapper-wine').find('.new-block-sub-taste').append($(that).closest('.cart-wrapper-wine').find('.sub-taste'));
            $(that).hide();
        }
        // $('#tasteWine').hide();
        // $('.modal-backdrop').remove();
    });

    // rm block w/ taste wine
    $('.close-sub-taste').click(function () {
        $(this).parents('.cart-wrapper-wine').find('.add-test').attr('data-target', '#tasteWine-' + $(this).parents('.cart-wrapper-wine').attr('product-id') + '');
        // $(this).closest('.cart-wrapper-wine').removeClass('active');
        // $(this).closest('.cart-wrapper-wine').find('.new-block-sub-taste').hide();
        $(this).closest('.cart-wrapper-wine').find('.add-test').show();
    });
    //$('.removeWine-btn').on('click', function () {
    //    $('.cart-wrapper-wine[product-id="' + $(this).attr('product-id') + '"]').remove();
    // $(this).parents('.modal').prev().prev().remove();
    //});

    $(function () {
        $('.flex-cart-item-price__rm').click(function () {
            var rmButton = $(this).closest('.cart-wrapper-wine');
            $('.close-cart-wrapper-wine').click(function () {
                $(rmButton).hide();
            });
        })
    });
});


// cart item
$(".ui-spinner-wrapper > input").spinner({
    min: 10,
    step: 10,
    incremental: false,
    spin: function (event, ui) {
        spinHandler(event, ui);
    }
});

function number_format (number, decimals, decPoint, thousandsSep) {
    // eslint-disable-line camelcase
    //  discuss at: http://locutus.io/php/number_format/
    // original by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // improved by: Kevin van Zonneveld (http://kvz.io)
    // improved by: davook
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Brett Zamir (http://brett-zamir.me)
    // improved by: Theriault (https://github.com/Theriault)
    // improved by: Kevin van Zonneveld (http://kvz.io)
    // bugfixed by: Michael White (http://getsprink.com)
    // bugfixed by: Benjamin Lupton
    // bugfixed by: Allan Jensen (http://www.winternet.no)
    // bugfixed by: Howard Yeend
    // bugfixed by: Diogo Resende
    // bugfixed by: Rival
    // bugfixed by: Brett Zamir (http://brett-zamir.me)
    //  revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    //  revised by: Luke Smith (http://lucassmith.name)
    //    input by: Kheang Hok Chin (http://www.distantia.ca/)
    //    input by: Jay Klehr
    //    input by: Amir Habibi (http://www.residence-mixte.com/)
    //    input by: Amirouche
    //   example 1: number_format(1234.56)
    //   returns 1: '1,235'
    //   example 2: number_format(1234.56, 2, ',', ' ')
    //   returns 2: '1 234,56'
    //   example 3: number_format(1234.5678, 2, '.', '')
    //   returns 3: '1234.57'
    //   example 4: number_format(67, 2, ',', '.')
    //   returns 4: '67,00'
    //   example 5: number_format(1000)
    //   returns 5: '1,000'
    //   example 6: number_format(67.311, 2)
    //   returns 6: '67.31'
    //   example 7: number_format(1000.55, 1)
    //   returns 7: '1,000.6'
    //   example 8: number_format(67000, 5, ',', '.')
    //   returns 8: '67.000,00000'
    //   example 9: number_format(0.9, 0)
    //   returns 9: '1'
    //  example 10: number_format('1.20', 2)
    //  returns 10: '1.20'
    //  example 11: number_format('1.20', 4)
    //  returns 11: '1.2000'
    //  example 12: number_format('1.2000', 3)
    //  returns 12: '1.200'
    //  example 13: number_format('1 000,50', 2, '.', ' ')
    //  returns 13: '100 050.00'
    //  example 14: number_format(1e-8, 8, '.', '')
    //  returns 14: '0.00000001'

    number = (number + '').replace(/[^0-9+\-Ee.]/g, '')
    var n = !isFinite(+number) ? 0 : +number
    var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals)
    var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep
    var dec = (typeof decPoint === 'undefined') ? '.' : decPoint
    var s = ''

    var toFixedFix = function (n, prec) {
        var k = Math.pow(10, prec)
        return '' + (Math.round(n * k) / k)
            .toFixed(prec)
    }

    // @todo: for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.')
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep)
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || ''
        s[1] += new Array(prec - s[1].length + 1).join('0')
    }

    return s.join(dec)
}

function spinHandler(event, ui) {

    event.stopPropagation();

    var unitPrice = +$(event.target).data('unit-price');
    var unitAdvancedPrice = +$(event.target).data('unit-advanced-price');
    var productId = $(event.target).data('product-id');

    var totalPrice = parseFloat($('.cart-total-price').text().slice(1).replace(/,/g, '')),
        count = ui.value,
        itemAdvancedPrice = parseInt(count * unitAdvancedPrice),
        itemPrice = parseInt((count / 10) * unitPrice);

    // console.log(event.currentTarget.text);
    if ($(event.target).hasClass('quantity-input')) {
        if (+event.target.value <= 20) {
            $(event.target).parent().find('.ui-spinner-down').css("pointer-events", "none");
        }


        $(event.target).closest('.cart-wrapper').find('#basket-element-wines-' + productId).html(itemPrice);

        if (event.currentTarget.text === '-') {
            unitPrice = -unitPrice;
        }
        totalPrice += unitPrice;

        if (event.currentTarget.text === '+') {
            $(event.target).parent().find('.ui-spinner-down').css("pointer-events", "auto");
        }

    } else {
        if (+event.target.value <= 2) {
            $(event.target).parent().find('.ui-spinner-down').css("pointer-events", "none");
        }

        $(event.target).closest('.cart-wrapper').find('#basket-element-bottles-' + productId).html(itemAdvancedPrice);

        if (event.currentTarget.text === '-') {
            unitAdvancedPrice = -unitAdvancedPrice;
        }
        totalPrice += unitAdvancedPrice;

        if (event.currentTarget.text === '+') {
            $(event.target).parent().find('.ui-spinner-down').css("pointer-events", "auto");
        }

    }

    totalPrice = number_format(totalPrice, 2);
    $('.cart-total-price').html('$' + totalPrice);
    // $(event.target).closest('.cart-wrapper').find('.flex-cart-item-price__sum').html(count*unitPrice);
}

$(".ui-spinner-taste-wrapper > input").spinner({
    min: 1,
    step: 1,
    incremental: false,
    spin: function (event, ui) {
        spinHandler(event, ui);
    }
});
$('.ui-spinner-up').text('+');
$('.ui-spinner-down').text('-');
// eof cart item


// page: DECISION.HTML
var decisionFormCellar = $("#decision-cellar-form");
decisionFormCellar.validate({
    errorPlacement: function errorPlacement(error, element) {
        element.before(error);
    },
    rules: {
        confirm: {
            equalTo: "#password"
        }
    }
});
decisionFormCellar.children("div").steps({
    headerTag: "h3",
    bodyTag: "section",
    transitionEffect: "slideLeft",
    onStepChanging: function (event, currentIndex, newIndex) {

        $("#"+event.handleObj.namespace+"-p-"+newIndex).find('.share-mybtn').attr('data-step-id', newIndex);

        decisionFormCellar.validate().settings.ignore = ":disabled,:hidden";
        return decisionFormCellar.valid();
    },
    onFinishing: function (event, currentIndex) {
        decisionFormCellar.validate().settings.ignore = ":disabled";
        return decisionFormCellar.valid();
    },
    onFinished: function (event, currentIndex) {
        alert("Submitted!");
    }
});

var decisionFormField = $("#decision-field-form");
decisionFormField.validate({
    errorPlacement: function errorPlacement(error, element) {
        element.before(error);
    },
    rules: {
        confirm: {
            equalTo: "#password"
        }
    }
});
decisionFormField.children("div").steps({
    headerTag: "h3",
    bodyTag: "section",
    enableAllSteps: true,
    transitionEffect: "slideLeft",
    enablePagination: false,
    onStepChanging: function (event, currentIndex, newIndex) {

        $("#"+event.handleObj.namespace+"-p-"+newIndex).find('.share-mybtn').attr('data-step-id', newIndex);

        decisionFormField.validate().settings.ignore = ":disabled,:hidden";
        return decisionFormField.valid();
    },
    onFinishing: function (event, currentIndex) {
        decisionFormField.validate().settings.ignore = ":disabled";
        return decisionFormField.valid();
    },
    onFinished: function (event, currentIndex) {
        alert("Submitted!");
    }
});


/* SPOILER - (MODULES) =>
 *   .mySpoiler-btn - link to open spoiler;
 *   .mySpoiler-block - spoiler-block
 */

$(document).ready(function () {
    $('.mySpoiler-btn').click(function () {
        $(this).prev('.mySpoiler-block').toggle('normal');
        return false;
    });
});
$(document).ready(function () {
    $('.spoiler-btn').click(function () {
        $(this).next('.spoiler-block').toggle();
        return false;
    });
    // wine-card (mobile)
    $('.wine-card__spoiler-title').click(function () {
        $('.wine-card__spoiler-title').removeClass('active');
        $(this).addClass('active');
    })
});

$(".spoiler-item .spoiler-btn").click(function () {
    $(this).toggleClass('active');
    $(this).parent().toggleClass('active');
});
// $('.spoiler-btn').click(function () {
//     $('.spoiler-block').show();
// });

/*
 *  oef SPOILER (MODULES)
 */
// EOF DECISION.HTML


// page profile-active.html

$('.nav-tabs a').click(function (e) {
    e.preventDefault();
    $(this).tab('show');
});

// eof page profile-active.html


// rate-wine.html page

// eof rate-wine.html page







