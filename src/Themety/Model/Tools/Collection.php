<?php

namespace Themety\Model\Tools;

use Iterator;
use Countable;

class Collection implements Iterator, Countable
{
    /**
     * Models
     *
     * @var array
     */
    private $items = array();


    /**
     * Constructor
     *
     * @param array $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }


    /**
     * Get current model attribute
     */
    public function __get($name)
    {
        return $this->current()->$name;
    }

    /**
     * Call current model method
     */
    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->current(), $name], $arguments);
    }

    /**
     * Call current model static method
     */
    public static function __callStatic($name, $arguments)
    {
        return call_user_func_array([$this->current(), $name], $arguments);
    }


    
    public function count()
    {
        return count($this->items);
    }


    public function rewind()
    {
        reset($this->items);
    }


    public function current()
    {
        $item = current($this->items);
        return $item;
    }


    public function key()
    {
        $var = key($this->items);
        return $var;
    }


    public function next()
    {
        $var = next($this->items);
        return $var;
    }


    public function valid()
    {
        $key = key($this->items);
        $var = ($key !== NULL && $key !== FALSE);
        return $var;
    }
}
