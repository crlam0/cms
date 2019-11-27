$(document).ready(function() {
    $('body').on('click', 'a.buy_button', function () {
        var id = $(this).attr("item_id");
        var cnt_id='.cnt_' + id;
        cnt=$(cnt_id).val();
        $.ajax({
            type: "GET", url: DIR + "modules/price/index.php", data: "add_buy=1&item_id="+id+"&cnt="+cnt,
            success: function(msg){
                if(msg !== 'OK') alert(msg);
                $('#popupContent').load(DIR + "modules/catalog/buy.php?get_summary=1");
                loadPopup();                
                centerPopup();
            }
        });
    });
});

var aside = document.querySelector('#price_parts'),
    HTMLtop = document.documentElement.getBoundingClientRect().top,
    t0 = aside.getBoundingClientRect().top - HTMLtop;
window.onscroll = function() {
    aside.className = (t0 < window.pageYOffset ? 'sticky' : '');
};
