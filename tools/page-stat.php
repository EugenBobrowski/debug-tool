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

        $stat = get_option('pages_stat', array());

        $page = $this->get_page();

        ob_start();

        ?>
        <h3>Page Stat</h3>

        <p>
            <?php if (array_key_exists($page, $stat)) { ?>
                <a href="#">Remove this page</a> |
                <a href="#">Add notice</a>
            <?php } else { ?>
                <a href="#add-page-to-stat" data-page="<?php echo esc_attr($page); ?>">Add this page</a>
            <?php } ?>
        </p>



        <?php

        $refs['page_stat'] = array(
            'title' => 'Page Stat',
            'content' => ob_get_clean(),
        );
        return $refs;
    }

    private function get_page() {
        return $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    public function ajax() {
        var_dump($_POST);
        exit;
    }

    public function js () {
        ?>
        <script>

            (function ($) {
                var ajax = {
                    url: '<?php echo admin_url('admin-ajax.php'); ?>',
                    action: 'dbt_page_stat',
                    nonce: '<?php echo wp_create_nonce('dbt_page_stat'); ?>'
                };

                $(document).ready(function () {
                    $('body').on('click', '[href="#add-page-to-stat"]', function (e) {
                        e.preventDefault();
                        var $this = $(this);

                        $.post(ajax.url, {
                            action: ajax.action,
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