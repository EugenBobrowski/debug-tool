<?php

/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 10.05.17
 * Time: 19:06
 */
class Debug_Tool_Plugin_Disactivator {
	protected static $instance;


	private function __construct() {
		add_filter( 'wp_debug_refs', array( $this, 'ref' ), 11, 4 );
		add_filter( 'dbt_settings', array( $this, 'add_setting' ) );
		add_action( 'wp_ajax_dbt_plugins_disactive', array( $this, 'ajax_disactive' ) );
	}

	public function add_setting( $settings ) {
		$settings ['show_plugin_disactivator']                  = array(
			'label' => 'Use Plugin Disactivator',
			'value' => false,
		);
		$settings ['plugin_disactivator__plugins_active_force'] = array(
			'label' => 'Plugins active force',
			'type'  => 'text',
			'value' => '',
		);

		return $settings;
	}

	function ref( $refs, $a, $b, $settings ) {
		if ( ! $settings['show_plugin_disactivator']['value'] ) {
			return $refs;
		}

		ob_start();

		wp_enqueue_script( 'jquery' );
		?>
        <form id="dbt_disactive_plugins_form" action="">
            <input type="hidden" name="action" value="dbt_plugins_disactive">
            <ol class="dbt-plugins-list">
				<?php $this->plugins_list(); ?>
            </ol>
            <button id="dbt_plugin_disactive">Set active</button>
            <a href="#" class="check-all">Check All</a>
            <a href="#" class="uncheck-all">Uncheck All</a>
        </form>


        <script>
            (function ($) {
                var _ = {};

                _.init = function () {
                    _.sets = {
                        ajax: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                        action: 'dbt_pluginsa_disactive',
                        nonce: '<?php echo wp_create_nonce( 'dbt_page_stat' ); ?>',
                        loc: '<?php echo get_site_url(); ?>',
                        prod: '<?php echo $settings['image_puller__prod_url']['value']; ?>'
                    };
                    _.$ = {
                        body: $('body'),
                        set_button: $('#dbt_plugin_disactive'),
                        form: $('#dbt_disactive_plugins_form'),
                        list: $('.images-list')
                    };
                    _.$.form.submit(_.disactive);
                    _.$.form.on('click', '.check-all', _.check_all);
                    _.$.form.on('click', '.uncheck-all', _.uncheck_all);

                };

                _.disactive = function (e) {
                    e.preventDefault();

                    $.post(_.sets.ajax, $(this).serialize(), function (response) {
                        console.log($(this).find('.dbt-plugins-list'));
                        $(this).find('.dbt-plugins-list').html(response);
                    });
                };

                _.check_all = function (e) {
                    e.preventDefault();
                    console.log(_.$.form.find('input[type="checkbox"]'));
                    _.$.form.find('input[name="plugins[]"]').prop('checked', true);
                };
                _.uncheck_all = function (e) {
                    e.preventDefault();
                    _.$.form.find('input[type="checkbox"]').prop('checked', false);
                };
                _.pull = function ($item) {


                };

                $(document).ready(_.init);

            })(jQuery)
        </script>

		<?php

		$refs['dbt_plugin_disactivator'] = array(
			'title'   => 'Plugin disactivator',
			'content' => '<h3>Plugin disactivator</h3><br />' . ob_get_clean(),
		);

		return $refs;
	}

	private function plugins_list () {

		$native_active_plugins = (array) get_option( 'dbt_native_active_plugins', array() );

		$active_plugins = (array) get_option( 'active_plugins', array() );

		$native_active_plugins = ( empty( $native_active_plugins ) ) ? $active_plugins : $native_active_plugins;

		foreach ( $native_active_plugins as $slug ) {
			?>
            <li>
                <label><input name="plugins[]" type="checkbox"
                             value="<?php echo $slug; ?>" <?php checked( in_array( $slug, $active_plugins ) ); ?> > <?php echo $slug; ?>
                </label>
            </li>
			<?php
		} ?>
        <?php
    }

	public function ajax_disactive() {

		if ( ! current_user_can( 'manage_options' ) ) {
			exit();
		}

		$native_active_plugins = (array) get_option( 'dbt_native_active_plugins', array() );

		$active_plugins = (array) get_option( 'active_plugins', array() );

		$new = $_POST['plugins'];

		if (!empty($_POST['activate']) && $_POST['activate'] == 'all' ) {
		    $new = $native_active_plugins;
        }

		if ( empty( $native_active_plugins ) ) {
			update_option( 'dbt_native_active_plugins', $active_plugins );
		}

		if ( count(array_diff($native_active_plugins, $new )) == 0 ) {
			delete_option( 'dbt_native_active_plugins' );
		}

		update_option( 'active_plugins', $new );

		$this->plugins_list();

		exit();

	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Debug_Tool_Plugin_Disactivator::get_instance();