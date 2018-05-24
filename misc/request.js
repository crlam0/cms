$(document).ready(function() {
    
    $(document).on("click","#request-button", function () {
        var firstname = $('#request-name').val();firstname=encodeURIComponent(firstname);
        var phone = $('#request-phone').val();phone=encodeURIComponent(phone);
        var comment = $('#request-comment').val();comment=encodeURIComponent(comment);
        var code = $('#request-code').val();code=encodeURIComponent(code);
        var type_id=$( "#request-type option:selected" ).val();
        
	$.ajax({
	   type: "POST", url: "misc/request.php", data: 'request=top&firstname=' + firstname + '&type_id=' + type_id + '&phone=' + phone + '&comment=' + comment + '&code=' + code,
	   success: function(msg){
               if(msg === 'ok'){
                   $('#request-result').html('<p class="alert alert-success">Заявка успешно отправлена !</p>');
                   $('#request-name').val('');
                   $('#request-phone').val('');
                   $('#request-comment').val('');
                   $('#request-code').val('');
               } else {
                   $('#request-result').html('<p class="alert alert-warning">' + msg + '</p>');
               }    
	   }
	});
        
    });  

    $(document).on("click","#callback-button", function () {
        var firstname = $('#callback-name').val();firstname=encodeURIComponent(firstname);
        var phone = $('#callback-phone').val();phone=encodeURIComponent(phone);
        var code = $('#callback-code').val();code=encodeURIComponent(code);
        
	$.ajax({
	   type: "POST", url: "misc/request.php", data: 'request=call&firstname=' + firstname + '&phone=' + phone+ '&code=' + code,
	   success: function(msg){
               if(msg === 'ok'){
                   $('#callback-result').html('<p class="alert alert-success">Заявка успешно отправлена !</p>');
                   $('#callback-name').val('');
                   $('#callback-phone').val('');
                   $('#callback-code').val('');
               } else {
                   $('#callback-result').html('<p class="alert alert-warning">' + msg + '</p>');
               }    
	   }
	});
        
    });  


    function test_result(){
        var content = $('#request-form-result').html();
        if(content === 'ok') {
            $('#request-form-name').val('');
            $('#request-form-email').val('');
            $('#request-form-phone').val('');
            $('#request-form-comment').val('');
            $('#request-form-code').val('');
            $('#request-form-file').val('');
            $('#request-form-result').html('<p class="alert alert-success">Заявка успешно отправлена !</p>');
        }
    }

    $('#request-form').submit(function(){
        var options = { 
            target: '#request-form-result',
            success: test_result
        }; 
        $(this).ajaxSubmit(options);
        return false;
        
    });  

});

