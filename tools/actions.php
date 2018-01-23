<?php
/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 19.01.18
 * Time: 15:46
 */


class Debug_Tool_Actions {
	protected static $instance;

	private function __construct() {
		add_filter( 'wp_debug_refs', array( $this, 'ref' ), 11, 4 );
		add_filter( 'dbt_settings', array( $this, 'add_setting' ) );
	}

	public function add_setting( $settings ) {
		$settings ['show_actions'] = array(
			'label' => 'Show Actions',
			'value' => true,
		);

		return $settings;
	}

	function ref( $refs, $a, $b, $settings ) {
		if ( ! $settings['show_actions']['value'] ) {
			return $refs;
		}

		ob_start();
		global $wp_filter, $wp_actions;

		?>
        <p>
            Actions on this page: <?php
			foreach ( $wp_actions as $action => $times ) {
                echo ' <em>'.$action.' ('.$times.')</em> ';
			}
			?>
        </p>

        <table id="dbt_cron_jobs" class="dbt-ref-table dbt-ref-actions">
            <thead>
            <tr>
                <th><?php _e( 'Tag', '' ); ?></th>
                <th><?php _e( 'Scheduled', '' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			foreach ( $wp_filter as $tag => $hook ) {
//						?>
                <tr>
                    <td class="text-left"><?php echo $tag; ?></td>
                    <td class="text-left">
                        <table class="dbt-ref-table">
							<?php foreach ( $hook->callbacks as $priority => $callbacks ) { ?>
                                <tr>
                                    <td class="priority"><?php echo $priority; ?></td>
                                    <td><ul><?php foreach ( $callbacks as $callback_key => $callback ) {
											if ( is_array( $callback['function'] ) ) {
												$name = is_string( $callback['function'][0] ) ? $callback['function'][0] : get_class( $callback['function'][0] ) . '::<em>' . $callback['function'][1] . '</em>';
											} elseif ( is_string( $callback['function'] ) ) {
												$name = $callback['function'];
											} else {
												$name = '[Closure]';
											}
											printf( '<li id="%s">%s</li>', $callback_key, $name );

										} ?></ul>
                                    </td>

                                </tr>
							<?php }
							?></table>
                    </td>
                </tr>
				<?php
			}
			?>
            </tbody>

        </table>
        <script>

        </script>

		<?php


//		var_dump( _get_cron_array() );
//		var_dump(wp_get_schedules());


		$refs['actions'] = array(
			'title'   => 'Actions',
			'content' => '<b>Actions</b><br />' . ob_get_clean(),
		);

		return $refs;
	}

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

Debug_Tool_Actions::get_instance();