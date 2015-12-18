<html>
<head>
<title>[ [%settings(site_title)%] : Адм. раздел ] [%Header%]</title>
<META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
<meta name="keywords" content="[%settings(base_keywords)%]">
<meta name="description" content="Admin's part of BooT's site">
<LINK href="../css/admin.css" type=text/css rel=stylesheet>
<script type="text/javascript" src="../inc/misc.js"></script>
<link rel="icon" type="image/png" href="[%BASE_HREF%]favicon.png" />
[%head_inc%]
</head>
<body bgcolor=#ffffff>
<table border=0 cellspacing=3 cellpadding=3 width=100% height=100% align=center bgcolor=#FFFFFF>
<tr valign=top>
	<td width=150 align=center>
		[%block(menu_admin)%]
	</td>
	<td width=100% align=center>
		<table width=100% border=0 cellspacing=5 cellpadding=5 align=center bgcolor=#FFFFFF>
			<tr class=header><td><center><font class=title>[%Header%]</font></center></td></tr>
		</table>
		
[%inner_content%]

	</td>
</tr>
</table>
</body>
</html>