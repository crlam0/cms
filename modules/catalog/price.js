$(document).ready(function () {
    $('body').on('click', 'a.buy_button', function () {
        var id = $(this).attr("item_id");
        var cnt_id = ".cnt_" + id;
        cnt = $(cnt_id).val();
        $.ajax({
            type: "GET", url: DIR + "basket/add-buy", dataType: "json", data: "item_id=" + id + "&cnt=" + cnt,
            success: function (msg) {
                if (msg.result !== 'OK') {
                    alert(msg.result);
                } else {
                    $('#popupHeader').html('Сейчас в корзине :');
                    $('#popupContent').load(DIR + "basket/get-summary");
                    $('.basket-button').css('display', 'inline-block');
                    $('.basket-count').html(msg.count);
                    loadPopup();
                    centerPopup();
                }
            }
        });
    });
});

var aside = document.querySelector('#price_parts'),
    HTMLtop = document.documentElement.getBoundingClientRect().top,
    t0 = aside.getBoundingClientRect().top - HTMLtop;
window.onscroll = function () {
    aside.className = (t0 < window.pageYOffset ? 'sticky' : '');
};
