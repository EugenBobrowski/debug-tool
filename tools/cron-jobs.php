<?php
/**
 * Created by PhpStorm.
 * User: eugen
 * Date: 19.01.18
 * Time: 15:46
 */

class Debug_Tool_Cron_Jobs {
	protected static $instance;

	private function __construct() {
		add_filter( 'wp_debug_refs', array( $this, 'ref' ), 11, 4 );
		add_filter( 'dbt_settings', array( $this, 'add_setting' ) );
	}

	public function add_setting( $settings ) {
		$settings ['show_cron_jobs'] = array(
			'label' => 'Show cron jobs',
			'value' => true,
		);

		return $settings;
	}

	function ref( $refs, $a, $b, $settings ) {
		if ( ! $settings['show_cron_jobs']['value'] ) {
			return $refs;
		}

		ob_start();
		$crons = _get_cron_array();
		?>
        <p>
	        <?php _e('Time now: '); ?>
	        <?php echo date("D, d M Y H:i:s"); ?>
        </p>
        <table id="dbt_cron_jobs" class="dbt-ref-table">
            <thead>
            <tr>
                <th><?php _e( 'Action', '' ); ?></th>
                <th><?php _e( 'Scheduled', '' ); ?></th>
                <th><?php _e( 'Next Run', '' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			foreach ( $crons as $timestamp => $events ) {
				foreach ( $events as $action => $hooks ) {
					foreach ( $hooks as $hook ) {
						?>
                        <tr>
                            <td class="text-left"><?php echo $action; ?></td>
                            <td><?php echo $hook['schedule']; ?></td>
                            <td><?php echo date("D, d M Y H:i:s", $timestamp); ?></td>
                        </tr>
                    <?php
					}
				}
			} ?>
            </tbody>

        </table>
        <script>

        </script>

		<?php


//		var_dump( _get_cron_array() );
//		var_dump(wp_get_schedules());


		$refs['wp_cron_jobs'] = array(
			'title'   => 'WP Cron',
			'content' => '<b>WP Cron Jobs</b><br />' . ob_get_clean(),
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

Debug_Tool_Cron_Jobs::get_instance();