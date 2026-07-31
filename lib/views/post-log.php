<?php
/**
 * Outputs the Logs table when viewing/editing an individual Post.
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>
<div class="wpzinc-option">
	<div class="full">
		<table class="widefat wp-to-social-log">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Request Sent', 'wpzinc-social' ); ?></th>
					<th><?php esc_html_e( 'Action', 'wpzinc-social' ); ?></th>
					<th><?php esc_html_e( 'Profile', 'wpzinc-social' ); ?></th>
					<th><?php esc_html_e( 'Status Text', 'wpzinc-social' ); ?></th>
					<th><?php esc_html_e( 'Result', 'wpzinc-social' ); ?></th>
					<th><?php esc_html_e( 'Response', 'wpzinc-social' ); ?></th>
					<th>
						<?php
						echo esc_html(
							sprintf(
							/* translators: Social Media Service Name (Buffer, Hootsuite) */
								__( '%s: Status Created At', 'wpzinc-social' ),
								$this->base->plugin->account
							)
						);
						?>
					</th>
					<th>
						<?php
						echo esc_html(
							sprintf(
								/* translators: Social Media Service Name (Buffer, Hootsuite) */
								__( '%s: Status Scheduled For', 'wpzinc-social' ),
								$this->base->plugin->account
							)
						);
						?>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				echo $this->base->get_class( 'log' )->build_log_table_output( $log ); // phpcs:ignore WordPress.Security.EscapeOutput
				?>
			</tbody>
		</table>
	</div>
</div>
<div class="wpzinc-option">
	<div class="full">
		<a href="<?php echo esc_attr( $urls['refresh'] ); ?>" class="<?php echo esc_attr( $this->base->plugin->name ); ?>-refresh-log button" data-action="<?php echo esc_attr( $this->base->plugin->filter_name ); ?>_get_log" data-target="#<?php echo esc_attr( $this->base->plugin->name ); ?>-log">
			<?php esc_html_e( 'Refresh Log', 'wpzinc-social' ); ?>
		</a>
		<a href="<?php echo esc_attr( $urls['export'] ); ?>" class="<?php echo esc_attr( $this->base->plugin->name ); ?>-export-log button">
			<?php esc_html_e( 'Export Log', 'wpzinc-social' ); ?>
		</a>
		<a href="<?php echo esc_attr( $urls['clear'] ); ?>" class="<?php echo esc_attr( $this->base->plugin->name ); ?>-clear-log button wpzinc-button-red" data-action="<?php echo esc_attr( $this->base->plugin->filter_name ); ?>_clear_log" data-target="#<?php echo esc_attr( $this->base->plugin->name ); ?>-log" data-message="<?php esc_attr_e( 'Are you sure you want to clear the logs associated with this Post?', 'wpzinc-social' ); ?>">
			<?php esc_html_e( 'Clear Log', 'wpzinc-social' ); ?>
		</a>
	</div>
</div>
