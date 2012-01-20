<?php

class PewStack
{
    public $_data;
    public $_top;
    
    const EMPTY_STACK = -1;
    
    function __construct($data = array())
    {
        $this->_data = array();
        $this->_top = self::EMPTY_STACK;
        
        if (func_num_args() > 1) {
            $data = func_get_args();
        }
        
        foreach ($data as $item) {
            $this->put($item);
        }
    }
    
    public function put($item)
    {
        $items = func_get_args();
        
        foreach ($items as $item) {
            if ($item) {
                $this->_data[++$this->_top] = $item;
            }
        }
    }
    
    public function current()
    {
        return $this->void() ? null : $this->_data[$this->_top];
    }

    public function pop()
    {
        return $this->void() ? null : $this->_data[$this->_top--];
    }
    
    public function skip($items = 1)
    {
        $this->_top -= $items;
        
        if ($this->void()) {
            $this->_top = -1;
        }
        
        return $this->pop();
    }
    
    public function size()
    {
        return $this->_top + 1;
    }
    
    public function void()
    {
        return $this->_top === self::EMPTY_STACK;
    }
    
    public function is_current($item)
    {
        return $item === $this->current();
    }
}

/* TESTS */

$s = new PewStack(array('uno', 'dos', 'tres'));
echo '#1 Must print "tres": ' . $s->pop() . PHP_EOL;
$s->put('cuatro');
echo '#2 Must print "cuatro": ' . $s->pop() . PHP_EOL;
$s->put('cinco');
$s->put('seis');
echo '#3 Must print "cinco": ' . $s->skip() . PHP_EOL;
echo '#4 Must print "dos": ' . $s->pop() . PHP_EOL;
$s->put('siete');
$s->put('ocho');
$s->put('nueve');
$s->put('diez');
echo '#5 Must print "siete": ' . $s->skip(3) . PHP_EOL;
echo '#6 Must print "1": ' . $s->size() . PHP_EOL;
$s->skip(1);
echo "#7 Must print NULL: ";
var_dump($s->pop());
echo "#8 Must print true: ";
var_dump($s->void());
