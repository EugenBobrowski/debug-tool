<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 10.05.17
 * Time: 19:06
 */
class Debug_Tool_var_dump
{
    protected static $instance;
    private $var_dump;

    private function __construct()
    {
        add_filter('wp_debug_refs', array($this, 'ref'), 11, 4);
        add_filter('dbt_settings', array($this, 'add_setting'));
        add_action('dbt_var_dump', array($this, 'var_dump'));
        if(WP_DEBUG) {
	        add_action('wp_ajax_nopriv_dbt_phpinfo', array($this, 'info'));
        }
    }

    public function add_setting ($settings) {
    	$settings ['show_var_dump'] = array(
    		'label' => 'Show var_dump',
		    'value' => false,
	    );
    	return $settings;
    }

    function ref($refs, $a, $b, $settings)
    {
		if (!$settings['show_var_dump']['value']) return $refs;

        $refs['var_dump'] = array(
            'title' => 'var_dump()',
            'content' => $this->var_dump,
        );
        return $refs;
    }
    public function var_dump($var) {
		ob_start();
		var_dump($var);
		$this->var_dump .= ob_get_clean();
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

Debug_Tool_var_dump::get_instance();