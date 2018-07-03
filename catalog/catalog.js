
$(document).ready(function() {
    var pathArray = window.location.pathname.split( '/' );
    for (i = 0; i < pathArray.length; i++) {
        console.log(pathArray[i]);
    }
    var domain = pathArray[1];
    if(domain.match(/^[\w-.]+\.\w{1,5}$/)) {
        var DIR = '/' + domain + '/';        
    } else {
        var DIR = '/';
    }
    DIR = DIR + 'catalog/';

    $("a.buy_button").on('click', function() {
        var id = $(this).attr("item_id");
        var cnt_id=".cnt_"+id;
        cnt=$(cnt_id).attr("value");
        $.ajax({
            type: "GET", url: DIR + "index.php", data: "add_buy=1&item_id="+id+"&cnt="+cnt,
            success: function(msg){
                if(msg !== 'OK') alert(msg);
                $('#popupContent').load(DIR + "buy.php?get_summary=1");
                loadPopup();                
                centerPopup();
            }
        });
    });

    $("img.cat_item_image_popup").click(function() {
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: DIR + "index.php", data: "get_popup_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,
            success: function(msg){
                $('#popupContent').html(msg);
                $("#popupContent").waitForImages(function() {
                    loadPopup();                
                    centerPopup();
                });
            }
        });
    });

    $("img.cat_images").live('click',function() {        
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: DIR + "index.php", data: "get_popup_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,
            success: function(msg){
                $('#popupContent').html(msg);
                $("#popupContent").waitForImages(function() {
                    loadPopup();                
                    centerPopup();
                });
            }
        });
    });

    $("a.cat_image_button").live('click',function(){ 
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $("#popupItem").fadeOut("slow",function(){
            $.ajax({
                type: "GET", url: DIR + "index.php", data: "get_popup_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,
                success: function(msg){
                    $('#popupContent').html(msg);
                    $("#popupContent").waitForImages(function() {
                        centerPopup();
                        $("#popupItem").fadeIn("slow");
                    });
                }
            });
        
        });    
    });

});

