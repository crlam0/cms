
$(document).ready(function() {

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

    $("img.cat_images").live('click',function() {        
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
        $("#popupContent").fadeOut("fast");
        var height = $("#popupContent").height();
        height=height + 'px';
        $("#popupContent").css({
            "height": height
        });
        $('#popupContent').load("index.php?get_popup_content=1&file_name=" + file_name + "&image_id=" + image_id + '&item_id=' + item_id + "&windowHeight="+windowHeight,function(){
            $("#popupContent").waitForImages(function() {
                centerPopup();
                $("#popupContent").fadeIn("fast");
            });
        });
    });  

    $("a.cat_item_button").click(function() {
        var item_id = $(this).attr("item_id");
        $("#popupContent").css({
            "width": "300px"
        });
        $("#popupItem").css({
            "width": "320px"
        });
//        $("#popupContent").css({
//            "height": "500px"
//        });
        $('#popupContent').load('catalog/request.php?get_form_content=1&item_id=' + item_id ,function(){
            loadPopup();                    
            centerPopup();            
        });
    });
    
    $("#request_form_submit").live('click',function(){  
        var item_id = $('#cat_request_form_item_id').attr('value');
        var message = $('#cat_request_form_message').attr('value');message=encodeURIComponent(message);
        var firstname = $('#cat_request_form_firstname').attr('value');firstname=encodeURIComponent(firstname);
        var email = $('#cat_request_form_email').attr('value');email=encodeURIComponent(email);
        var phone = $('#cat_request_form_phone').attr('value');phone=encodeURIComponent(phone);
        // alert(firstname);
        // $('#popupContent').html("");
        $('#popupContent').load('catalog/request.php?send_form=1&item_id=' + item_id + '&message=' + message + '&firstname=' + firstname + '&email=' + email + '&phone=' + phone,function(){
            centerPopup();            
        });
    });  

});

