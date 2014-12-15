<?php

namespace Themety\Model\Tools;

use Exception;

class QueryBulder
{

    /**
     * Allowed query values
     *
     * @var array
     */
    protected static $allowedQueryVars;

    /**
     * Inputed query vars
     *
     * @var array
     */
    protected $queryVars = array();

    /**
     * Themety Model
     *
     * @var \Themety\Model\Base
     */
    protected $queryModel;


    public function __construct(\Themety\Model\Base $model)
    {
        $this->queryModel = $model;
    }


    /**
     * Fill vars
     */
    public function __call($name, $arguments)
    {
        if (count($arguments) !== 1) {
            throw new Exception("There should be a single argument");
        }

        $this->queryVars[$name] = $arguments[0];
        return $this;
    }


    /**
     * Returns Model
     *
     * @return \Themety\Model\Base
     */
    public function get()
    {
        return $this->queryModel->query($this->queryVars);
    }
}
