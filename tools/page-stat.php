<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 10.05.17
 * Time: 19:06
 */
class Debug_Tool_Page_Stat
{
    protected static $instance;

    private function __construct()
    {
        add_filter('wp_debug_refs', array($this, 'ref'), 11, 3);
        add_action('debug_tool_print_scripts', array($this, 'js'));
        add_action('wp_ajax_dbt_page_stat', array($this, 'ajax'));
    }

    function ref($refs, $data, $totals)
    {

        $stat = get_option('dbt_pages_stat', array());

        $current_page = $this->get_page();

        ob_start();

        ?>
        <h3>Page Stat</h3>

        <p>
            <?php if (array_key_exists($current_page, $stat)) {
                $stat[$current_page][time()] = $totals;
                $current_page_stat = $stat[$current_page];
                update_option('dbt_pages_stat', $stat);
                ?>
                <a href="#clear-stat" data-do="clear-page-stat" data-page="<?php echo esc_attr($current_page); ?>"
                   class="dbt-page-stat">Clear page stat</a> |
                <a href="#remove-page" data-do="remove-page" data-page="<?php echo esc_attr($current_page); ?>"
                   class="dbt-page-stat">Remove this page</a>

            <?php } else {
                $current_page_stat = array();
                ?>
                <a href="#add-page-to-stat" data-do="add-page" data-page="<?php echo esc_attr($current_page); ?>"
                   class="dbt-page-stat">Add this page</a>
            <?php } ?>
        </p>

        <table class="dbt-ref-table">
            <thead>
            <th>Date</th>
            <th>Time</th>
            <th>Queries</th>
            <th>Memory</th>
            </thead>
            <tbody>
            <?php foreach ($current_page_stat as $timestamp => $timestat) { ?>
                <tr data-timestamp="<?php echo $timestamp; ?>">
                    <td><?php echo date("Y-m-d H:i:s", $timestamp); ?></td>
                    <td><?php echo number_format($timestat['time'], 2); ?></td>
                    <td><?php echo $timestat['queries']; ?></td>
                    <td><?php echo number_format($timestat['memory'], 2) . $timestat['memory_unit']; ?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>

        <h4>
            <?php _e('Monitored pages', 'dbt'); ?>
        </h4>

        <ul>
            <?php foreach ($stat as $page => $page_stat): ?>
                <li><a href="<?php echo '//' . $page; ?>"><?php echo $page; ?></a></li>
            <?php endforeach; ?>
        </ul>

        <?php

        $refs['page_stat'] = array(
            'title' => 'Page Stat',
            'content' => ob_get_clean(),
        );
        return $refs;
    }

    private function get_page()
    {
        return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function ajax()
    {
        check_ajax_referer('dbt_page_stat');

        $do = trim($_POST['do']);
        $page = trim($_POST['page']);

        switch ($do) {
            case 'add-page':
                $stat = get_option('dbt_pages_stat', array());
                if (!isset($stat[$page])) $stat[$page] = array();
                update_option('dbt_pages_stat', $stat);
                break;
            case 'remove-page':
                $stat = get_option('dbt_pages_stat', array());
                if (isset($stat[$page])) unset($stat[$page]);
                update_option('dbt_pages_stat', $stat);
                break;
            case 'clear-page-stat':
                $stat = get_option('dbt_pages_stat', array());
                if (isset($stat[$page])) $stat[$page] = array();
                update_option('dbt_pages_stat', $stat);
                break;
        }

        exit;
    }

    public function js()
    {
        ?>
        <script>

            (function ($) {
                var ajax = {
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    action: 'dbt_page_stat',
                    nonce: '<?php echo wp_create_nonce('dbt_page_stat'); ?>'
                };

                $(document).ready(function () {
                    $('body').on('click', '.dbt-page-stat', function (e) {
                        e.preventDefault();
                        var $this = $(this);

                        $.post(ajax.url, {
                            action: ajax.action,
                            _wpnonce: ajax.nonce,
                            do: $this.data('do'),
                            page: $this.data('page')
                        }, function (response) {
                            console.log(response);
                        });
                    })

                });
            })(jQuery)
        </script>
        <?php
    }

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}

Debug_Tool_Page_Stat::get_instance();