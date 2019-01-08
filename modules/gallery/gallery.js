$(document).ready(function () {
    var pathArray = window.location.pathname.split( '/' );
    var domain = pathArray[1];
    if(domain.match(/^[\w-.]+\.\w{1,5}$/)) {
        var DIR = '/' + domain + '/';
    } else {
        var DIR = '/';
    }
    DIR = DIR + 'modules/gallery/';
    
    $("img.gallery_popup").click(function () {
        var id = $(this).attr("item_id");
        var clientHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: DIR + "index.php", dataType : "json", data: "load=1&id=" + id + "&clientHeight=" + clientHeight,
            success: function (msg) {
                $('#popupHeader').html(msg.title);
                $('#popupContent').html(msg.content);
                $("#popupContent").waitForImages(function () {
                    loadPopup();
                    $('.modal').removeClass('modal-fs');
                    $('.modal').addClass('modal-fs');
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

//    $("a.gallery_button").live('click', function () {
    $('body').on('click', 'a.gallery_button' , function () {
        var id = $(this).attr("item_id");
        $(".modal-dialog").fadeOut("slow", function () {
            var clientHeight = document.documentElement.clientHeight;
            $.ajax({
                type: "GET", url: DIR + "index.php", dataType : "json", data: "load=1&id=" + id + "&clientHeight=" + clientHeight,
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
                    centerPopup();
                }
            });
        });
    });
});

