<?php

namespace classes;

/**
 * Base class for models.
 *
 * @author BooT
 */
class BaseModel implements \ArrayAccess 
{   
    public static $fields = [];
    
    private $data;
    private $errors;
    
    public function __construct($id = null) 
    {
        $this->errors = [];
        $result = static::findOne($id);
        if($result !== null && $result->num_rows) {
            $row = $result->fetch_assoc();
            foreach($row as $key => $value) {                
                $this->data[$key] = $value;
            }
            return $this;
        }
        foreach(static::$fields as $key) {
            $this->data[$key] = '';
        }
        return $this;
    }
    
    /**
     * @return string Table name
     */
    public static function tableName()
    {
        return '';
    }
    
    public function __get(string $name) 
    {
        if(array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        throw new \InvalidArgumentException('Unknown property: ' . $name);        
    }
    
    public function __set(string $name, $value) 
    {
        if(array_key_exists($name, $this->data)) {
            return $this->data[$name] = $value;
        }
        throw new \InvalidArgumentException('Unknown property: ' . $name);        
    }
    
    public function load($input) {
        if(!$input || !is_array($input) || !count($input)) {
            return false;
        }
        foreach($input as $key => $value) {
            $this->$key = $value;
        }
        return true;
    }
    
    
    /**
     * @return array Rules array
     */
    public function rules()
    {
        return [];
    }
    
    /**
     * @return boolean True if no rules or all rules checked.
     */
    public function checkRules() 
    {
        $rules = $this->rules();
        if(!count($rules)) {
            return true;
        }
        $this->errors = [];
        foreach($rules as $rule) {            
            foreach($rule[0] as $field) {
                $this->checkRule($field, $rule);
            }
        }        
        return count($this->errors) == 0;
    }
    
    private function checkRule(string $field, array $rule) {
        switch ($rule[1]) {
            case 'required':
                if(!isset($this->$field)) {
                    return true;
                }
                break;
            case 'integer':
                if($this->checkInteger($this->$field, $rule)) {
                    return true;
                }
                break;
            case 'string':
                if($this->checkString($this->$field, $rule)) {
                    return true;
                }
                break;
            case 'number':
                if(is_numeric($this->$field)) {
                    return true;
                }
                break;
            case 'text':
                if(is_string($this->$field)) {
                    return true;
                }
                break;
            default:
                break;
        }
        $this->errors[] = "Field {$field} Rule {$rule[1]} failed.";
        return false;
    }
    
    private function checkInteger($value, $rule)
    {
        if(!is_int($value)) {
            return false;
        }        
        if(is_array($rule[2])) {
            return $this->checkInteger($value, $rule[2]);
        }        
        if($rule['min'] && $value < $rule['min']) {
            return false;
        }        
        if($rule['max'] && $value > $rule['max']) {
            return false;
        }
        return true;
    }
    
    private function checkString($value, $rule)
    {
        if(!is_string($value)) {
            return false;
        }        
        if(is_array($rule[2])) {
            return $this->checkString($value, $rule[2]);
        }        
        if($rule['min'] && strlen($value) < $rule['min']) {
            return false;
        }        
        if($rule['max'] && strlen($value) > $rule['max']) {
            return false;
        }
        return true;
    }
    
    /**
     * @return array Get errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @return string Get errors as string
     */
    public function getErrorsAsString(): string
    {
        if(!count($this->errors)) {
            return '';
        }
        $result = '';
        foreach($this->errors as $error) {
            $result .= $error . PHP_EOL;
        }
        return $result;
    }
    
    
    /**
     * Save model to DB.
     * 
     * @param boolean check rules before save.
     * 
     * @return boolean TRUE if complete.
     */
    public function save($check_rules = true)             
    {
        if($check_rules) {
            if(!$this->checkRules()) {
                return false;
            }
        }
        if($this->data['id']) {
            App::$db->updateTable(static::tableName(), $this->data, ['id' => $this->data['id']]);
        } else {
            App::$db->insertTable(static::tableName(), $this->data);
            $this->data['id'] = App::$db->insert_id();
        }
        return true;
    }
    
    /**
     * Delete model from DB.
     * 
     * @return \mysqli_result.
     */
    public function delete()
    {
        if(!is_integer($this->data['id'])) {
            throw new \InvalidArgumentException('ID is empty');
        }
        return App::$db->deleteFromTable(static::tableName(), ['id' => $this->data['id']]);  
    }    
    
    /**
     * Return model data from DB.
     * 
     * @param integer $id
     * 
     * @return \mysqli_result.
     */
    public static function findOne($id) {
        if($id !== null) {
            return App::$db->findOne(static::tableName() , $id);
        }
        return null;
    }
    
    /**
     * Return model's data from DB.
     * 
     * @param array $where params for where statement.
     * @param string $order_by Order by string.
     * 
     * @return \mysqli_result.
     */
    public static function findAll($where = [], $order_by = 'id desc') {
        return App::$db->findAll(static::tableName() , $where, $order_by);          
    }
    
    /**
     * Return model's data
     * 
     * @return array
     */
    public function asArray() {
        return $this->data;
    }

    /**
     * Function for \ArrayAccess implement
     */
    public function offsetSet($offset, $value) 
    {
        $this->$offset = $value;
    }
    /**
     * Function for \ArrayAccess implement
     */
    public function offsetExists($offset) 
    {
        return array_key_exists($offset, $this->data);
    }
    /**
     * Function for \ArrayAccess implement
     */
    public function offsetUnset($offset) 
    {
        $this->$offset = '';
    }
    /**
     * Function for \ArrayAccess
     */
    public function offsetGet($offset) 
    {
        return $this->$offset;
    }
   
}
