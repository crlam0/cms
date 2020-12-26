<?php

/* =========================================================================

  SQL helper library

  ========================================================================= */

namespace classes;

class DB 
{
    /**
     * @var mysqli Used mysqli object
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
        $this->host = $host;
        $this->user = $user;
        $this->passwd = $passwd;
        $this->dbname = $dbname;
        $this->mysqli = new \mysqli($this->host, $this->user, $this->passwd, $this->dbname);

        if ($this->mysqli->connect_error) {
            $error_str = 'DB connect error ' . $this->mysqli->connect_errno . ': ' . $this->mysqli->connect_error;
            App::$logger->error($error_str);
            die($error_str);
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
            throw new \InvalidArgumentException('SQL Cant bind params: ' . $stmt->error);
        }
        return $stmt;
    }

    public function prepareAndExecute(string $sql, array $params = []) {

        $stmt = $this->mysqli->prepare($sql);
        if(!$stmt) {
            throw new \InvalidArgumentException('SQL Prepare error: ' . $this->mysqli->error . ' Query was: ' . $sql);
        }
        $this->bindParams($stmt, $params);
        if (!$stmt->execute()) {
            throw new \InvalidArgumentException('SQL Execute error: ' . $stmt->error . ' Query was: ' . $sql);
        }
        $result = $stmt->get_result();
        $stmt->free_result();
        return $result;
    }
    
    /**
     * Replace for mysql_query
     *
     * @param string $sql SQL Query
     * @param boolean $params Params for query prepare
     *
     * @return array mysqli result
     */
    public function queryUnsafe(string $sql, array $params = []) 
    {
        if(count($params)) {
            return $this->prepareAndExecute($sql, $params);
        }
        $result = $this->mysqli->query($sql);
        if (!$result) {
            throw new \InvalidArgumentException('SQL Error: ' . $this->mysqli->error . ' Query was: ' . $sql);
        }
        return $result;
    }
    
    /**
     * Replace for mysql_query
     *
     * @param string $sql SQL Query
     * @param boolean $params Params for query prepare
     *
     * @return array mysqli result
     */
    public function query(string $sql, array $params = [])             
    {
        try {
            if($this->debug){
                $start_time = microtime(true);
            }
            $result = $this->queryUnsafe($sql, $params);
            if($this->debug){
                $time = sprintf('%.4F', microtime(true) - $start_time);
                $this->query_log_array[] = $time . "\t" . $sql;
            }
            return $result;
        } catch (\InvalidArgumentException $e) {
            App::$logger->error($e->getMessage());
            if($this->debug) {                
                die($e->getMessage());
            } else {
                die('Sorry, internal server error. Try to retry later.');
            }
        }
    }

