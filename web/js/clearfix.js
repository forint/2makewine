$(function () {
    $('.wizard').removeClass('clearfix');

    $('.modal').on('hidden.bs.modal', function (e) {
        $('#scroll-blocks').css('padding-right', 0)
    })
});