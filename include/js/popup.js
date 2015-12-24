
var popupStatus = 0;

function loadPopup() {
    centerPopup();
    if (popupStatus == 0) {
        $("#backgroundPopup").css({
            "opacity": "0.7"
        });
        $("#backgroundPopup").fadeIn("fast");
        $("#popupItem").fadeIn("fast");
        popupStatus = 1;
    }
}

function disablePopup() {
    if (popupStatus == 1) {
        $("#backgroundPopup").fadeOut("fast");
        $("#popupItem").fadeOut("fast");
        popupStatus = 0;
    }
}

function centerPopup() {

    var scrolledX, scrolledY;
    if (self.pageYOffset) {
        scrolledX = self.pageXOffset;
        scrolledY = self.pageYOffset;
    } else if (document.documentElement && document.documentElement.scrollTop) {
        scrolledX = document.documentElement.scrollLeft;
        scrolledY = document.documentElement.scrollTop;
    } else if (document.body) {
        scrolledX = document.body.scrollLeft;
        scrolledY = document.body.scrollTop;
    }

    var windowHeight = document.documentElement.clientHeight;
    var windowWidth = document.documentElement.clientWidth;

    var popupHeight = $("#popupItem").height();
    var popupWidth = $("#popupItem").width();
    if (popupHeight < 200) {
        popupHeight = 600;
    }
    if( popupWidth > windowWidth ){
        PopupWidth = windowWidth + 'px';
        $("#popupItem").css({
            "width": PopupWidth
        });   
    }
//	alert('WH: '+windowHeight+' PPH: '+popupHeight);

    $("#popupItem").css({
        "position": "absolute",
	"top": scrolledY + (windowHeight/2-popupHeight/2),  
//        "top": (windowHeight / 2 - popupHeight / 2),
        "left": scrolledX + (windowWidth / 2 - popupWidth / 2)
    });
}

$(document).ready(function () {

    //Click the x event!  
    $("#popupItemClose").click(function () {
        disablePopup();
    });
    //Click out event!  
    $("#backgroundPopup").click(function () {
        disablePopup();
    });
    //Press Escape event!  
    $(document).keypress(function (e) {
        if (e.keyCode == 27 && popupStatus == 1) {
            disablePopup();
        }
    });

});

