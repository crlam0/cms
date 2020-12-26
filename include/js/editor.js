if (typeof window.DIR == "undefined") {
    var pathArray = window.location.pathname.split( '/' );
    var domain = pathArray[1];
    if(domain.match(/^[\w-.]+\.\w{1,5}$/)) {
        window.DIR = '/' + domain + '/';
    } else {
        window.DIR = '/';
    }
    // DIR = window.DIR;
};    

var filebrowserBrowseUrl = window.DIR + 'include/filemanager/index.html';

$(document).ready(function(){
	CKEDITOR.replace( 'editor',
	{
		skin : 'office2003',
                extraPlugins : 'tableresize',
                language : 'ru',
                width : 900,
                height : 400,
		filebrowserBrowseUrl : filebrowserBrowseUrl,
//		contentsCss : 'body{background-color:#333333;}'
	});
});

