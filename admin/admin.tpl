<html>
    <head>
        <title>[%settings(site_title)%] > Адм. раздел > [%Header%]</title>
        <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <meta name="keywords" content="[%settings(base_keywords)%]">
        <meta name="description" content="Admin's part of BooT's site">
        <!-- Bootstrap core CSS -->
        <link href="[%SUBDIR%]css/bootstrap.min.css" rel="stylesheet" />
        <link href="[%SUBDIR%]css/admin.css" rel="stylesheet" />
        <link rel="icon" type="image/png" href="[%BASE_HREF%]favicon.png" />
        [%INCLUDE_HEAD%]
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
</head>
    <body bgcolor="#ffffff">
        <table border=0 cellspacing=3 cellpadding=3 width=100% height=100% align=center bgcolor=#fff>
            <tr valign=top>
                <td width=150 align=center>
                    [%block(menu_admin)%]
                </td>
                <td width=100% align=center>
                    <center><h1>[%Header%]</h1></center>

                    [%inner_content%]

                </td>
            </tr>
        </table>
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="[%SUBDIR%]include/js/bootstrap.min.js"></script>
        <script src="[%SUBDIR%]include/js/docs.min.js"></script>
        <script src="[%SUBDIR%]include/js/misc.js"></script>
    </body>
</html>