function test() {
	if (confirm("Вы уверены ?")){
		return true;
	}else{
		return false;
	}
}

$(function() {
  $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
});


if(jQuery && !jQuery.fn.live) {
    jQuery.fn.live = function(evt, func) {
        $('body').on(evt, this.selector, func);
    };
}

/*
(function($) {

    $.fn.print_r = $.fn.print = function(variable){
        return this.each(function(){
        if(typeof variable == 'object'){
            var string = $.print_r.objectToString(variable,0);
            $(this).html(string);
        } else {
            $(this).html('<pre>'+variable.toString()+'</pre>');
        }
    });

    }

    $.print_r = {
            objectToString : function (variable,i){
              var string = '';
              if(typeof variable == 'object' && i < 3){ // 3 is to prevent endless recursion, set higher for more depth
                  string += 'Object ( <ul style="list-style:none;">';
                  var key;
                  for(key in variable) {
                      if (variable.hasOwnProperty(key)) {
                        string += '<li>['+key+'] => ';
                        string += $.print_r.objectToString(variable[key],i+1);
                        string += '</li>';
                      }
                  }
                  string += '</ul> )';
              } else {
                  string = variable.toString();
              }
              return string;
        }
    }

})(jQuery)


  */