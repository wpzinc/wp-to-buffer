<?php
/**
 * Yoast SEO Plugin Class.
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 */

/**
 * Provides compatibility with Yoast SEO
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 * @version 4.3.8
 */
class WP_To_Social_Pro_Yoast_SEO {

	/**
	 * Holds the base object.
	 *
	 * @since   4.3.8
	 *
	 * @var     object
	 */
	public $base;

	/**
	 * Constructor
	 *
	 * @since   4.3.8
	 *
	 * @param   object $base    Base Plugin Class.
	 */
	public function __construct( $base ) {

		// Store base class.
		$this->base = $base;

		// Register Status Tags.
		add_filter( $this->base->plugin->filter_name . '_get_tags', array( $this, 'register_status_tags' ), 10, 1 );

		// Replace Tags with Values.
		add_filter( $this->base->plugin->filter_name . '_publish_get_all_possible_searches_replacements', array( $this, 'register_searches_replacements' ), 10, 3 );

		// Fetch OpenGraph title and excerpt for Link Status Types.
		add_filter( $this->base->plugin->filter_name . '_publish_get_opengraph_title', array( $this, 'get_opengraph_title' ), 10, 2 );
		add_filter( $this->base->plugin->filter_name . '_publish_get_opengraph_excerpt', array( $this, 'get_opengraph_excerpt' ), 10, 2 );

	}

	/**
	 * Defines Dynamic Status Tags that can be inserted into status(es) for the given Post Type.
	 * These tags are also added to any 'Insert Tag' dropdowns.
	 *
	 * @since   4.3.8
	 *
	 * @param   array $tags       Tags.
	 * @return  array               Tags
	 */
	public function register_status_tags( $tags ) {

		// Bail if Yoast SEO isn't active.
		if ( ! $this->is_active() ) {
			return $tags;
		}

		// Register Status Tags.
		return array_merge(
			$tags,
			array(
				'yoast_seo' => array(
					'{yoast_seo_meta_title}'             => __( 'Meta Title', 'wp-to-buffer' ),
					'{yoast_seo_meta_description}'       => __( 'Meta Description', 'wp-to-buffer' ),
					'{yoast_seo_twitter_title}'          => __( 'Twitter Title', 'wp-to-buffer' ),
					'{yoast_seo_twitter_description}'    => __( 'Twitter Description', 'wp-to-buffer' ),
					'{yoast_seo_open_graph_title}'       => __( 'Facebook Title', 'wp-to-buffer' ),
					'{yoast_seo_open_graph_description}' => __( 'Facebook Description', 'wp-to-buffer' ),
				),
			)
		);

	}

	/**
	 * Registers any additional status message tags, and their Post data replacements, that are supported.
	 *
	 * @since   4.3.8
	 *
	 * @param   array   $searches_replacements  Registered Supported Tags and their Replacements.
	 * @param   WP_Post $post                   WordPress Post.
	 * @param   WP_User $author                 WordPress User (Author of the Post).
	 * @return  array                               Registered Supported Tags and their Replacements
	 */
	public function register_searches_replacements( $searches_replacements, $post, $author ) {

		// Bail if Yoast SEO isn't active.
		if ( ! $this->is_active() ) {
			return $searches_replacements;
		}

		// Register Tags and their replacement values.
		return array_merge( $searches_replacements, $this->get_searches_replacements( $post, $author ) );

	}

	/**
	 * Returns the OpenGraph title to use for the given post.
	 *
	 * @since   6.0.5
	 *
	 * @param   string  $title      Post Title.
	 * @param   WP_Post $post       WordPress Post.
	 * @return  string
	 */
	public function get_opengraph_title( $title, $post ) {

		// Bail if Yoast SEO isn't active.
		if ( ! $this->is_active() ) {
			return $title;
		}

		$searches_replacements = $this->get_searches_replacements( $post, wp_get_current_user() );

		// If the OpenGraph title is set, use it.
		if ( ! empty( $searches_replacements['yoast_seo_open_graph_title'] ) ) {
			return $searches_replacements['yoast_seo_open_graph_title'];
		}

		// If the OpenGraph title is not set, use the meta title.
		if ( ! empty( $searches_replacements['yoast_seo_meta_title'] ) ) {
			return $searches_replacements['yoast_seo_meta_title'];
		}

		// If the OpenGraph title is not set, use the post title.
		return $title;

	}

