<?php
include "../include/common.php";

$file_name=$DIR.$input["file_name"];
$download_file_name=$input["download_file_name"];

if(strstr($file_name,".php")){
    echo "kekeke";
    exit ();
}

$mime_type=mime_content_type($file_name);

if(file_exists($file_name)) {
    header('Content-Description: File Transfer');
    header('Content-Type: '.$mime_type);
    header('Content-Disposition: attachment; filename='.$download_file_name);
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file_name));
    ob_clean();
    flush();
    readfile($file_name);
    if(is_numeric($input['media_file_id'])){
        $query="update media_files set download_count=download_count+1 where id='{$input['media_file_id']}'";
        my_query($query, true);
    }
    exit;
}

