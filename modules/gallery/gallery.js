$(document).ready(function () {
    
    var MYDIR = DIR + 'modules/gallery/';
    
    $('body').on('click', 'img.gallery_popup' , function () {
        var id = $(this).attr("item_id");
        var clientHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: MYDIR + "index.php", dataType : "json", data: "load=1&id=" + id + "&clientHeight=" + clientHeight,
            success: function (msg) {
                $('#popupHeader').html(msg.title);
                $('#popupContent').html(msg.content);
                $("#popupContent").waitForImages(function () {
                    loadPopup();
                    $('#myModal').removeClass('modal-fs');
                    $('#myModal').addClass('modal-fs');
                    centerPopup();
                });
            },
            error: function (jqXHR, error, errorThrown) {                
                $('#popupContent').html(jqXHR.responseText);
                loadPopup();
                centerPopup();
            }
        });
    });

    $('body').on('click', 'a.gallery_button' , function () {
        var id = $(this).attr("item_id");
        $(".modal-dialog").fadeOut("slow", function () {
            var clientHeight = document.documentElement.clientHeight;
            $.ajax({
                type: "GET", url: MYDIR + "index.php", dataType : "json", data: "load=1&id=" + id + "&clientHeight=" + clientHeight,
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

