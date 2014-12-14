<?php

namespace Themety\Content\Metabox;

class SingleField
{
    /**
     * Value
     *
     * @var mixed
     */
    protected $value;

    /**
     * Constructor
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Get Value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        $value = $this->value ?: '';
        return is_array($value) || is_object($value) ? json_encode($value) : $value;
    }
}
