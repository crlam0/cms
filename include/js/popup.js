
function loadPopup() {
    $('#myModal').modal();
}

function centerPopup() {
}

function disablePopup() {
}

if (typeof window.loadPopup == "undefined") {
    window.loadPopup = loadPopup;
};

if (typeof window.centerPopup == "undefined") {
    window.centerPopup = centerPopup;
};

if (typeof window.loadPopup == "undefined") {
    window.disablePopup = disablePopup;
};

$("#myModal").on("show.bs.modal", function() {
    var width = $(window).width() - 40;
    $(this).find(".modal-body").css("max-width", width);
    var height = $(window).height() - 100;
    $(this).find(".modal-body").css("max-height", height);
});

