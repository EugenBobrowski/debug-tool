<?php

/*
Plugin Name: Debug Tool
Description: Show helpful debug bar at front and admin side. Tool for developers.
Author: Eugen Bobrowski
Git URI:
Version: 1.0
*/

class Debug_Tool
{

    protected static $instance;

    private $start;
    private $stop;
    private $time;
    private $data;
    private $refs;


    private function __construct()
    {
        if (!WP_DEBUG && !current_user_can('manage_options')) return;

        $this->start();
        add_action('wp_print_footer_scripts', array($this, 'stop'), 99);
        add_action('admin_footer', array($this, 'stop'), 99);

        add_action('wp_enqueue_scripts', array($this, 'assets'), 1);
        add_action('admin_enqueue_scripts', array($this, 'assets'), 1);
        add_action('login_enqueue_scripts', array($this, 'assets'), 1);

        add_action('wp_footer', array($this, 'debug_bar'), 999999);
        add_action('login_footer', array($this, 'debug_bar'), 999999);
        add_action('admin_footer', array($this, 'debug_bar'), 999999);

        add_action('check_segment', array($this, 'check_segment'), 10, 2);


        $this->data = array();
        add_action('init', array($this, 'add_filters'), 1);
    }

    public function add_filters()
    {

        foreach (array('the_content') as $filter) {
            add_filter($filter, array($this, 'check_filter'), 0);
            add_filter($filter, array($this, 'check_filter'), 99999);
        }
    }

    public function check_filter($input)
    {
        global $wp_current_filter;

        $current_filter = $wp_current_filter;
        $current_filter = array_pop($current_filter);

        $this->check_segment($current_filter);

        return $input;
    }

    public function check_segment($name, $is_done = null)
    {
        global $wpdb;


        $current_filter = $name;

        if (!isset($this->data[$current_filter])) {
            $this->data[$current_filter] = array(
                'time' => 0,
                'time_buffer' => 0,
                'queries' => 0,
                'queries_buffer' => 0,
                'times' => 0,
            );
        }

        $timestamp = $this->microtime_float();

        if ($is_done) {
            $this->data[$current_filter]['time'] += $timestamp - $this->start;
            $this->data[$current_filter]['queries'] += $wpdb->num_queries;
            $this->data[$current_filter]['times']++;
            return;
        }

        if (empty($this->data[$current_filter]['time_buffer'])) {
            $this->data[$current_filter]['time_buffer'] = $timestamp;
            $this->data[$current_filter]['queries_buffer'] = $wpdb->num_queries;
        } else {
            $this->data[$current_filter]['time'] += $timestamp - $this->data[$current_filter]['time_buffer'];
            $this->data[$current_filter]['queries'] += $wpdb->num_queries - $this->data[$current_filter]['queries_buffer'];
            $this->data[$current_filter]['times']++;

            $this->data[$current_filter]['time_buffer'] = 0;
            $this->data[$current_filter]['queries_buffer'] = 0;
        }

    }

    public function start()
    {
        $this->start = $this->microtime_float();
    }

    public function stop()
    {
        $this->stop = $this->microtime_float();
        $this->time = $this->stop - $this->start;
    }

    public function assets()
    {
        wp_enqueue_style('wp-debug-bar', plugin_dir_url(__FILE__) . '/css/style.css', array(), 2, 'screen');
        wp_enqueue_script('wp-debug-bar', plugin_dir_url(__FILE__) . '/js/debug.js', array(), 2, true);

        do_action('debug_tool_assets');
    }

    public function debug_bar()
    {
        global $wpdb;

        $memory = memory_get_usage ( false );
        $memory_unit = '';

        if ($memory > 1000) {
            $memory = $memory / 1000;
            $memory_unit = 'K';
        }
        if ($memory > 1000) {
            $memory = $memory / 1000;
            $memory_unit = 'M';
        }


        ?>
        <div id="wp-debug-bar" <?php echo ((current_user_can('manage_options') && !WP_DEBUG) || is_admin()) ? 'style="display: none;"' : ''; ?>>
            <h3 class="title">Debug bar</h3>
            <div class="main">
                <p class="time"> <span><?php echo number_format($this->time, 3); ?>s</span>Imp. time   <span class="circle"></span></p>
                <p class="queries"> <span><?php echo $wpdb->num_queries; ?></span> Queries<span class="circle"></span></p>
                <p class="memory"> <span><?php echo number_format($memory, 1) . $memory_unit; ?></span> Memory <span class="circle"></span></p>
            </div>
            <div class="details"></div>
            <div class="filters">
                <ul><?php
                    foreach ($this->data as $filter => $data) {
                        $asterisk = '';
                        if (!empty($data['time_buffer'])) {
                            $data['time'] += $this->stop - $data['time_buffer'];
                            $data['queries'] += $wpdb->num_queries - $data['queries_buffer'];
                            $data['times']++;
                            $asterisk = '*';
                        }

                        echo '<li><strong>' . $asterisk . $filter . ':</strong> ' . number_format($data['time'] * 1000, 2) . '/' . $data['queries'] . '/' . $data['times'] . '</li>';
                    }

                    ?>
                </ul>

            </div>
            <div class="clear"></div>

            <a href="#" class="hide-bar">&times;</a>

            <?php $this->refs = apply_filters('wp_debug_refs', array()); ?>
            <ul class="refs">
                <?php
                foreach ($this->refs as $ref_key => $ref) {
                    echo '<li><a href="#'.$ref_key.'" >'.$ref['title'].'</a></li>';
                }
                ?>
            </ul>

            <div class="clear"></div>

            <div class="refs-content">
                <div class="bg"></div>
                <?php
                foreach ($this->refs as $ref_key => $ref) {
                    echo '<div id="'.$ref_key.'" class="ref-item">'.$ref['content'].'</div>';
                }
                ?>
            </div>

        </div>

        <!-- Debug tool scripts -->
        <?php

        do_action('debug_tool_print_scripts');

        ?>
        <!-- / Debug tool scripts -->
        <?php

    }

    private function microtime_float()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float)$usec + (float)$sec);
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

require_once 'tools/errors.php';

add_action('plugins_loaded', array('Debug_Tool', 'get_instance'), 1);