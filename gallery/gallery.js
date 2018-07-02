$(document).ready(function () {
    $("img.gallery_popup").click(function () {
        var id = $(this).attr("item_id");
        var clientHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: "index.php", data: "load=1&id=" + id + "&clientHeight=" + clientHeight,
            success: function (msg) {
                $('#popupContent').html(msg);
                $("#popupContent").waitForImages(function () {
                    loadPopup();
                    centerPopup();
                });
            },
            error: function (jqXHR, error, errorThrown) {
                alert(jqXHR.responseText);
                alert(error);
                alert(errorThrown);
            }
        });

    });

    $("a.gallery_button").live('click', function () {
        var id = $(this).attr("item_id");
        $("#popupItem").fadeOut("slow", function () {
            var clientHeight = document.documentElement.clientHeight;
            $('#popupContent').load("index.php?load=1&id=" + id + "&clientHeight=" + clientHeight, function () {
                $("#popupContent").waitForImages(function () {
                    $("#popupItem").fadeIn("slow");
                    centerPopup();
                });
            });
        });
    });
});

