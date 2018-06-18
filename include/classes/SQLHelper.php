<?php

/* =========================================================================

  SQL helper library

  ========================================================================= */

namespace Classes;

class SQLHelper {
    public $mysqli;
    
    /**
    * @var Array Array of denied words for input strings
    */
    private $DENIED_WORDS=array('union','insert','update ','delete ','alter ','drop ','\$_[','<?php','javascript');
    
    /**
     * Connect to database
     *
     * @param string $host Host
     * @param string $user Username
     * @param string $passwd Password
     * @param string $db DB
     *
     * @return null
     */    
    public function __construct($host, $user, $passwd, $db) {
        $this->mysqli = new \mysqli($host, $user, $passwd, $db);
        if (mysqli_connect_error()) {
            die('DB connect error ' . mysqli_connect_errno() . ': ' . mysqli_connect_error());
        }        
    }
    
    
    /**
     * Test field parameter for deny sql injections
     *
     * @param string $sql Input string
     *
     * @return string Output string
     */
    public function test_param($str,$param="") {
        global $server;
        if (is_array($str)) {
            return $str;
        }    
        if (get_magic_quotes_gpc()){
            $str=stripslashes($str);
        }
        if(!strstr($server['PHP_SELF'], 'admin/')) {
            $str=htmlspecialchars($str);
        }    
        if($param=="title") { 
            $str=str_replace("\"",'&quot;',$str);
        }    
        $str=str_replace("'","\\'",$str);
        $str=str_replace("\"","\\\"",$str);
    //    $str=mysql_real_escape_string($str);
    //    echo $str."<br>";

        foreach($this->DENIED_WORDS as $word) {
            if(stristr($str, $word)){
                header($server['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
                exit();
            }
        }

        $str=str_replace("\\r\\n","",$str);
        return $str;
    }
    
    public function escape_string($str) {
        return $this->mysqli->escape_string($str);        
    }

    /**
     * Replace for mysql_query
     *
     * @param string $sql SQL Query
     * @param boolean $dont_debug Dont echo debug info
     *
     * @return array mysqli result
     */
    public function query($sql, $dont_debug=false) {
        global $settings,$DEBUG;
        if (!$dont_debug) {
            print_debug($sql);
        }    
        if($settings['debug']){
            $start_time = microtime(true);
        }    
        $result = $this->mysqli->query($sql);
        if($settings['debug']){
            $time = sprintf('%.4F', microtime(true) - $start_time);
            $DEBUG['sql_query_array'][] = $time . "\t" . $sql;
        }
        if (!$result) {
            echo 'SQL Error: '.$this->mysqli->error;
            if($settings['debug']){
                echo '<br />Query is: '.$sql;
            }
            exit();
        }
        return $result;
    }

    /**
     * Return one row from query
     *
     * @param string $sql SQL Query
     * @param boolean $dont_debug Dont echo debug info
     *
     * @return array One row
     */
    public function select_row($sql, $dont_debug=false) {
        $result = $this->query($sql, $dont_debug);    
        if ($result->num_rows) {
            $row = $result->fetch_array();
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Return string for insert query
     *
     * @param array $fields Fields and data for query
     *
     * @return string Complete string for query
     */
    public function insert_fields($fields) {
        $total = count($fields);
        $output = '';
        $str_fields = '';
        $str_values = '';
        if ($total > 0) {
            $a = 0;
            while (list($key, $value) = each($fields)) {
                $a++;
                if (is_array($value)){ 
                    $value = implode(";", $value);
                }    
                if ($a == $total) {
                    $str = "";
                } else {
                    $str = ",";
                }
                $str_fields.=$key . $str;
                if(strstr($value,'date_format')){
                    $str_values.=stripcslashes($value) . "$str";
                }else{
                    $value=$this->mysqli->escape_string($value);
                    $str_values.= ( $value == 'now()' ? "$value" . "$str" : "'$value'$str");
                }    
            }
            $output = "({$str_fields}) VALUES({$str_values})";
            return $output;
        } else {
            return 0;
        }
    }

    /**
     * Return string for update query
     *
     * @param array $fields Fields and data for query
     *
     * @return string Complete string for query
     */
    public function update_fields($fields) {
        $total = count($fields);
        $output = '';
        $a = 0;
        while (list($key, $value) = each($fields)) {
            $a++;
            if (is_array($value)){
                $value = implode(';', $value);
            }    
            if ($a == $total) {
                $str = '';
            } else {
                $str = ',';
            }
            if(strstr($value,'date_format')){
                $output.="$key=".stripcslashes($value) . $str;
            }else{
                $value=$this->mysqli->escape_string($value);
                $output.= ( $value == 'now()' ? "$key=$value" . $str : "$key='$value'$str");
            }    
        }
        return $output;
    }
    
}


