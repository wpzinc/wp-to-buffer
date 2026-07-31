<?php
/**
 * Outputs the upgrade reasons to upgrade to a Pro product.
 *
 * @package WPZinc\Shared
 * @author WP Zinc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( isset( $this->base->plugin->upgrade_reasons ) && is_array( $this->base->plugin->upgrade_reasons ) && count( $this->base->plugin->upgrade_reasons ) > 0 ) {
	foreach ( $this->base->plugin->upgrade_reasons as $reasons ) {
		?>
		<div class="wpzinc-option ignore-nth-child">
			<strong><?php echo esc_html( $reasons[0] ); ?>:</strong> <?php echo esc_html( $reasons[1] ); ?>
		</div>
		<?php
	}
	?>

	<div class="wpzinc-option ignore-nth-child">
		<strong><?php esc_html_e( 'Support', 'wp-to-buffer' ); ?>:</strong> <?php esc_html_e( 'Access to one on one email support', 'wp-to-buffer' ); ?>
	</div>

	<div class="wpzinc-option ignore-nth-child">
		<strong><?php esc_html_e( 'Documentation', 'wp-to-buffer' ); ?>:</strong> <?php esc_html_e( 'Detailed documentation on how to install and configure the plugin', 'wp-to-buffer' ); ?>
	</div>

	<div class="wpzinc-option ignore-nth-child">
		<strong><?php esc_html_e( 'Updates', 'wp-to-buffer' ); ?>:</strong> <?php esc_html_e( 'Receive one click update notifications, right within your WordPress Adminstration panel', 'wp-to-buffer' ); ?>
	</div>

	<div class="wpzinc-option ignore-nth-child">
		<strong><?php esc_html_e( 'Seamless Upgrade', 'wp-to-buffer' ); ?>:</strong> <?php esc_html_e( 'Retain all current settings when upgrading to Pro', 'wp-to-buffer' ); ?>
	</div>

	<div class="wpzinc-option">
		<a href="<?php echo esc_url( $this->base->dashboard->get_upgrade_url( 'settings_footer_upgrade' ) ); ?>" class="button button-primary" rel="noopener" target="_blank">
			<?php esc_html_e( 'Upgrade Now', 'wp-to-buffer' ); ?>
		</a>
	</div>
	<?php
}
