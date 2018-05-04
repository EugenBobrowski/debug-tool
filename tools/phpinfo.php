<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 10.05.17
 * Time: 19:06
 */
class Debug_Tool_PHP_Info
{
    protected static $instance;

    private function __construct()
    {
        add_filter('wp_debug_refs', array($this, 'ref'), 11, 4);
        add_filter('dbt_settings', array($this, 'add_setting'));
        add_action('wp_ajax_dbt_phpinfo', array($this, 'info'));
        if(WP_DEBUG) {
	        add_action('wp_ajax_nopriv_dbt_phpinfo', array($this, 'info'));
        }
    }

    public function add_setting ($settings) {
    	$settings ['show_phpinfo'] = array(
    		'label' => 'Show phpinfo()',
		    'value' => false,
	    );
    	return $settings;
    }

    function ref($refs, $a, $b, $settings)
    {
		if (!$settings['show_phpinfo']['value']) return $refs;

        $refs['phpinfo'] = array(
            'title' => 'phpinfo()',
            'content' => '<iframe src="'.admin_url('admin-ajax.php?action=dbt_phpinfo').'" frameborder="0" scrolling="no" onload="resizeIframe(this)" style="width: 100%"></iframe><script>
  function resizeIframe(obj) {obj.style.height = obj.contentWindow.document.body.scrollHeight + \'px\';}
</script>',
        );
        return $refs;
    }
    public function info() {
	    phpinfo();
	    wp_die();
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

Debug_Tool_PHP_Info::get_instance();