
$(document).ready(function() {

    $("img.cat_item_image_popup").click(function() {
        var id = $(this).attr("item_id");
        $('#popupContent').load("index.php?get_popup_content=1&item_id=" + id,function(){
            $("#popupContent").waitForImages(function() {
                loadPopup();                    
                centerPopup();
            });
        });
    });

});

