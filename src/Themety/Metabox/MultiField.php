<?php

namespace Themety\Metabox;

class MultiField
{
    /**
     * Value
     *
     * @var mixed
     */
    protected $values;

    /**
     * Constructor
     *
     * @param mixed $value
     */
    public function __construct($values)
    {
        $this->values = $values;
    }

    /**
     * Get Value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->values;
    }

    public function __get($name)
    {
        if (isset($this->values[$name])) {
            return $this->values[$name];
        }
        return null;
    }

    public function __toString()
    {
        $value = $this->values ?: '';
        return is_array($value) || is_object($value) ? json_encode($value) : $value;
    }
}
