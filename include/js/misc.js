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
