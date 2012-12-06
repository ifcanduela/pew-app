<?php

/**
 * @package sys
 */

/**
 * A class to validate values against one or multiple rules.
 *
 * The available validation rules are:
 * - is_not_empty:value
 * - is_number:value
 * - is_integer:value
 * - is_in_range:value:min:max
 * - is_string:value
 * - is_alphanumeric:value
 * - is_alphabetic:value
 * - is_in_string:value:string
 * - is_in_length:value:min:max
 * - is_email:value
 * - is_in_array:value:val1:val3:...:valN
 *
 * @version 0.1 05-10-2011
 * @author ifcanduela <ifcanduela@gmail.com>
 * @package sys
 */
class PewValidator
{
    /**
     * @var array
     */
    protected $_errors = array();
    
    /**
     * @var array
     */
    protected $_checks = array();
    
    /**
     * @var mixed
     */
    protected $_value = null;
    
    /**
     *
     * @return PewValidator
     */
    public function __construct($value = null, $rules = null)
    {
        $this->_value = $value;
        
        if (is_string($rules)) {
            $this->_checks = explode('|', $rules);
        }
    }
    
    /**
     * Sets or updates the value to test.
     *
     * @return void
     */
    public function set_value($value)
    {
        $this->_value = $value;
    }
    
    /**
     * Return the current value to be tested.
     * 
     * @return mixed The value being tested.
     */
    public function get_value()
    {
        return $this->_value;
    }
    
    /**
     * Adds a new validation rule.
     *
     * @return void
     */
    public function add_rule($rule)
    {
        $this->_checks[] = $rule;
    }
    
    /**
     * Validates the current value using the current ruleset.
     * 
     * @return array The error list, empty if all criteria was met
     */
    public function check_all()
    {
        # clear the error list
        $this->reset_errors();
        
        # loop the checks array
        foreach ($this->checks as $check) {
            # extract the rule parameters
            $params = explode(':', $check);
            # the first parameter is the rule itself
            $rule = array_shift($params);
            # prepare the validation function arguments list
            $arguments = array($this->_value) + $params;
            # call the validation function
            $result = call_user_func_array(array($this, $rule), $arguments);
            
            # if an error message was returned, add it to the error list
            if (!$result) {
                $this->_errors[] = compact($rule, $value, $arguments);
            }
        }
        
        return $this->_errors;
    }
    
    /**
     * Returns the errors produced by the last validation.
     *
     * @return array The error list, empty if all criteria was met
     */
    public function get_errors()
    {
        return $this->_errors;
    }
    
    /**
     * Resets the error list.
     *
     * @return void
     */
    public function reset_errors()
    {
        $this->_errors = array();
    }
    
    /**
     * Checks if the value has a non-empty value.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_not_empty($value)
    {
        return trim($value);
    }
    
    /**
     * Checks if the value is a number.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_number($value)
    {
        return is_numeric($value);
    }
    
    /**
     * Checks if the value is an integer number.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_integer($value)
    {
        return (is_numeric($value)) && (intval($value) == $value);
    }
    
    /**
     * Checks if the value is a number within a range.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_within_range($value, $min, $max)
    {
        return ($value > $min) && ($value < $max);
    }
    
    /**
     * Checks if the value is a string.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_string($value)
    {
        return is_string($value);
    }
    
    /**
     * Checks if the value is only comprised of letters and numbers.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_alphanumeric($value)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }
    
    /**
     * Checks if the value is only comprised of letters.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_alphabetic($value)
    {
        return preg_match('/^[a-zA-Z]+$/', $value);
    }
    
    /**
     * Checks if the value is a string
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_within_length($value, $min, $max)
    {
        $value .= '';
        $len = strlen($value);
        
        if ($max < 0) {
            $max = $len + 1;
        }
        
        return ($len > $min) && ($len < $max);
    }
    
    /**
     * Checks if the value is a substring of other string.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_in_string($value, $string)
    {
        return false !== strpos($string, $value);
    }
    
    /**
     * Checks if the value is contained in the array
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_in_array($value, $array)
    {
        $a = array_flip($array);
        return isset($a[$value]);
    }
    
    /**
     * Checks if the value is a valid e-mail address.
     *
     * @return bool True if the $value meets the conditions, false otherwise
     */
    public function is_email($value) {
        return preg_match('/^([a-z0-9])+([\.a-z0-9_-])*@([a-z0-9_-])+(\.[a-z0-9_-]+)*\.([a-z]{2,6})$/', $value);
    }
}
