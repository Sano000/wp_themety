<?php

namespace Themety\Model;

use WP_Query;

use Themety\Model\Tools\QueryBulder;
use Themety\Model\Tools\Collection;

class Base {

    /**
     * @var string
     */
    protected $modelClass = 'Themety\Model\Tools\PostModel';

    /**
     * Query logs
     *
     * @var array
     */
    protected static $logs = [];


     /**
     *  WP_Query instance
     *
     * @var WP_Query
     */
    protected $query;

    /**
     * Post count
     *
     * @var integer
     */
    protected $post_count = false;

    /**
     * Current model's index
     *
     * @var integer
     */
    protected $index = false;

    /**
     * Models
     *
     * @var array
     */
    protected $models = [];


    /**
     * Default query vars
     *
     * @var array
     */
    protected $defaults = array();

    /**
     * Construction
     *
     * @param mixed WP_Query params
     */
    public function __construct($args = null)
    {
        $args && ($this->query($args));
    }


    /**
     * Call QueryBuilder method
     *
     * @param type $name
     * @param type $arguments
     * @return type
     */
    public static function __callStatic($name, $arguments)
    {
        $class = get_called_class();
        $bulder = new QueryBulder(new $class);
        return call_user_func_array([$bulder, $name], $arguments);
    }


    /**
     * Get
     *
     * @param mixed $args query params or post ID
     * @return self
     */
    public static function get($args = array())
    {
        if (is_numeric($args)) {
            $args = ['p' => $args];
        }

        $class = get_called_class();
        $model = new $class;
        return $model->query($args);
    }


    /**
     * Create new query
     *
     * @param array $args Query arguments
     * @return self
     */
    public function query(array $args)
    {
        $args = $this->updateQueryVars($args);
        $this->query = new WP_Query($args);

        self::$logs[get_called_class()] = [
            'request'       => $this->query->request,
            'params'        => $args,
            'found_posts'   => $this->query->found_posts,
        ];

        $items = [];
        foreach($this->query->get_posts() as $post) {
            $items[] = new $this->modelClass($post);
        }

        $collection = new Collection($items);
        return $collection;
    }

    /**
     * Reset query
     */
    public function reset()
    {
        wp_reset_postdata();
    }


    /**
     * Update query vars
     *
     * @param array $args Query vars
     */
    protected function updateQueryVars(array $args)
    {
        // no limit
        isset($args['posts_per_page']) || $args['posts_per_page'] = -1;

        // if p (ID) is set, search by all all content types
        if (!isset($args['post_type']) && isset($args['p'])) {
            $args['post_type'] = get_post_types();
        }


        // all public page types except "attachment"
        if (!isset($args['post_type'])) {
            $postTypes = get_post_types([
                'public' => true
            ]);
            $args['post_type'] = array_diff($postTypes, ['attachment']);
        }

        $args = array_merge($this->defaults, $args);
        empty($this->defaults['post_type']) || ($args['post_type'] = $this->defaults['post_type']);
        return $args;
    }


    /**---------------------------------------------------------------------------------------------
     *                                                                  LOGS
     ---------------------------------------------------------------------------------------------*/
    /**
     * Show query log
     *
     * @param string    $field
     * @param boolean   $thisClassOnly
     * @return array
     */
    public static function getQueryLog($field = null, $thisClassOnly = false) {
        $result = [];

        foreach (self::$logs as $class => $value) {
            if ($thisClassOnly && $class !== get_called_class()) {
                continue;
            }

            if ($field && !isset($value[$field])) {
                continue;
            }

            $result[] = $field ? $value[$field] : $value;
        }

        return $result;
    }
}
