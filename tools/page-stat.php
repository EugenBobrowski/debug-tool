<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 10.05.17
 * Time: 19:06
 */
class Dbt_Items_Stat
{
    protected static $instance;

    private function __construct()
    {
        add_filter('wp_debug_refs', array($this, 'ref'), 11);
    }

    function ref($refs)
    {

        global $post;

        $meta = get_post_meta($post->ID, 'dbt_');

        $refs['my_ref_id'] = array(
            'title' => 'My ref',
            'content' => '<b>My ref</b><br />' . var_export($post, true),
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

Dbt_Items_Stat::get_instance();