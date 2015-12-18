navHover = function() {
	var lis = document.getElementById("mainmenu").getElementsByTagName("LI");
	for (var i=0; i<lis.length; i++) {
		lis[i].onmouseover=function() {
			this.className+=" iehover";
			}
			lis[i].onmouseout=function() {
			this.className=this.className.replace(new RegExp(" iehover\\b"), "");
		}
	}
}
if (window.attachEvent) window.attachEvent("onload", navHover);


function test() {
	if (confirm("Вы уверены ?")){
		return true;
	}else{
		return false;
	}
}

