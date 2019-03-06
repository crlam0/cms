$(document).ready(function(){

	editAreaLoader.init({
		id: "editor_html"
		,start_highlight: true
		,allow_resize: "both"
		,allow_toggle: true
		,language: "ru"
		,syntax: "html"	
		,toolbar: "search, go_to_line, |, undo, redo, |, syntax_selection , |, change_smooth_selection, highlight, reset_highlight, |, help"
		,syntax_selection_allow: "css,html,js,php"
	});

});



