<?php

namespace classes;

/**
 * Base class for models.
 *
 * @author BooT
 */
class BaseModel implements \ArrayAccess 
{   
    private $data;
    private $errors;
    
    /**
     * @return string Table name
     */
    public static function tableName()
    {
        return '';
    }
    
    /**
     * @return array Fields
     */
    public static function fields()
    {
        return [];
    }
    
    /**
     * @return array Rules array
     */
    public function rules()
    {
        return [];
    }
    
    /**
     * @return array Labaels for fields.
     */
    public function attributeLabels()
    {
        return [];
    }
    
    public function __construct($id = null) 
    {
        $this->errors = [];
        if($id !== null && $id>0) {
            return $this->loadFromDb($id);
        }
        foreach(static::fields() as $key) {
            $this->data[$key] = null;
        }
        return $this;
    }
    
    /**
     * @return string Label for field
     */
    public function label(string $name)
    {
        $labels = $this->attributeLabels();
        if(isset($labels[$name])) {
            return $labels[$name];
        }        
        return $name;
    }
    
    /**
     * Load data to model from Database
     * 
     * @return BaseModel|null 
     */
    public function loadFromDb($id = null) 
    {
        $result = static::findOne($id);
        if($result !== null && $result->num_rows) {
            $row = $result->fetch_assoc();
            foreach($row as $key => $value) {                
                $this->data[$key] = $value;
            }
            return $this;
        }
        return null;
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
        if(isset($this->data) && array_key_exists($name, $this->data)) {
            return $this->data[$name] = $value;
        }
        throw new \InvalidArgumentException('Unknown property: ' . $name);        
    }
    
    /**
     * Load data to model from $input
     *
     * @param array $input Input data from POST/GET
     *
     * @return bool
     */
    public function load($input) : bool
    {
        if(!$input || !is_array($input) || !count($input)) {
            return false;
        }
        foreach($input as $key => $value) {
            $this->$key = $value;
        }
        return true;
    }
    
    
    /**
     * @return boolean True if no rules or all rules checked.
     */
    public function validate() : bool
    {
        $rules = $this->rules();
        if(!count($rules)) {
            return true;
        }
        $this->errors = [];
        foreach($rules as $rule) {            
            foreach($rule[0] as $field) {
                $this->checkRule($field, $this->data[$field], $rule);
            }
        }
        $have_errors = (count($this->errors) > 0);
        if($have_errors) {
            App::setErrors($this->errors);
        }
        return !$have_errors;
    }
    
    /**
     * Check $value by $rule.
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $rule Rule
     * 
     * @return bool
     */
    private function checkRule(string $field, $value, array $rule) : bool
    {
        switch ($rule[1]) {
            case 'required':
                if(isset($value)) {
                    return true;
                }
                break;
            case 'integer':
                if($this->checkInteger($value, $rule)) {
                    return true;
                }
                break;
            case 'string':
                if($this->checkString($value, $rule)) {
                    return true;
                }
                break;
            case 'number':
                if(is_numeric($value)) {
                    return true;
                }
                break;
            case 'text':
                if(is_string($value)) {
                    return true;
                }
                break;
            default:
                break;
        }
        App::debug("Field '{$field}' Rule '{$rule[1]}' failed.");
        $this->errors[] = "Поле '{$this->label($field)}' заполнено неверно.";
        return false;
    }
    
    /**
     * Check $value by $rule.
     * 
     * @param mixed $value Field value
     * @param array $rule Rule
     * 
     * @return bool
     */
    private function checkInteger($value, array $rule) : bool
    {
        if(strlen($value) != strlen(intval($value))) {
            return false;
        }        
        if(isset($rule[2]) && is_array($rule[2])) {
            return $this->checkInteger($value, $rule[2]);
        }        
        if(isset($rule['min']) && $value < $rule['min']) {
            return false;
        }        
        if(isset($rule['max']) && $value > $rule['max']) {
            return false;
        }
        return true;
    }
    
    /**
     * Check $value by $rule.
     * 
     * @param mixed $value Field value
     * @param array $rule Rule
     * 
     * @return bool
     */
    private function checkString($value, array $rule) : bool
    {
        if(!is_string($value)) {
            return false;
        }        
        if(isset($rule[2]) && is_array($rule[2])) {
            return $this->checkString($value, $rule[2]);
        }        
        if(isset($rule['min']) && strlen($value) < $rule['min']) {
            return false;
        }        
        if(isset($rule['max']) && strlen($value) > $rule['max']) {
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
    public function save(bool $need_validate = true)             
    {
        if($need_validate && !$this->validate()) {
            return false;
        }
        if($this->data['id']) {
            App::$db->updateTable(static::tableName(), $this->data, ['id' => $this->data['id']]);
        } else {
            App::$db->insertTable(static::tableName(), $this->data);
            $this->data['id'] = App::$db->insert_id();
        }
        App::setErrors([]);
        return true;
    }
    
    /**
     * Delete model from DB.
     *
     * @return bool
     */
    public function delete(): bool
    {
        if(!isset($this->data) || !is_integer($this->data['id'])) {
            throw new \InvalidArgumentException('Data or ID is empty');
        }
        return App::$db->deleteFromTable(static::tableName(), ['id' => $this->data['id']]);  
    }    
    
    /**
     * Return model data from DB.
     *
     * @param integer $id
     *
     * @return \mysqli_result|null
     */
    public static function findOne(?int $id): ?\mysqli_result {
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
     * @return \mysqli_result
     */
    public static function findAll(array $where = [], string $order_by = 'id desc'): ?\mysqli_result {
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
