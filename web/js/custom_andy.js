$(window).load(function() {
    // $(".steps-item__figure").equalHeights();
    setTimeout(function() {
        $(".steps-main-wrapper").removeClass("is-loaded");
    }, 300);
});
$(document).ready(function () {

    searchTimer = 0;
    function searchWine (input){
        searchbar = input;
        query = input.val();
        notFoundMsg = input.data('nf');
        if (query.length >= 3) {
            url = searchbar.data('ajaxsearch');
            $.ajax({
                url: url,
                dataType: 'json',
                data: {
                    'name': query
                },
                async: false,
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
                }
                else {
                    results = '<div class="s-results-row s-results-row__desc">' + notFoundMsg + '</div>';
                }
                $('.s-results__body').html(results);
            });
        }
        else {
            $('.s-results__body').addClass('hidden');
            $('.s-results__title').addClass('hidden');
        }
    }
    $(document).on('keyup', '.steps-search .drop-form__input', function(e){
        if (searchTimer) {
            clearTimeout(searchTimer);
        }
        searchTimer = setTimeout(searchWine, 1000, $(this));
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

    $(document).on('click', '.back-trigger--winecard', function(e){
        e.preventDefault();
        $('form#back_vineyards').submit();
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
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

var stopDoubleClick = true;

$(".user-dialog").click(function () {

    if (stopDoubleClick) {

        stopDoubleClick = false;

        try {

            //var previousMessage = $('.chat-message__conversation-create').clone();
            var user_id = $(this).data('user');
            var conversation_id = $(this).data('conversation');
            var message_id = $(this).data('message');

            if (typeof Routing != 'undefined'){

                var url = Routing.generate('chat_conversation', {
                    user: user_id,
                    message: message_id
                });

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
                        var conversation = data['conversation'];

                        var user = data['user'];

                        $("#app_conversation_conversation").val(conversation.id);
                        $("form[name=app_conversation]").attr('action', Routing.generate('chat_message', {conversation: conversation.id}));

                        var content = '';

                        if (typeof conversation.messages !== 'undefined' && conversation.messages.length > 0) {

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

                                if (conversation.user.id == conversation.related_user.id && conversation.user.id != user.id){
                                    side = 'not-your-msg';
                                    avatar = conversation.related_user.temporality_avatar;
                                }


                                if (conversation.messages[i].is_read == 0) {
                                    unread = 'chat-conversation__unread-message';
                                }

                                content += '<div class="' + side + '-wrapper flex-wrap" id="coversation_message_' + conversation.messages[i].text + '">\n' +
                                    '                        <div class="' + side + ' msg ' + unread + '">\n' +
                                    '                            <p class="basic-text">' + conversation.messages[i].text + '</p>\n' +
                                    '                        </div><img class="chat-user" src="' + avatar  + '" alt="chat-user" title="chat-user">\n' +
                                    '                        <p class="basic-text msg-time">' + formatAMPM(new Date(Date.parse(conversation.messages[i].created_at))) + '</p>\n' +
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
                    }
                });

            }
        } catch (err) {
            console.log(err);
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
                if (data.error){
                    $('.preload-mini').hide();
                    $('.chat-msg #msgArea > .msg-container');
                    var conversationContainer = $('.chat-msg #msgArea > .msg-container').css('height', 'auto').clone(true,true);

                    $('.chat-msg #msgArea > .msg-container')
                        .css('height', '100%')
                        .html('')
                        .append('<div class="chat-message__conversation-create"><div class="flash-error">'+data.error+'</div></div>');

                    setTimeout(function(){
                        $('.chat-msg #msgArea > .msg-container').replaceWith(conversationContainer);
                        $('#msgArea').stop().animate({scrollTop: $('.msg-container.flex-container.flex-wrap')[0].scrollHeight}, 800);
                        delete(conversationContainer);
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
                    '                        <div class="' + side + ' msg">\n' +
                    '                            <p class="basic-text">' + message.text + '</p>\n' +
                    '                        </div><img class="chat-user" src="' + avatar  + '" alt="chat-user" title="chat-user">\n' +
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

            }
        });

    }

});






