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

    }

    public function add_ref($refs)
    {
        ob_start();
        global $wpdb;

        $i = 1;

        ?>
        <table id="aaff_notices_table" class="dbt-ref-table" data-page="1" data-full="<?php echo $count; ?>"
               data-per_page="">
            <?php foreach ($wpdb->queries as $query) { ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td>
                        <code><?php echo $query[0]; ?></code>
                        <p><?php echo $query[2]; ?></p>
                    </td>
                    <td><?php echo number_format($query[1], 4); ?></td>
                </tr>
                <?php $i++;
            } ?>
        </table>


        <?php
        $refs['errors'] = array(
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