$(document).ready(function(){
	CKEDITOR.replace( 'editor',
	{
		skin : 'office2003',
                extraPlugins : 'tableresize',
                language : 'ru',
                width : 900,
                height : 200,
		filebrowserBrowseUrl : '../include/filemanager/index.html'
	});
});



