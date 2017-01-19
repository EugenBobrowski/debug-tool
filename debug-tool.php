<?php

/*
Plugin Name: Debug Tool
Description: Debug only debug
Author: Eugen Bobrowski
Git URI: https://github.com/ruhanirabin/wp-optimize
Version: 1.0
*/

class Debug
{

    protected static $instance;

    private $start;
    private $stop;
    private $time;
    private $data;
    private $refs;
    private $current_filter;


    private function __construct()
    {
        if (!WP_DEBUG) return;
        add_action('plugins_loaded', array($this, 'start'), 1);
        add_action('wp_print_footer_scripts', array($this, 'stop'), 99);

        add_action('wp_enqueue_scripts', array($this, 'assets'), 1);
        add_action('login_enqueue_scripts', array($this, 'assets'), 1);
        add_action('wp_footer', array($this, 'debug_bar'), 999999);
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

//        $timestamp = (isset($this->data[$current_filter]['time'])) ?
//            number_format(($timestamp - $this->data[$current_filter]['time']) * 1000, 2):
//            $timestamp;


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
        wp_enqueue_style('wp-debug-bar', plugin_dir_url(__FILE__) . '/css/style.css', array(), 1, 'screen');
        wp_enqueue_script('wp-debug-bar', plugin_dir_url(__FILE__) . '/js/debug.js', array(), 1, true);
    }

    public function debug_bar()
    {
        wp_enqueue_style('wp-debug-bar', plugin_dir_url(__FILE__) . '/css/style.css', array(), 1, 'screen');

        global $wpdb;

        ?>
        <div id="wp-debug-bar">
            <div class="main">
                <h3 class="title">Debug bar</h3>
                <p class="time"> <span><?php echo number_format($this->time, 3); ?>s</span>Imp. time</p>
                <p class="queries"> <span><?php echo $wpdb->num_queries; ?></span> Queries</p>
            </div>
            <div class="details"></div>
            <div class="filters">
                <ul><?php
                    foreach ($this->data as $filter => $data) {
                        if (!empty($data['time_buffer'])) {
                            $data['time'] += $this->stop - $data['time_buffer'];
                            $data['queries'] += $wpdb->num_queries - $data['queries_buffer'];
                            $data['times']++;
                        }

                        echo '<li><strong>' . $filter . ':</strong> ' . number_format($data['time'] * 1000, 2) . '/' . $data['queries'] . '/' . $data['times'] . '</li>';
                    }

                    ?>
                </ul>

            </div>
            <div class="clear"></div>
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
            <a href="#" class="hide-bar">&times;</a>

        </div>

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

require_once 'functions.php';

Debug::get_instance();