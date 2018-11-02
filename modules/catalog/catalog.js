
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
    DIR = DIR + 'modules/catalog/';

    $("a.buy_button").live('click', function() {
        var id = $(this).attr("item_id");
        var cnt_id=".cnt_"+id;
        cnt=$(cnt_id).attr("value");
        $.ajax({
            type: "GET", url: DIR + "index.php", data: "add_buy=1&item_id="+id+"&cnt="+cnt,
            success: function(msg){
                if(msg !== 'OK') alert(msg);
                $('#popupHeader').html('Сейчас в корзине :');
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
            type: "GET", url: DIR + "index.php", dataType : "json", data: "get_popup_image_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,
            success: function(msg){
                $('#popupHeader').html(msg.title);
                $('#popupContent').html(msg.content);
                $("#popupContent").waitForImages(function() {
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

    $("img.cat_images").live('click',function() {        
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
        $.ajax({
            type: "GET", url: DIR + "index.php", dataType : "json", data: "get_popup_image_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,
            success: function(msg){
                $('#popupHeader').html(msg.title);
                $('#popupContent').html(msg.content);
                $("#popupContent").waitForImages(function() {
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

    $("a.cat_image_button").live('click',function(){ 
        var file_name = $(this).attr("file_name");
        var item_id = $(this).attr("item_id");
        var image_id = $(this).attr("image_id");
        var windowHeight = document.documentElement.clientHeight;
            $.ajax({
                type: "GET", url: DIR + "index.php", dataType : "json", data: "get_popup_image_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,
                success: function(msg){
                $('#popupHeader').html(msg.title);
                $('#popupContent').html(msg.content);
                    $("#popupContent").waitForImages(function() {
                        centerPopup();
                        $("#popupItem").fadeIn("slow");
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

});

