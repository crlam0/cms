$(document).ready(function () {
    
    var MYDIR = DIR + 'modules/blog/';

    $('body').on('click', 'a.score_button', function () {
        var id = $(this).attr("post_id");
        var score_value = '.score_value_' + id;
        $.ajax({
            type: "GET", url: MYDIR + "add-score", dataType: "json", data: "post_id=" + id,
            success: function (msg) {
                if (msg.result !== 'OK') {
                    alert(msg.result);
                } else {
                    $(score_value).html(msg.score);
                }                
            },
            error: function (jqXHR, error, errorThrown) {
                $(score_value).html(jqXHR.responseText);
            }
            
        });
        return false;
    });
});