    /**
     * Return one row from query
     *
     * @param string $sql SQL Query
     * @param boolean $params Params for query prepare
     *
     * @return array One row
     */
    public function getRow(string $sql, array $params = []) 
    {        
        $result = $this->query($sql, $params);    
        if ($result->num_rows) {
            return $result->fetch_array();
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
     * Return last error.
     *
     * @return string
     */
    public function error(): string 
    {
        return $this->mysqli->error;
    }
    
    /**
     * Test field parameter for deny SQL injections
     *
     * @param string $sql Input string
     *
     * @return string Output string
     */
    public function testParam($str, $param='') 
    {
        if (is_array($str)) {
            foreach ($str as $key => $value) {
                $str[$key] = $this->testParam($value);
            }
            return $str;
        }    
        if(!strstr(App::$server['REQUEST_URI'], 'admin/')) {
            $str = htmlspecialchars($str);
        }
        $str = $this->escapeString($str);
        foreach($this->DENIED_WORDS as $word) {
            if(stristr($str, $word)){
                App::$logger->error('testParam denied word', ['URI'=>App::$server['REQUEST_URI']]);
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
        return $this->mysqli->real_escape_string($str);        
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
            foreach ($fields as $key => $value) {
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
                    $str_values.= ( $value == 'now()' ? $value . $str : "'{$value}'{$str}");
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
        foreach ($fields as $key => $value) {
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
                $output.= ( $value == 'now()' ? "{$key}={$value}" . $str : "{$key}='{$value}'{$str}");
            }    
        }
        return $output;
    }
    
    /**
     * Insert row to table
     *
     * @param string $table Table name
     * @param array $fields Fields and data for query
     *
     * @return string Complete string for query
     */
    public function insertTable(string $table, array $fields) : bool
    {
        $str_fields = '';
        $str_values = '';
        $params=[];
        $total = count($fields);
        $a=0;
        foreach($fields as $field => $value) {
            $a++;
            $str_fields .= $field;
            if(strstr($value, 'now()') || strstr($value, 'date_format')){
                $str_values .= $value;
            } else {
                $str_values .= '?';
                $params[$field] = stripcslashes($value);
            }
            if ($a != $total) {
                $str_fields .= ',';
                $str_values .= ',';
            }            
        }
        $sql = "INSERT INTO {$table}({$str_fields}) VALUES({$str_values})";
        return $this->query($sql, $params);        
    }
    
    /**
     * Update data in table
     *
     * @param string $table Table name
     * @param array $fields Fields and data for query
     * @param array $where Fields for where statement
     *
     * @return string Complete string for query
     */
    public function updateTable(string $table, array $fields, array $where) : bool
    {
        $sql = "UPDATE {$table} SET ";
        $params=[];
        $total = count($fields);
        $a=0;
        foreach($fields as $field => $value) {
            $a++;
            if(strstr($value, 'now()') || strstr($value, 'date_format')){
                $sql .= $field . "='{$value}'";
            } else {
                $sql .= $field . '=?';
                $params[$field] = stripcslashes($value);
            }
            if ($a != $total) {
                $sql .= ',';
            }            
        }
        if(count($where)) {
            $sql .= ' WHERE ';
            $total = count($where);
            $a=0;
            foreach($where as $field => $value) {
                $a++;
                $sql .= $field . ' = ?';    
                if ($a != $total) {
                    $sql .= ' AND ';
                }            
                $params['$field'] = $value;            
            }
        }
        return $this->query($sql, $params);
    }
    
    /**
     * Select record from DB.
     *
     * @param string $table Table name
     * @param integer $id
     * 
     * @return \mysqli_result.
     */
    public function findOne(string $table, string $id) 
    {
        $query = "SELECT * FROM {$table} WHERE id=?";
        return $this->query($query, ['id' => $id]);          
    }
    
    /**
     * Select records from DB.
     *
     * @param string $table Table name
     * @param array $where Fields for where statement
     * @param string $order_by Expression for ORDER BY
     * 
     * @return \mysqli_result.
     */
    public function findAll(string $table, array $where = [], string $order_by = 'id desc') 
    {
        if(!count($where)) {
            return $this->query("SELECT * FROM {$table} ORDER BY {$order_by}");
        }
        $expr = '';
        foreach($where as $key => $value) {
            if(strlen($expr) == 0) {
                $expr .= $key . '=?';                
            } else {
                $expr .= ' AND ' . $key . '=?';
            }
        }
        $query = "SELECT * FROM {$table} WHERE {$expr} ORDER BY {$order_by}";
        return $this->query($query, $where);          
    }    
    
    /**
     * Delete record from DB.
     *
     * @param string $table Table name
     * @param array $where Fields for where statement
     * 
     * @return \mysqli_result.
     */
    public function deleteFromTable(string $table, array $where)
    {
        
        $expr = '';
        foreach($where as $key => $value) {
            if(strlen($expr) == 0) {
                $expr .= $key . '=?';                
            } else {
                $expr .= ' AND ' . $key . '=?';
            }
        }
        $query = "delete from {$table} where {$expr}";
        return $this->query($query , $where);  
    }        
    
}

