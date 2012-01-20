<?php

function assertEquality($value1, $value2)
{
    return $value1 === $value2;
}

function assertEquivalence($value1, $value2)
{
    return $value1 == $value2;
}

function assertTruthy($value)
{
    return $value;
}

function assertTrue($value)
{
    return $value == true;
}

function assertReallyTrue($value)
{
    return $value === true;
}

function assertNumeric($value)
{
    return is_numeric($value);
}

function assertExists($value)
{
    return isset($value);
}

function assertNotExists($value)
{
    return !isset($value);
}

function assertNull($value)
{
    return is_null($value);
}

function assertEmpty($value)
{
    if (is_array($value)) {
        return count($value) === 0;
    }
    
    if (is_string($value)) {
        return strlen($value) === 0;
    }
    
    if (is_numeric($value)) {
        return false;
    }
    
    if (is_null($value)) {
        return true;
    }
    
    return empty($value);
}