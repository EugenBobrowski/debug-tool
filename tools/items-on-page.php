<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 10.05.17
 * Time: 19:06
 */
class Debug_Tool_WP_Cache_Stat
{
    protected static $instance;

    private function __construct()
    {
        add_filter('wp_debug_refs', array($this, 'ref'), 11);
    }

    function ref($refs)
    {

        global $wp_object_cache;

        ob_start();

        $wp_object_cache->stats();

        $refs['wp_cache'] = array(
            'title' => 'My ref',
            'content' => '<b>WP Cache</b><br />' . ob_get_clean(),
        );
        return $refs;
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

Debug_Tool_WP_Cache_Stat::get_instance();