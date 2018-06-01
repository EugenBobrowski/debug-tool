<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 10.05.17
 * Time: 19:06
 */
class Debug_Tool_Image_Puller {
	protected static $instance;

	private function __construct() {
		add_filter( 'wp_debug_refs', array( $this, 'ref' ), 11, 4 );
		add_filter( 'dbt_settings', array( $this, 'add_setting' ) );
		add_action( 'wp_ajax_nopriv_dbt_pull_image', array( $this, 'ajax_pull' ) );
		add_action( 'wp_ajax_dbt_pull_image', array( $this, 'ajax_pull' ) );
	}

	public function add_setting( $settings ) {
		$settings ['show_image_puller']      = array(
			'label' => 'Use Image Puller',
			'value' => false,
		);
		$settings ['image_puller__prod_url'] = array(
			'label' => 'Production URL (replace ' . get_site_url() . ')',
			'type'  => 'text',
			'value' => '',
		);

		return $settings;
	}

	function ref( $refs, $a, $b, $settings ) {
		if ( ! $settings['show_image_puller']['value'] ) {
			return $refs;
		}

		ob_start();

		wp_enqueue_script( 'jquery' );
		?>
        <a href="#" class="search-images">Search</a> | <a href="#" class="pull-images">Pull</a>
        <ul class="images-list">

        </ul>
        <script>
            (function ($) {
                var _ = {};

                _.init = function () {
                    _.sets = {
                        ajax: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                        action: 'dbt_pull_image',
                        nonce: '<?php echo wp_create_nonce( 'dbt_page_stat' ); ?>',
                        loc: '<?php echo get_site_url(); ?>',
                        prod: '<?php echo $settings['image_puller__prod_url']['value']; ?>'
                    };
                    _.$ = {
                        body: $('body'),
                        search_button: $('.search-images'),
                        pull_button: $('.pull-images'),
                        list: $('.images-list')
                    };
                    _.$.search_button.click(_.search);
                    _.$.pull_button.click(_.pull_all);

                };

                _.search = function (e) {
                    e.preventDefault();
                    _.$.list.html('');
                    _.$.body.find('img').each(function () {
                        var $this = $(this), url = $this.attr('src');

                        $.ajax({
                            url: url,
                            statusCode: {
                                404: function () {
                                    _.$.list.append('<li data-missed="' + url + '" data-source="' + url.replace(_.sets.loc, _.sets.prod) + '"><img src="' + url.replace(_.sets.loc, _.sets.prod) + '" alt="">' + url + '</li>');
                                }
                            }
                        });
                    })
                };

                _.pull_all = function (e) {
                    e.preventDefault();
                    _.pull(_.$.list.find('li:first'));
                };
                _.pull = function ($item) {


                    $.post(_.sets.ajax, {
                        action: _.sets.action,
                        source: $item.data('source'),
                        missed: $item.data('missed')
                    }, function (response) {
                        console.log(response);
                        $item.addClass('done').hide('slow');
                        var $next = $item.next();
                        if ($next.length) _.pull($next);
                    });
                };

                $(document).ready(_.init);

            })(jQuery)
        </script>

		<?php

		$refs['dbt_image_puller'] = array(
			'title'   => 'Pull images',
			'content' => '<h3>Pull images</h3><br />' . ob_get_clean(),
		);

		return $refs;
	}

	public function ajax_pull() {

		var_dump( $_POST );

		$path = str_replace(get_site_url() . '/', ABSPATH, $_POST['missed']);
		$path = explode('?', $path);
		$path = $path[0];
		var_dump($path);
		file_put_contents($path, file_get_contents($_POST['source']));


		exit();

	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Debug_Tool_Image_Puller::get_instance();