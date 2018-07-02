$(document).ready(function () {
    $("img.gallery_popup").click(function () {
        var id = $(this).attr("item_id");
        var clientHeight = document.documentElement.clientHeight;
        alert('0');
        $.ajax({
            type: "GET", url: "index.php", data: "load=1&id=" + id + "&clientHeight="+clientHeight,
            success: function(msg){
                alert('1');
                if(msg !== 'OK') alert(msg);
                $('#popupContent').html('Test');
                loadPopup();                
                centerPopup();
            }
        });
        
    });

    $("a.gallery_button").live('click', function () {
        var id = $(this).attr("item_id");
        $("#popupItem").fadeOut("slow",function(){
            var clientHeight = document.documentElement.clientHeight;
            $('#popupContent').load("index.php?load=1&id=" + id + "&clientHeight="+clientHeight, function () {
                $("#popupContent").waitForImages(function () {
                    $("#popupItem").fadeIn("slow");
                    centerPopup();
                });
            });
        });    
    });
});

