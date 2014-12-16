<?php

namespace Themety\Tools;

class Options
{

    private static $themety = array();

    /**
     * Set a option to the wp_themety
     *
     * @param string $key
     * @param mixed $value
     */
    public static function setThemety($key, $value)
    {
        empty(self::$themety) && (self::$themety = get_option('wp_themety'));
        self::$themety[$key] = $value;

        update_option('wp_themety', self::$themety);
    }


    /**
     * Get a option from the wp_themety
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getThemety($key, $default = null)
    {
        empty(self::$themety) && (self::$themety = get_option('wp_themety'));
        return empty(self::$themety[$key]) ? $default : self::$themety[$key];
    }


    /**
     * Delete wp_themety option
     */
    public static function deleteThemety()
    {
        delete_option('wp_themety');
    }
}
