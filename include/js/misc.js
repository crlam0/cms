function test() {
	if (confirm("Вы уверены ?")){
		return true;
	}else{
		return false;
	}
}

if(jQuery && !jQuery.fn.live) {
    jQuery.fn.live = function(evt, func) {
        $('body').on(evt, this.selector, func);
    };
}
