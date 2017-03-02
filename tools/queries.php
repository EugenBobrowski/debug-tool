<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 01.02.17
 * Time: 14:20
 */
class Debug_Tool_Queries
{
    protected static $instance;

    private function __construct()
    {
        if (!defined('SAVEQUERIES') && empty($_GET['try_save_queries'])) return;

        if (defined('SAVEQUERIES') && !SAVEQUERIES) return;

        if (!defined('SAVEQUERIES')) define('SAVEQUERIES', true);

        add_filter('wp_debug_refs', array($this, 'add_ref'));
        add_action('debug_tool_assets', array($this, 'assets'));
    }

    public function assets (){
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-tablesorter', DBT_ROOT_URI . 'js/jquery.tablesorter.min.js');
    }

    public function add_ref($refs)
    {
        ob_start();
        global $wpdb;

        $i = 1;

        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-tablesorter', plugin_dir_url(__FILE__) . '../js/jquery.tablesorter.min.js');

        ?>
        <table id="dbt_queries_table" class="dbt-ref-table dbt-ref-table-queries" >
            <thead>
            <tr>
                <th><?php _e('No.', ''); ?></th>
                <th><?php _e('Request', ''); ?></th>
                <th><?php _e('ms', ''); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($wpdb->queries as $query) { ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td>
                        <pre><code><?php echo esc_html($query[0]); ?></code></pre>
                        <p><?php echo $query[2]; ?></p>
                    </td>
                    <td><?php echo number_format($query[1]*1000, 2); ?></td>
                </tr>
                <?php $i++;
            } ?>
            </tbody>

        </table>
        <script>
            (function ($) {
                $(document).ready(function () {
                    console.log($("#dbt_queries_table"));
                    $("#dbt_queries_table").tablesorter();
                });
            })(jQuery)
        </script>

        <?php
        $refs['queries'] = array(
            'title' => 'Queries' . ' (' . $wpdb->num_queries . ')',
            'content' => ob_get_clean(),
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

add_action('load_debug_tools', array('Debug_Tool_Queries', 'get_instance'));
Debug_Tool_Queries::get_instance();