<?php

/*
  SQL library.
 */

$mysqli = new mysqli($DBHOST, $DBUSER, $DBPASSWD, $DB);

if (mysqli_connect_error()) {
    die('DB connect error ' . mysqli_connect_errno() . ': ' . mysqli_connect_error());
}

/* test field marameter for deny sql injections */

function db_test_param($str,$param="") {
    global $server;
    if (is_array($str))return $str;
    if (get_magic_quotes_gpc()){
	$str=stripslashes($str);
    }
    if(!strstr($server["PHP_SELF"], "admin/"))$str=htmlspecialchars($str);
    if($param=="title")$str=str_replace("\"","&quot;",$str);
    $str=str_replace("'","\'",$str);
    $str=str_replace("\"","\\\"",$str);
//    $str=mysql_real_escape_string($str);
//    echo $str."<br>";
    if ((stristr($str, "union")) || (stristr($str, "insert")) || (stristr($str, "update ")) || (stristr($str, "delete ") || (stristr($str, "alter ") || (stristr($str, "drop "))
            || (strstr($str, "\$_[")) || (stristr($str, "<?php")) || ( stristr($str, "'")) && !stristr($str, "\'")) )  ) {
        // my_msg("sql_field_eror");
        exit();
    }
	$str=str_replace("\\r\\n","",$str);
    return $str;
}

/* replace for my_query */

function my_query($sql, $conn=null, $dont_debug=false) {
    global $mysqli,$settings;
    if (!$dont_debug)print_debug($sql);
    $result = $mysqli->query($sql);
    if (!$result) {
        echo "SQL Error: ".$mysqli->error;
        if($settings['debug'])echo "<br />Query is: ".$sql;
        exit();
    }
    return $result;
}

/* return first row */

function my_select_row($sql, $dont_debug=false) {
    global $conn;
    $result = my_query($sql, $conn, $dont_debug);    
    if ($result->num_rows) {
        $row = $result->fetch_array();
        return $row;
    } else {
        return 0;
    }
}

function db_insert_fields($fields) {
    $total = count($fields);
    if ($total > 0) {
        $a = 0;
        while (list($key, $value) = each($fields)) {
            $a++;
//            $value = db_test_param($value);
            if (is_array($value))$value = implode(";", $value);
            if ($a == $total) {
                $str = "";
            } else {
                $str = ",";
            }
            $str_fields.=$key . $str;
            if(strstr($value,'date_format')){
                $str_values.=stripcslashes($value) . "$str";
            }else{
                $str_values.= ( $value == "now()" ? "$value" . "$str" : "'$value'$str");
            }    
        }
        $output = "($str_fields) VALUES($str_values)";
        return $output;
    } else {
        return 0;
    }
}

function db_update_fields($fields) {
    $total = count($fields);
    $a = 0;
    while (list($key, $value) = each($fields)) {
        $a++;
//        $value = db_test_param($value);
        if (is_array($value))$value = implode(";", $value);
        if ($a == $total) {
            $str = "";
        } else {
            $str = ",";
        }
        if(strstr($value,'date_format')){
            $output.="$key=".stripcslashes($value) . $str;
        }else{
            $output.= ( $value == "now()" ? "$key=$value" . $str : "$key='$value'$str");
        }    
    }
    return $output;
}

$query = "SET character_set_client = utf8";
my_query($query, $conn, 1);
$query = "SET character_set_results = utf8";
my_query($query, $conn, 1);
$query = "SET character_set_connection = utf8";
my_query($query, $conn, 1);

?>