if (typeof window.DIR == "undefined") {
    var pathArray = window.location.pathname.split( '/' );
    var domain = pathArray[1];
    if(domain.match(/^[\w-.]+\.\w{1,6}$/)) {
        window.DIR = '/' + domain + '/';
    } else {
        window.DIR = '/';
    }
};    

function test() {
    if (confirm("Вы уверены ?")) {
        return true;
    } else {
        return false;
    }
}

if(jQuery && !jQuery.fn.live) {
    jQuery.fn.live = function(evt, func) {
        $('body').on(evt, this.selector, func);
    };
}

