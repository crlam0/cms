$(document).ready(function () {
    $("img.gallery_popup").click(function () {
        var id = $(this).attr("item_id");
        var windowHeight = document.documentElement.clientHeight;
        $('#popupContent').load("index.php?load=1&id=" + id + "&windowHeight="+windowHeight, function () {
            loadPopup();
            $("#popupContent").waitForImages(function () {
                centerPopup();
            });
        });
    });

    $("a.gallery_button").live('click', function () {
        var id = $(this).attr("item_id");
        var windowHeight = document.documentElement.clientHeight;
        $('#popupContent').load("index.php?load=1&id=" + id + "&windowHeight="+windowHeight, function () {
            $("#popupContent").waitForImages(function () {
                centerPopup();
            });
        });
    });
});

