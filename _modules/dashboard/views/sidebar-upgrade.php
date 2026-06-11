<?php
/**
 * Outputs settings screen sidebar for free plugins.
 *
 * @package WPZincDashboardWidget
 * @author WP Zinc
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="postbox">
	<h3 class="hndle">
		<?php echo esc_html( $upgrade_title ); ?>
	</h3>

	<div class="wpzinc-option">
		<p class="description">
			<?php echo $upgrade_content; // phpcs:ignore WordPress.Security.EscapeOutput ?>
		</p>
	</div>

	<?php
	if ( isset( $upgrade_url ) && isset( $upgrade_button_text ) ) {
		?>
		<div class="wpzinc-option">
			<a href="<?php echo esc_url( $upgrade_url ); ?>" class="button button-primary" target="_blank">
				<?php echo esc_html( $upgrade_button_text ); ?>
			</a>
		</div>
		<?php
	}
	?>
</div>
