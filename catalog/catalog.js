
$(document).ready(function() {

    $("a.buy_button").on('click', function() {
        var id = $(this).attr("item_id");
        var cnt_id=".cnt_"+id;
        cnt=$(cnt_id).attr("value");
        $.ajax({
            type: "GET", url: "index.php", data: "add_buy=1&item_id="+id+"&cnt="+cnt,
            success: function(msg){
                if(msg !== 'OK') alert(msg);
                $('#popupContent').load("buy.php?get_summary=1");
                centerPopup();
                loadPopup();                
            }
        });
    });

    $("img.cat_item_image_popup").click(function() {
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $('#popupContent').load("index.php?get_popup_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,function(){
            loadPopup();                    
            $("#popupContent").waitForImages(function() {
                centerPopup();
            });
        });
    });

    $("img.cat_images").on('click',function() {        
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $('#popupContent').load("index.php?get_popup_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,function(){
            loadPopup();                    
            $("#popupContent").waitForImages(function() {
                centerPopup();
            });
        });
    });

    $("a.cat_image_button").live('click',function(){ 
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $("#popupItem").fadeOut("slow",function(){
//        var height = $("#popupContent").height();
//        height=height + 'px';
//        $("#popupContent").css({
//            "height": height
//        });
	        $('#popupContent').load("index.php?get_popup_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,function(){
                $("#popupContent").waitForImages(function () {
                    centerPopup();
                    $("#popupItem").fadeIn("slow");
                });
            });
        });    
    });

});

