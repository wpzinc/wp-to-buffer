<?php
/**
 * Defines functions that are called by WordPress' Cron.
 *
 * @package WP_To_Buffer
 * @author WP Zinc
 */

/**
 * Define the WP Cron function to perform the log cleanup
 *
 * @since   3.9.8
 */
function wp_to_buffer_log_cleanup_cron() {

	// Initialise Plugin.
	$wp_to_buffer = WP_To_Buffer::get_instance();
	$wp_to_buffer->initialize();

	// Call CRON Log Cleanup function.
	$wp_to_buffer->get_class( 'cron' )->log_cleanup();

	// Shutdown.
	unset( $wp_to_buffer );

}
add_action( 'wp_to_buffer_log_cleanup_cron', 'wp_to_buffer_log_cleanup_cron' );

/**
 * Define the WP Cron function to perform the Media Library cleanup
 * of Text to Image generations
 *
 * @since   4.1.0
 */
function wp_to_buffer_media_cleanup_cron() {

	// Initialise Plugin.
	$wp_to_buffer = WP_To_Buffer::get_instance();
	$wp_to_buffer->initialize();

	// Call Media Cleanup function.
	$wp_to_buffer->get_class( 'media_library' )->cleanup();

	// Shutdown.
	unset( $wp_to_buffer );

}
add_action( 'wp_to_buffer_media_cleanup_cron', 'wp_to_buffer_media_cleanup_cron' );
