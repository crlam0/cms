<?php

/* =========================================================================

  SQL helper library

  ========================================================================= */

namespace classes;
use classes\App;

class DB 
{
    /**
     * @var mysqli Use mysqli object
     */
    public $mysqli;
    /**
     * @var boolean Write debug information if true.
     */
    public $debug = false;
    
    /**
     * @var Array Debug array of all SQL query
     */
    public $query_log_array;
    
    private $host;
    private $user;
    private $passwd;
    private $dbname;
    
    /**
    * @var Array Array of denied words for input strings
    */
    private $DENIED_WORDS=array('union','insert','update ','delete ','alter ','drop ','\$_[','<?php','<script','javascript');
    
    /**
     * Connect to database
     *
     * @param string $host Host
     * @param string $user Username
     * @param string $passwd Password
     * @param string $dbname DB
     *
     * @return null
     */    
    public function __construct(string $host, string $user, string $passwd, string $dbname) 
    {
        $this->host=$host;
        $this->user=$user;
        $this->passwd=$passwd;
        $this->dbname=$dbname;
        $this->mysqli = new \mysqli($this->host, $this->user, $this->passwd, $this->dbname);
        if (mysqli_connect_error()) {
            die('DB connect error ' . mysqli_connect_errno() . ': ' . mysqli_connect_error());
        }
        $this->query('SET character_set_client = utf8');
        $this->query('SET character_set_results = utf8');
        $this->query('SET character_set_connection = utf8');
        empty($this->query_log_array);
        $this->query_log_array[] = 'Connected to ' . $host;
    }

    private function bindParams($stmt, $params) {
        $types = '';
        $values = [];
        foreach ($params as $name => $value) {            
            if($name == 'id') {
                $types .= 'i';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        if(!$stmt->bind_param($types, ...$values)) {
            throw new \InvalidArgumentException('Cant bind params: ' . $stmt->error);
        }
        return $stmt;
    }

    public function prepareAndExecute(string $sql, array $params = []) {
        if($this->debug){
            $start_time = microtime(true);
        }
        $stmt = $this->mysqli->prepare($sql);
        if(!$stmt) {
            throw new \InvalidArgumentException('SQL Prepare error: ' . $this->mysqli->error . ' Query is: ' . $sql);
        }
        $this->bindParams($stmt, $params);
        if (!$stmt->execute()) {
            throw new \InvalidArgumentException('SQL Execute error: ' . $stmt->error . ' Query is: ' . $sql);
        }        
        if($this->debug){
            $time = sprintf('%.4F', microtime(true) - $start_time);
            $this->query_log_array[] = $time . "\t" . $sql;
        }
        $result = $stmt->get_result();
        $stmt->free_result();
        return $result;
    }
    
    /**
     * Replace for mysql_query
     *
     * @param string $sql SQL Query
     * @param boolean $dont_debug Dont echo debug info
     *
     * @return array mysqli result
     */
    public function query(string $sql, array $params = []) 
    {
        if(count($params)) {
            return $this->prepareAndExecute($sql, $params);
        }
        if($this->debug){
            $start_time = microtime(true);
        }
        $result = $this->mysqli->query($sql);
        if($this->debug){
            $time = sprintf('%.4F', microtime(true) - $start_time);
            $this->query_log_array[] = $time . "\t" . $sql;
        }
        if (!$result) {
            if($this->debug){
                throw new \InvalidArgumentException('SQL Error: ' . $this->mysqli->error . ' Query is: ' . $sql);
            }
            die('SQL Error: '.$this->mysqli->error);
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
    public function getRow(string $sql, array $params = []) 
    {
        $result = $this->query($sql, $params);    
        if ($result->num_rows) {
            $row = $result->fetch_array();
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Return last insert ID.
     *
     * @return integer 
     */
    public function insert_id(): int 
    {
        return $this->mysqli->insert_id;
    }
    
    /**
     * Test field parameter for deny SQL injections
     *
     * @param string $sql Input string
     *
     * @return string Output string
     */
    public function test_param($str, $param='') 
    {
        if (is_array($str)) {
            foreach ($str as $key => $value) {
                $str[$key]=$this->test_param($value);
            }
            return $str;
        }    
        if(!strstr(App::$server['PHP_SELF'], 'admin/')) {
            $str=htmlspecialchars($str);            
        }
        $str=$this->escapeString($str);        
        foreach($this->DENIED_WORDS as $word) {
            if(stristr($str, $word)){
                header($_SERVER['SERVER_PROTOCOL'] . ' 400 Bad Request', true, 400);
                exit();
            }
        }
        return $str;
    }

    /**
     * Escape string
     *
     * @param string $str Input string
     *
     * @return string
     */
    public function escapeString(string $str) : string 
    {
        return $this->mysqli->escape_string($str);        
    }

    /**
     * Return htmlspecialchars() for $value if needed.
     *
     * @param string $value Field value
     * @param string $field Field name
     *
     * @return string Complete string for query
     */
    public function specialChars(string $value, string $field = '') : string 
    {
        if($field == 'title' || $field == 'name') {
            $value = htmlspecialchars($value);
        }
        return $value;
    }
    
    /**
     * Return string for insert query
     *
     * @param array $fields Fields and data for query
     *
     * @return string Complete string for query
     */
    public function insertFields(array $fields) : string 
    {
        $total = count($fields);
        $output = '';
        $str_fields = '';
        $str_values = '';
        if ($total > 0) {
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
                $str_fields.=$key . $str;
                if(strstr($value,'date_format')){
                    $str_values.=stripcslashes($value) . $str;
                }else{
                    $value=$this->specialChars($value, $key);
                    // $value=$this->escapeString($value);
                    $str_values.= ( $value == 'now()' ? $value . $str : "'$value'$str");
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
    public function updateFields(array $fields) : string 
    {
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
                $value=$this->specialChars($value, $key);
                // $value=$this->escapeString($value);
                $output.= ( $value == 'now()' ? "$key=$value" . $str : "$key='$value'$str");
            }    
        }
        return $output;
    }
    
}


