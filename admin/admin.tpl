<html>
    <head>
        <title>[%settings(site_title)%] > Адм. раздел > [%Header%]</title>
        <meta HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <!-- Bootstrap core CSS -->
        <link href="[%SUBDIR%]admin/bootstrap.min.css" rel="stylesheet" />
        <link href="[%SUBDIR%]admin/admin.css" rel="stylesheet" />
        <link rel="icon" type="image/png" href="[%BASE_HREF%]favicon.png" />
        <script type="text/javascript" src="[%BASE_HREF%]include/js/jquery.min.js"></script>        
        [%INCLUDE_HEAD%]
        
</head>
    <body bgcolor="#fff">
<div class="modal fade modal-wide" id="myModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header"><button class="close" type="button" data-dismiss="modal">x</button>
                <h4 class="modal-title" id="popupHeader"></h4>
            </div>
            <div class="modal-body">
                <div id=popupContent></div>                        
            </div>
        </div>
    </div>
</div>
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
        [%block(debug)%]
        <script src="[%SUBDIR%]include/js/bootstrap.min.js"></script>
        <script src="[%SUBDIR%]include/js/misc.js"></script>

        [%block(debug)%]

    </body>
</html>
