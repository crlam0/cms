
var popupStatus = 0;

function loadPopup() {
    $('#myModal').modal();
}

function disablePopup() {
}

function centerPopup() {
}

$('#myModal').on('show.bs.modal', function () {
    $(this).find('.modal-body').css({
        'max-width': '100%',
        'max-height':'100%'
    });
});

