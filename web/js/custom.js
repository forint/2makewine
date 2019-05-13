$(window).load(function () {
    // $(".steps-item__figure").equalHeights();
    setTimeout(function () {
        $(".steps-main-wrapper").removeClass("is-loaded");
    }, 300);
});

$(function () {



    var notifDialog = $('.msg-container-msg');
    var notifDialogText = $('#msg-container-empty-content-text');

    notifDialogText.text('You don\'t have any notification');

    if (notifDialog.length > 0) {
        notifDialogText.text('Select notification');
    }

    $('.tab__conten-preload').removeClass('tab__conten-preload');

    $('.tab-pane-trigger').click(function () {
        var tab_id = $(this).attr('data-tab-id');

        $('#msg-container-empty-content').remove();
        // $('#msg-container-empty-content').fadeOut(1000);

        $('.tab-pane-trigger').removeClass('tab-pane-trigger-active');
        $(this).addClass('tab-pane-trigger-active');

        // $('.msg-container-msg').addClass('no-active');
        $('.msg-container-msg').removeAttr('style');
        // $("#" + tab_id).removeClass('no-active');
        $("#" + tab_id).fadeIn();

    });

});

$(document).ready(function () {
    searchTimer = 0;

    function searchWine(input) {
        searchbar = input;
        query = input.val();
        notFoundMsg = input.data('nf');
        if (query.length >= 1) {
            url = searchbar.data('ajaxsearch');
            $('.drop-form .preload__wrap').show();
            $.ajax({
                url: url,
                dataType: 'json',
                data: {
                    'name': query
                },
                async: true,
                // beforeSend: function () {
                //     $('#preload-background').show('fast');
                //     $('.steps-header__title').text(92222);
                //     $('.preload__wrap').show('fast');
                //     console.log('-------- props ---------- ');
                //     console.log(123);
                //     console.log('-------- props ---------- ');
                // },
                method: 'GET'
            }).done(function (data) {
                $('.s-results__body').html('').removeClass('hidden');
                $('.s-results__title span').html('"' + query + '"');
                $('.s-results__title').removeClass('hidden');
                if (data.length > 0) {
                    results = '';
                    $.each(data, function (key, value) {
                        results +=
                            '<form class="s-results-row" method="post" action="' + searchbar.data('map') + '">' +
                            '<div class="s-results-row__figure">' +
                            '<img class="s-results-row__img" src="' + value.imagePath + '" alt="">' +
                            '</div>' +
                            '<div class="s-results-row__name">' +
                            '<div class="s-results-row__title">' + value.name + '</div>' +
                            '<div class="s-results-row__desc">' + value.description + '</div>' +
                            '</div>';
                        $.each(value.wines, function (wk, wval) {
                            results += '<input  type="hidden" name="wine[]" value="' + wval + '">'
                        });
                        results += '</form>'
                    });
                } else {
                    results = '<div class="s-results-row s-results-row__desc">' + notFoundMsg + '</div>';
                }

                $('.s-results__body').html(results);
                $('.preload__wrap').hide();
            });
        }
        else {
            $('.s-results__body').addClass('hidden');
            $('.s-results__title').addClass('hidden');
        }
    }

    $('html, body').not('.cart-drop').on('click', function () {
        if ($(".cart-drop").size() > 0 && $(".cart-drop").css('display', 'block')) {
            $(".cart-drop").hide();
        }
    });

    $(document).on('keyup', '.steps-search .drop-form__input', function (e) {
        if (searchTimer) {
            clearTimeout(searchTimer);
        }
        searchTimer = setTimeout(searchWine, 500, $(this));
    });

    $(document).on('click', '.vineyard-item .ui-spinner-button', function () {
        var val = $(this).parent().find('.ui-spinner-input').val() / 10;
        price = $(this).parents('.vineyard-item').find('.vineyard-card__price span');
        price.text(val * price.data('base'));
    });

    $(document).on('click', '.wine-card .ui-spinner-button, .header-wine-card-item .ui-spinner-button', function () {
        var inputVal = $(this).parent().find('.ui-spinner-input').val();
        $('.ui-spinner-input').val(inputVal);
        var val = inputVal / 10;
        price = $('.wine-card-order-price span');
        price.text(val * price.data('base'));
    });

    $(document).on('submit', '.vineyard-item form', function () {
        $(this).find('.ui-spinner-input').removeAttr('disabled');
    });

    $(document).on('submit', '.rate-wine-form', function () {
        $(this).find('#form_rate').val($('#rateYo').rateYo("rating") * 20);
    });

    $(document).on('click', '.chat-message-submit', function (event) {

        event.preventDefault();

        var e = jQuery.Event("keypress");
        e.which = 13; //choose the one you want
        e.keyCode = 13;
        $(this).trigger(e);

    });

    $(document).on('click', '.back-trigger--winecard', function (e) {
        e.preventDefault();
        $('form#back_vineyards').submit();
    });

    if (jQuery('#quantity').size() > 0) {
        jQuery('#quantity-top').html(jQuery('#quantity').html());
    }

    if (jQuery('.sonata_add_basket_add').size() > 0) {
        var request = false;
        jQuery('.sonata_add_basket_add').on("click", function (e) {
            e.preventDefault();

            if (false === request) {
                request = true;
                var self = jQuery('form[id^="form_add_basket"]');

                jQuery.ajax({
                    type: self.attr('method'),
                    url: self.attr('action'),
                    data: self.serialize(),
                    beforeSend: function () {
                        $('#preload-background').show();
                        $('.preload__wrap').show();
                    },
                    success: function (data) {
                        if (data) {
                            $('.preload__wrap').hide();
                            $('#preload-background').hide();
                            jQuery(".cart-drop").replaceWith(data);
                            $('html, body').animate({scrollTop: '0px'}, 400, function () {
                                request = false;
                                if ($(document).width() > 480) {
                                    jQuery(".cart-drop").show();
                                }
                                jQuery('#quantity-top').html(jQuery('#header-section__quantity-top').html());
                            });
                        }
                    }
                });
            } else {
                return false;
            }
        });
    }

    $(document).on('click', '.sonata_add_basket_order', function (event) {
        event.preventDefault();
        $(this).closest('#form_add_basket').attr('id', 'form_add').submit();
    });

    $(document).on('click', '#update_basket_before_next_step', function (event) {
        event.preventDefault();

        $(this).closest('#example-form').find('.quantity-input, .advanced-quantity-input').each(function(){
            $(this).attr('disabled', false);
        });
        $(this).closest('#example-form').submit();
    });

    $(document).on( 'click','.removeWine-btn', function (e) {

        var context = $(this);
        e.preventDefault();

        var deleteBasketElementUrl = Routing.generate('app_basket_element_delete', {
            basketId: $(this).data('basket-id'),
            elementId: $(this).data('basket-element-position')
        });

        $.ajax({
            url: deleteBasketElementUrl,
            type: 'GET',
            dataType: 'json',
            error: function (xhr, ajaxOptions, thrownError) {
                console.log(thrownError);
            },
            beforeSend: function (xhr) {
                $('#preload-background').show();
                $('.preload__wrap').show();
            },
            success: function (response) {

                var data = JSON.parse(response);

                $('.preload__wrap').hide();
                $('#preload-background').hide();

                var productId = $(context).attr('product-id');
                var totalPrice = parseInt($('.cart-total-price').text().slice(1).replace(/,/g, ''));
                var basketItem = $(".cart-wrapper[product-id="+productId+"]");

                totalPrice -= parseInt(basketItem.text());
                if ($(this).closest('.cart-wrapper-wine').hasClass('active')) {
                    totalPrice -= parseInt($(this).closest('.cart-wrapper').find('.advanced-price-wrap').text());
                }

                totalPrice = number_format(totalPrice, 2);
                $('.cart-total-price').html('$' + totalPrice);
                $('.cart-drop').replaceWith(data.topBasket.content);
                $('#basket-layout').replaceWith(data.mainBasket.content);
                if(data.countBasketElements > 0){
                    jQuery('#quantity-top').html(data.countBasketElements);
                }else{
                    jQuery('#quantity-top').html('');
                }
                var modal_window = $('.modal-backdrop.fade');
                modal_window.removeClass('in');
                modal_window.addClass('out');
                modal_window.hide();

                /** Reinit spinners after remove basket element */
                $(".ui-spinner-wrapper > input").spinner({
                    min: 10,
                    step: 10,
                    incremental: false,
                    spin: function (event, ui) {
                        spinHandler(event, ui);
                    },
                    create: function( event, ui ) {
                        if (event.target.value <= 10) {
                            $(event.target).parent().find('.ui-spinner-down').css("pointer-events", "none");
                        }
                    }
                });

                $(".ui-spinner-taste-wrapper > input").spinner({
                    min: 1,
                    step: 1,
                    incremental: false,
                    spin: function (event, ui) {
                        spinHandler(event, ui);
                    },
                    create: function( event, ui ) {
                        if (event.target.value <= 1) {
                            $(event.target).parent().find('.ui-spinner-down').css("pointer-events", "none");
                        }
                    }
                });
                $('.ui-spinner-up').text('+');
                $('.ui-spinner-down').text('-');
            }
        });



    });

    $('.removeAdvancedProduct').on('click', function (e) {
        e.preventDefault();
        $(this).closest('.cart-wrapper-wine').removeClass('active');
        $(this).closest('.cart-wrapper').find('.advanced-quantity-input').val(0);
        var totalPrice = parseInt($('.cart-total-price').text().slice(1).replace(/,/g, ''));
        totalPrice -= parseInt($(this).closest('.cart-wrapper').find('.advanced-price-wrap').text());
        $(this).closest('.cart-wrapper').find('.advanced-price-wrap').text($(this).data('advanced-price'));
        totalPrice = number_format(totalPrice, 2);
        $('.cart-total-price').html('$' + totalPrice);

        /*$(this).closest('.cart-wrapper').find('.sub-taste').remove();
        $('.add-test').remove();*/
    });
});

