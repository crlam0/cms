$(document).ready(function(){
    CKEDITOR.replace( 'editor', {
        language : 'ru',
        width : 900,
        height : 200,
        skin: 'moono-lisa',
        extraPlugins: 'justify,font,colordialog,colorbutton',
        removePlugins: 'about',
        removeButtons: 'Subscript,Superscript,Styles,SpecialChar'
    });
});



