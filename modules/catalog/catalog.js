
$(document).ready(function () {
    
    var MYDIR = DIR + 'modules/catalog/';

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
    
    $("img.cat_item_image_popup").click(function () {
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: MYDIR + "load-image", dataType: "json", data: "get_popup_image_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight=" + windowHeight,
            success: function (msg) {
                $('#popupHeader').html(msg.title);
                $('#popupContent').html(msg.content);
                $("#popupContent").waitForImages(function () {
                    loadPopup();
                    centerPopup();
                });
            },
            error: function (jqXHR, error, errorThrown) {
                $('#popupContent').html(jqXHR.responseText);
                loadPopup();
                // $('.modal-dialog').css({'max-width': '1024px'});
                centerPopup();
            }

        });
    });

    $('body').on('click', 'img.cat_images', function () {
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: MYDIR + "load-image", dataType: "json", data: "get_popup_image_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight=" + windowHeight,
            success: function (msg) {
                $('#popupHeader').html(msg.title);
                $('#popupContent').html(msg.content);
                $("#popupContent").waitForImages(function () {
                    loadPopup();
                    centerPopup();
                });
            },
            error: function (jqXHR, error, errorThrown) {
                $('#popupContent').html(jqXHR.responseText);
                loadPopup();
                // $('.modal-dialog').css({'max-width': '1024px'});
                centerPopup();
            }
        });
    });

    $('body').on('click', 'a.cat_image_button', function () {
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $(".modal-dialog").fadeOut("slow", function () {
            $.ajax({
                type: "GET", url: MYDIR + "load-image", dataType: "json", data: "get_popup_image_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight=" + windowHeight,
                success: function (msg) {
                    $('#popupHeader').html(msg.title);
                    $('#popupContent').html(msg.content);
                    $("#popupContent").waitForImages(function () {
                        $(".modal-dialog").fadeIn("slow");
                        centerPopup();
                    });
                },
                error: function (jqXHR, error, errorThrown) {
                    $('#popupContent').html(jqXHR.responseText);
                    $(".modal-dialog").fadeIn("slow");
                    // $('.modal-dialog').css({'max-width': '1024px'});
                    centerPopup();
                }
            });
        });
    });

});

