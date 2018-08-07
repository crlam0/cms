$(document).ready(function () {
    var pathArray = window.location.pathname.split( '/' );
    var domain = pathArray[1];
    if(domain.match(/^[\w-.]+\.\w{1,5}$/)) {
        var DIR = '/' + domain + '/';
    } else {
        var DIR = '/';
    }
    DIR = DIR + 'gallery/';
    
    $("img.gallery_popup").click(function () {
        var id = $(this).attr("item_id");
        var clientHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: DIR + "index.php", data: "load=1&id=" + id + "&clientHeight=" + clientHeight,
            success: function (msg) {
                $('#popupContent').html(msg);
                $("#popupContent").waitForImages(function () {
                    loadPopup();
                    // $('.modal-dialog').css({'max-width': '1024px'});
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

    $("a.gallery_button").live('click', function () {
        var id = $(this).attr("item_id");
        $(".modal-dialog").fadeOut("slow", function () {
            var clientHeight = document.documentElement.clientHeight;
            $.ajax({
                type: "GET", url: DIR + "index.php", data: "load=1&id=" + id + "&clientHeight=" + clientHeight,
                success: function (msg) {
                    $('#popupContent').html(msg);
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

