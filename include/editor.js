$(document).ready(function(){
	CKEDITOR.replace( 'editor',
	{
		skin : 'office2003',
                extraPlugins : 'tableresize',
                language : 'ru',
                width : 900,
                height : 400,
		filebrowserBrowseUrl : '../inc/filemanager/index.html'
	});
});



