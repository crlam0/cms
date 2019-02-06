
function loadPopup() {
    $('#myModal').modal();
}

function disablePopup() {
}

function centerPopup() {
}

$("#myModal").on("show.bs.modal", function() {
    var width = $(window).width() - 40;
    $(this).find(".modal-body").css("max-width", width);
    var height = $(window).height() - 100;
    $(this).find(".modal-body").css("max-height", height);
});