$(function () {

    $("select.custom").each(function () {
        var sb = new SelectBox({
            selectbox: $(this),
            height: 150,
            width: '100%'
        });
    });

    $(document).on('click', '.steps-item', function (e) {
        e.preventDefault();
        submitElem = $(this).find('.mybtn.steps--btn');
        if (submitElem.is("button")) {
            submitElem.parents('form').submit();
        }
        else {
            window.location.href = submitElem.attr('href');
        }
    });

});

$(document).on('click', '.steps-search form.s-results-row', function () {
    $(this).submit();
});

$('form[name=login_form]').keypress(function (e) {

    if (e.which == 13 && $('form[name=login_form]').size() > 0) {

        e.stopImmediatePropagation();
        $('form[name=login_form]').submit(function (e) {

            var str = $('form[name=login_form]').serialize();
            $.ajax({
                url: "/login",
                type: "POST",
                dataType: "json",
                data: str,
                success: function (data) {
                    alert(data);
                }
            });
        });

    }
});

function formatAMPM(date) {
    //console.log(date);
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';

    //console.log(hours+" : "+minutes+" : "+ampm);

    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

function showCountBasketItems() {
    var items = $('.flex-container.flex-side.cart-wrapper.cart-wrapper-wine.flex-center'); // array
    var itemQuantity = items.length; // quantity of el

    var vines = 10; // start value
    var countVine = $('#quantity');
    $(countVine).text(itemQuantity); // add quantity elements to ( )

    var arrClass = 'quantity-vines-';
    // console.log(arrClass);
    for (var i = 0; i < itemQuantity; i++) {
        $(items[i]).addClass(arrClass + i);
    }

    $(document).ready(function () {
        for (var j = 0; j < itemQuantity; j++) {
            $('.quantity-vines-' + j + ' .plus').click(function () {
                $(this).prev().text(vines += 10);
            })
            $('.quantity-vines-' + j + ' .minus').click(function () {
                if (vines > 0) {
                    $(this).next().text(vines -= 10);
                }
            })
        }
    });
}

function trim( str, charlist ) {	// Strip whitespace (or other characters) from the beginning and end of a string
    //
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: mdsjack (http://www.mdsjack.bo.it)
    // +   improved by: Alexander Ermolaev (http://snippets.dzone.com/user/AlexanderErmolaev)
    // +	  input by: Erkekjetter
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)

    charlist = !charlist ? ' \s\xA0' : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
    var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
    return str.replace(re, '');
}

function phpDate( format, timestamp ) {
    // Format a local time/date
    //
    // +   original by: Carlos R. L. Rodrigues
    // +	  parts by: Peter-Paul Koch (http://www.quirksmode.org/js/beat.html)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: MeEtc (http://yass.meetcweb.com)
    // +   improved by: Brad Touesnard

    var a, jsdate = new Date(timestamp ? timestamp * 1000 : null);
    var pad = function(n, c){
        if( (n = n + "").length < c ) {
            return new Array(++c - n.length).join("0") + n;
        } else {
            return n;
        }
    };
    var txt_weekdays = ["Sunday","Monday","Tuesday","Wednesday",
        "Thursday","Friday","Saturday"];
    var txt_ordin = {1:"st",2:"nd",3:"rd",21:"st",22:"nd",23:"rd",31:"st"};
    var txt_months =  ["", "January", "February", "March", "April",
        "May", "June", "July", "August", "September", "October", "November",
        "December"];

    var f = {
        // Day
        d: function(){
            return pad(f.j(), 2);
        },
        D: function(){
            t = f.l(); return t.substr(0,3);
        },
        j: function(){
            return jsdate.getDate();
        },
        l: function(){
            return txt_weekdays[f.w()];
        },
        N: function(){
            return f.w() + 1;
        },
        S: function(){
            return txt_ordin[f.j()] ? txt_ordin[f.j()] : 'th';
        },
        w: function(){
            return jsdate.getDay();
        },
        z: function(){
            return (jsdate - new Date(jsdate.getFullYear() + "/1/1")) / 864e5 >> 0;
        },

        // Week
        W: function(){
            var a = f.z(), b = 364 + f.L() - a;
            var nd2, nd = (new Date(jsdate.getFullYear() + "/1/1").getDay() || 7) - 1;

            if(b <= 2 && ((jsdate.getDay() || 7) - 1) <= 2 - b){
                return 1;
            } else{

                if(a <= 2 && nd >= 4 && a >= (6 - nd)){
                    nd2 = new Date(jsdate.getFullYear() - 1 + "/12/31");
                    return date("W", Math.round(nd2.getTime()/1000));
                } else{
                    return (1 + (nd <= 3 ? ((a + nd) / 7) : (a - (7 - nd)) / 7) >> 0);
                }
            }
        },

        // Month
        F: function(){
            return txt_months[f.n()];
        },
        m: function(){
            return pad(f.n(), 2);
        },
        M: function(){
            t = f.F(); return t.substr(0,3);
        },
        n: function(){
            return jsdate.getMonth() + 1;
        },
        t: function(){
            var n;
            if( (n = jsdate.getMonth() + 1) == 2 ){
                return 28 + f.L();
            } else{
                if( n & 1 && n < 8 || !(n & 1) && n > 7 ){
                    return 31;
                } else{
                    return 30;
                }
            }
        },

        // Year
        L: function(){
            var y = f.Y();
            return (!(y & 3) && (y % 1e2 || !(y % 4e2))) ? 1 : 0;
        },
        //o not supported yet
        Y: function(){
            return jsdate.getFullYear();
        },
        y: function(){
            return (jsdate.getFullYear() + "").slice(2);
        },

        // Time
        a: function(){
            return jsdate.getHours() > 11 ? "pm" : "am";
        },
        A: function(){
            return f.a().toUpperCase();
        },
        B: function(){
            // peter paul koch:
            var off = (jsdate.getTimezoneOffset() + 60)*60;
            var theSeconds = (jsdate.getHours() * 3600) +
                (jsdate.getMinutes() * 60) +
                jsdate.getSeconds() + off;
            var beat = Math.floor(theSeconds/86.4);
            if (beat > 1000) beat -= 1000;
            if (beat < 0) beat += 1000;
            if ((String(beat)).length == 1) beat = "00"+beat;
            if ((String(beat)).length == 2) beat = "0"+beat;
            return beat;
        },
        g: function(){
            return jsdate.getHours() % 12 || 12;
        },
        G: function(){
            return jsdate.getHours();
        },
        h: function(){
            return pad(f.g(), 2);
        },
        H: function(){
            return pad(jsdate.getHours(), 2);
        },
        i: function(){
            return pad(jsdate.getMinutes(), 2);
        },
        s: function(){
            return pad(jsdate.getSeconds(), 2);
        },
        //u not supported yet

        // Timezone
        //e not supported yet
        //I not supported yet
        O: function(){
            var t = pad(Math.abs(jsdate.getTimezoneOffset()/60*100), 4);
            if (jsdate.getTimezoneOffset() > 0) t = "-" + t; else t = "+" + t;
            return t;
        },
        P: function(){
            var O = f.O();
            return (O.substr(0, 3) + ":" + O.substr(3, 2));
        },
        //T not supported yet
        //Z not supported yet

        // Full Date/Time
        c: function(){
            return f.Y() + "-" + f.m() + "-" + f.d() + "T" + f.h() + ":" + f.i() + ":" + f.s() + f.P();
        },
        //r not supported yet
        U: function(){
            return Math.round(jsdate.getTime()/1000);
        }
    };

    return format.replace(/[\\]?([a-zA-Z])/g, function(t, s){
        if( t!=s ){
            // escaped
            ret = s;
        } else if( f[s] ){
            // a date function exists
            ret = f[s]();
        } else{
            // nothing special
            ret = s;
        }

        return ret;
    });
}

var stopDoubleClick = true;

$(document).on('click', '.chat-page .user-dialog', function () {

    var handlerAreaObjectContext = $(this);

    if (stopDoubleClick) {

        stopDoubleClick = false;

        try {

            //var previousMessage = $('.chat-message__conversation-create').clone();
            var action = $(this).data('action');
            var user_id = $(this).data('user');
            var data_related_id = $(this).data('related-user');
            var conversation_id = $(this).data('conversation');
            var message_id = $(this).data('message');

            if (typeof Routing != 'undefined') {

                var parameters = {user: user_id, related: data_related_id};
                if (action == 'chat_conversation'){
                    parameters.related = data_related_id;
                    parameters.message = message_id;
                }
                /*var url = Routing.generate('chat_conversation', {
                    user: user_id,
                    message: message_id
                });*/

                var url = Routing.generate(action, parameters);

                if (typeof message_id === 'undefined')
                    $(".user-dialog").removeClass('active');

                $(this).addClass('active');

                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function (xhr) {
                        $('.chat-msg #msgArea > .msg-container').css('height', '100%');
                        $('.msg-container.flex-container').html('<div class="preload"></div>');
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        // $('.msg-container.flex-container').html(previousMessage);
                        var unavailable = '<div class="chat-message__conversation-create"><div class="flash-notice">Sorry, but chat is unavailable. Work underway to restore.</div></div>';
                        $('.msg-container.flex-container').html(unavailable);
                        stopDoubleClick = true;
                    },
                    success: function (response) {

                        var data = JSON.parse(response);
                        var conversation = data['conversation'] || false;

                        var user = data['user'];

                        $("#app_conversation_conversation").val(conversation.id);

                        $("form[name=app_conversation]").attr('action', Routing.generate('chat_message', {conversation: conversation.id}));

                        var content = '';
                        // console.log( typeof conversation.messages !== 'undefined' && conversation.messages.length > 0);
                        if ( typeof conversation.messages !== 'undefined' && conversation.messages.length > 0) {

                            for (var i = 0 in conversation.messages) {

                                var side, avatar, unread = '';

                                if (user.id == conversation.user.id) {
                                    if (conversation.user.id == conversation.messages[i].user.id) {
                                        side = 'your-msg';
                                        avatar = conversation.user.temporality_avatar;
                                    } else {
                                        side = 'not-your-msg';
                                        avatar = conversation.related_user.temporality_avatar;
                                    }
                                } else {
                                    if (conversation.user.id == conversation.messages[i].user.id) {
                                        side = 'not-your-msg';
                                        avatar = conversation.user.temporality_avatar;
                                    } else {
                                        side = 'your-msg';
                                        avatar = conversation.related_user.temporality_avatar;
                                    }
                                }

                                if (conversation.user.id == conversation.related_user.id && conversation.user.id != user.id) {
                                    side = 'not-your-msg';
                                    avatar = conversation.related_user.temporality_avatar;
                                }

                                if (conversation.messages[i].is_read == 0) {
                                    unread = 'chat-conversation__unread-message';
                                }

                                var dateParse = conversation.messages[i].created_at.split(/[^0-9]/);
                                content += '<div class="' + side + '-wrapper flex-wrap" id="coversation_message_' + conversation.messages[i].text + '">\n' +
                                    '                        <div class="' + side + ' msg ' + unread + '">\n' +
                                    '                            <p class="basic-text">' + conversation.messages[i].text + '</p>\n' +
                                    '                        </div><img class="chat-user" src="' + avatar + '" alt="chat-user" title="chat-user">\n' +
                                    '                        <p class="basic-text msg-time">' + formatAMPM(new Date(dateParse[0],dateParse[1]-1,dateParse[2],dateParse[3],dateParse[4],dateParse[5])) + '</p>\n' +
                                    '                    </div>';

                                if (conversation.messages[i].attachment) {
                                    content += '\n' +
                                        '            <div class="flex-container chat-media">\n' +
                                        '              <div class="chat-done"><img src="' + assetsImageDir + '/chat.png" alt="chat" title="chat"><img class="chat-done-img" src="' + assetsImageDir + '/chat-done.png" alt="done" title="done"></div>\n' +
                                        '              <div class="flex-container">\n' +
                                        '                <p class="basic-text chat-file-name">' + conversation.messages[i].attachment + '</p><a class="basic-text chat-link" href="' + assetsBaseDir + 'attachment/' + conversation.messages[i].attachment + '" download="' + assetsBaseDir + 'attachment/' + conversation.messages[i].attachment + '">Download</a>\n' +
                                        '              </div>\n' +
                                        '            </div>';
                                }

                                if (message_id == conversation.messages[i].id)
                                    break;
                            }


                            $('.chat-msg #msgArea > .msg-container').css('height', 'auto');

                        } else {
                            $('.chat-msg #msgArea > .msg-container').css('height', '100%');
                            content = '<div class="chat-message__conversation-create"><div class="flash-notice">' + data['message'] + '</div></div>';
                        }

                        $('.chat-dialogs-tab__content .tab-pane#all .user-dialog').removeClass('active');
                        // $('.chat-dialogs-tab__content .tab-pane#all .user-dialog[data-user="' + user_id + '"]').addClass('active');
                        $('.chat-dialogs-tab__content .tab-pane#all div[class~="user-dialog"][data-user="' + user_id + '"]').addClass('active');

                        $('.msg-container.flex-container').html(content);
                        $('#msgArea').stop().animate({scrollTop: $('.msg-container.flex-container.flex-wrap')[0].scrollHeight}, 800, function () {
                            stopDoubleClick = true;
                        });

                        // update conversation handler area, with new data
                        if (data['created'] || data['updated']) {

                            var dateConversationParse = conversation.created_at.split(/[^0-9]/);
                            var dateConversation = new Date(conversation.created_at).getTime() / 1000;
                            var date = phpDate('M d Y g:i a', dateConversation);

                            var replacedConversationTitle = conversation.related_user.firstname;
                            if (user.id != conversation.user.id) {
                                replacedConversationTitle = conversation.user.firstname;
                            }

                            var replacedAvatar = '/images/updatePhoto.png';
                            if (user.id == conversation.user.id) {
                                replacedAvatar = conversation.related_user.temporality_avatar;
                            } else {
                                replacedAvatar = conversation.user.temporality_avatar;
                            }

                            if (conversation.user.id == conversation.related_user.id) {
                                replacedConversationTitle = 'Administration';
                                replacedAvatar = '/images/updatePhoto.png';
                            }

                            if (user.id == conversation.user.id) {
                                var connector_id = conversation.related_user.id;
                            }else{
                                var connector_id = conversation.user.id;
                            }

                            if (connector_id == conversation.user.id) {
                                var related_connector_id = conversation.related_user.id;
                            }else{
                                var related_connector_id = conversation.user.id;
                            }
                            var handlerArea = '<div class="user-dialog user-dialog flex-container transition" data-user="' + connector_id + '" data-related-user="' + related_connector_id + '" data-action="chat_conversation">\n' +
                                '                                    <div class="user-dialog-photo-wrapper">\n' +
                                '                                       <img class="user-dialog-photo" src="'+ replacedAvatar +'">\n' +
                                '                                                                            </div>\n' +
                                '                                    <div class="user-dialog-text-wrapper flex-container flex-column">\n' +
                                '                                        <div class="flex-container flex-side">\n' +
                                '                                            <div class="user-dialog-name">' + replacedConversationTitle + '</div>\n' +
                                '                                            <div class="user-dialog-time">' + date + '</div>\n' +
                                '                                        </div>\n' +
                                '                                    </div>\n' +
                                '                                </div>';

                            $(handlerAreaObjectContext).replaceWith(handlerArea);
                        }
                    }
                });

            }
        } catch (err) {
            stopDoubleClick = true;
        }


    }
});

