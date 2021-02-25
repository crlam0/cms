if (typeof window.DIR == "undefined") {
    var pathArray = window.location.pathname.split('/');
    var domain = pathArray[1];
    if (domain.match(/^[\w-.]+\.\w{1,5}$/)) {
        window.DIR = '/' + domain + '/';
    } else {
        window.DIR = '/';
    }
}

var filebrowserBrowseUrl = window.DIR + 'include/filemanager/index.html';
var smileyUrl = window.DIR + 'theme/smiley/';

$(document).ready(function () {
    CKEDITOR.replace('editor', {
        skin: 'moono',
        language: 'ru',
        width: 900,
        height: 400,
        extraPlugins: 'justify,font,colordialog,colorbutton,tableresize,smiley',
        removePlugins: 'about',
        removeButtons: 'Subscript,Superscript,Styles,SpecialChar',
        filebrowserBrowseUrl: filebrowserBrowseUrl,
        smiley_path: smileyUrl,
        smiley_images: [
            'whatchutalkingabout_smile.png','angry_smile.png','angel_smile.png','shades_smile.png',
            'devil_smile.png','cry_smile.png','lightbulb.png','thumbs_down.png','thumbs_up.png','heart.png',
            'broken_heart.png','kiss.png','envelope.png'
        ]
//		contentsCss : 'body{background-color:#333333;}'
    });
});