	/**
	 * Returns the OpenGraph excerpt to use for the given post.
	 *
	 * @since   6.0.5
	 *
	 * @param   string  $excerpt      Post Excerpt.
	 * @param   WP_Post $post       WordPress Post.
	 * @return  string
	 */
	public function get_opengraph_excerpt( $excerpt, $post ) {

		// Bail if Yoast SEO isn't active.
		if ( ! $this->is_active() ) {
			return $excerpt;
		}

		$searches_replacements = $this->get_searches_replacements( $post, wp_get_current_user() );

		// If the OpenGraph excerpt is set, use it.
		if ( ! empty( $searches_replacements['yoast_seo_open_graph_description'] ) ) {
			return $searches_replacements['yoast_seo_open_graph_description'];
		}

		// If the OpenGraph excerpt is not set, use the meta description.
		if ( ! empty( $searches_replacements['yoast_seo_meta_description'] ) ) {
			return $searches_replacements['yoast_seo_meta_description'];
		}

		// If the OpenGraph excerpt is not set, use the post excerpt.
		return $excerpt;

	}

	/**
	 * Returns tags and their Post data replacements, that are supported for Yoast SEO.
	 *
	 * @since   4.3.8
	 *
	 * @param   WP_Post $post                   WordPress Post.
	 * @param   WP_User $author                 WordPress User (Author of the Post).
	 * @return  array                               Registered Supported Tags and their Replacements
	 */
	private function get_searches_replacements( $post, $author ) {

		// Store Title and Description.
		$searches_replacements = array(
			'yoast_seo_meta_title'             => $this->get_title( $post ),
			'yoast_seo_meta_description'       => $this->get_description( $post ),
			'yoast_seo_twitter_title'          => '',
			'yoast_seo_twitter_description'    => '',
			'yoast_seo_open_graph_title'       => '',
			'yoast_seo_open_graph_description' => '',
		);

		// Fetch Social Metadata from DB.
		global $wpdb;
		$social_meta_data = $wpdb->get_row(
			$wpdb->prepare(
				'SELECT 
        		twitter_title AS yoast_seo_twitter_title,
        		twitter_description AS yoast_seo_twitter_description,
        		open_graph_title AS yoast_seo_open_graph_title,
        		open_graph_description AS yoast_seo_open_graph_description
        		FROM ' . $wpdb->prefix . "yoast_indexable
        		WHERE object_id = %d
        		AND object_type = 'post'
        		LIMIT 1",
				$post->ID
			),
			ARRAY_A
		);

		// If the data isn't in the DB, it might be in the Post Meta.
		if ( is_null( $social_meta_data ) ) {
			$social_meta_data = array(
				'yoast_seo_twitter_title'          => get_post_meta( $post->ID, '_yoast_wpseo_twitter-title', true ),
				'yoast_seo_twitter_description'    => get_post_meta( $post->ID, '_yoast_wpseo_twitter-description', true ),
				'yoast_seo_open_graph_title'       => get_post_meta( $post->ID, '_yoast_wpseo_opengraph-title', true ),
				'yoast_seo_open_graph_description' => get_post_meta( $post->ID, '_yoast_wpseo_opengraph-description', true ),
			);
		}

		// Merge Social Metadata array if data exists.
		if ( ! is_null( $social_meta_data ) ) {
			$searches_replacements = array_merge( $searches_replacements, $social_meta_data );
		}

		/**
		 * Registers any additional status message tags, and their Post data replacements, that are supported
		 * for Yoast SEO.
		 *
		 * @since   3.8.1
		 *
		 * @param   array       $searches_replacements  Registered Supported Tags and their Replacements.
		 * @param   WP_Post     $post                   WordPress Post.
		 * @param   WP_User     $author                 WordPress User (Author of the Post).
		 */
		$searches_replacements = apply_filters( $this->base->plugin->filter_name . '_publish_register_yoast_seo_searches_replacements', $searches_replacements, $post, $author );

		// Return filtered results.
		return $searches_replacements;

	}

	/**
	 * Return the Title
	 *
	 * @since   4.3.8
	 *
	 * @param   WP_Post $post   WordPress Post.
	 * @return  string              Yoast Post Title
	 */
	private function get_title( $post ) {

		// Get Title.
		$title = WPSEO_Meta::get_value( 'title', $post->ID );
		if ( empty( $title ) ) {
			// Get Title from Post Type Options.
			$title = str_replace( ' %%page%% ', ' ', WPSEO_Options::get( 'title-' . $post->post_type ) );
		}
		$title = wpseo_replace_vars( $title, $post );

		return $title;

	}

	/**
	 * Return the Description
	 *
	 * @since   4.3.8
	 *
	 * @param   WP_Post $post   WordPress Post.
	 * @return  string              Yoast Post Description
	 */
	private function get_description( $post ) {

		// Get Description.
		$description = WPSEO_Meta::get_value( 'metadesc', $post->ID );
		if ( empty( $description ) ) {
			// Get Description from Post Type Options.
			$description = WPSEO_Options::get( 'metadesc-' . $post->post_type );
		}
		$description = wpseo_replace_vars( $description, $post );

		return $description;

	}

	/**
	 * Checks if the Yoast SEO Plugin is active
	 *
	 * @since   4.3.8
	 *
	 * @return  bool    Yoast SEO Plugin Active
	 */
	private function is_active() {

		return defined( 'WPSEO_FILE' );

	}

}