$(document).keypress(function (e) {

    // enter pressed
    if (e.which == 13 && $('#type').val() != '' && $('form[name=app_conversation]').size() > 0) {

        e.preventDefault();
        var form = $('form[name=app_conversation]');

        var $data = new FormData();
        $data.append('app_conversation[conversation]', $('#app_conversation_conversation').val());
        $data.append('app_conversation[message]', $('#type').val());
        $data.append('app_conversation[attachmentFile][file]', $("input#app_conversation_attachmentFile_file").prop('files')[0]);

        $.ajax({
            url: $(form).attr('action'),
            data: $data, //$(form).serializeArray()
            type: 'POST',
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function (xhr) {
                $('.chat-message-submit').hide();
                $('.preload-mini').show();
            },
            error: function (xhr, ajaxOptions, thrownError) {
                $('.chat-message__conversation-create > .flash-notice').css('color', 'red');
                $('.preload-mini').hide();
                $('.chat-message-submit').show();
            },
            success: function (response) {

                var data = JSON.parse(response);

                /** Be in progress where obtain constraint violation errors */
                if (data.error) {
                    $('.preload-mini').hide();
                    $('.chat-msg #msgArea > .msg-container');
                    var conversationContainer = $('.chat-msg #msgArea > .msg-container').css('height', 'auto').clone(true, true);

                    $('.chat-msg #msgArea > .msg-container')
                        .css('height', '100%')
                        .html('')
                        .append('<div class="chat-message__conversation-create"><div class="flash-error">' + data.error + '</div></div>');

                    setTimeout(function () {
                        $('.chat-msg #msgArea > .msg-container').replaceWith(conversationContainer);

                        /** Need set height to 100%, if we don't have message in cloned message container */
                        var pattern = new RegExp('chat-message__conversation-create', 'i');
                        if (pattern.test(conversationContainer.html())) {
                            $('.chat-msg #msgArea > .msg-container').css('height', '100%');
                        }

                        $('#msgArea').stop().animate({scrollTop: $('.msg-container.flex-container.flex-wrap')[0].scrollHeight}, 800);
                        delete(conversationContainer);
                        $('.chat-message-submit').show();
                    }, 2500);

                    return false;
                }

                var message = data['message'];
                var conversation = data['message']['conversation'];
                var user = data['user'];

                var side, avatar;

                if (user.id == conversation.user.id) {
                    if (conversation.user.id == message.user.id) {
                        side = 'your-msg';
                        avatar = conversation.user.temporality_avatar;
                    } else {
                        side = 'not-your-msg';
                        avatar = conversation.related_user.temporality_avatar;
                    }
                } else {
                    if (conversation.user.id == message.user.id) {
                        side = 'not-your-msg';
                        avatar = conversation.user.temporality_avatar;
                    } else {
                        side = 'your-msg';
                        avatar = conversation.related_user.temporality_avatar;
                    }
                }

                var content = '<div class="' + side + '-wrapper flex-wrap">\n' +
                    '                        <div class="' + side + ' msg chat-conversation__unread-message">\n' +
                    '                            <p class="basic-text">' + message.text + '</p>\n' +
                    '                        </div><img class="chat-user" src="' + avatar + '" alt="chat-user" title="chat-user">\n' +
                    '                        <p class="basic-text msg-time">' + formatAMPM(new Date(Date.parse(message.created_at))) + '</p>\n' +
                    '                    </div>';

                if (message.attachment) {
                    content += '\n' +
                        '            <div class="flex-container chat-media">\n' +
                        '              <div class="chat-done"><img src="' + assetsImageDir + '/chat.png" alt="chat" title="chat"><img class="chat-done-img" src="' + assetsImageDir + '/chat-done.png" alt="done" title="done"></div>\n' +
                        '              <div class="flex-container">\n' +
                        '                <p class="basic-text chat-file-name">' + message.attachment + '</p><a class="basic-text chat-link" href="' + assetsBaseDir + 'attachment/' + message.attachment + '" download="' + assetsBaseDir + 'attachment/' + message.attachment + '">Download</a>\n' +
                        '              </div>\n' +
                        '            </div>';
                }
                $('.preload-mini').hide();
                $('.chat-message-submit').show();
                $('.chat-message__conversation-create').remove();
                $('.chat-msg #msgArea > .msg-container').css('height', 'auto');
                $('.msg-container.flex-container').append(content);
                $('#type').val('');
                $('#app_conversation_attachmentFile_file').val('');
                $('#msgArea').stop().animate({scrollTop: $('.msg-container.flex-container.flex-wrap')[0].scrollHeight}, 800);

                if (document.getElementById('file-indicator').className != ''){
                    document.getElementById('file-indicator').className = '';
                    openFileUploadDialog = true;
                }
            }
        });

    }

});


/** Change attachment indicator*/
var openFileUploadDialog = true;
var attachment_input = document.getElementById('app_conversation_attachmentFile_file');
if (attachment_input){
    attachment_input.onclick = function (e) {
        if (openFileUploadDialog){
            this.value = null;
        }else{
            document.getElementById('file-indicator').className = '';
            openFileUploadDialog = true;
            e.preventDefault();
        }
    };
    attachment_input.onchange = function () {
        document.getElementById('file-indicator').className = 'file-indicator-class';
        openFileUploadDialog = false;
    };
}