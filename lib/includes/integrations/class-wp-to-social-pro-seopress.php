<?php
/**
 * SEOPress Plugin Class.
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 */

/**
 * Provides compatibility with SEOPress
 *
 * @package WP_To_Social_Pro
 * @author  WP Zinc
 * @version 4.3.8
 */
class WP_To_Social_Pro_SEOPress {

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

		// Bail if SEOPress isn't active.
		if ( ! $this->is_active() ) {
			return $tags;
		}

		// Register Status Tags.
		return array_merge(
			$tags,
			array(
				'seopress' => array(
					'{seopress_meta_title}'       => __( 'Meta Title', 'wp-to-buffer' ),
					'{seopress_meta_description}' => __( 'Meta Description', 'wp-to-buffer' ),
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

		// Bail if SEOPress isn't active.
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

		// Bail if SEOPress isn't active.
		if ( ! $this->is_active() ) {
			return $title;
		}

		// Fetch the SEO title.
		$seo_title = $this->get_title( $post );

		// If the SEO title is not set, use the post title.
		if ( ! empty( $seo_title ) ) {
			return $seo_title;
		}

		// If the SEO title is not set, use the post title.
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

		// Bail if SEOPress isn't active.
		if ( ! $this->is_active() ) {
			return $excerpt;
		}

		// Fetch the SEO excerpt.
		$seo_excerpt = $this->get_description( $post );

		// If the SEO excerpt is not set, use the post excerpt.
		if ( ! empty( $seo_excerpt ) ) {
			return $seo_excerpt;
		}

		// If the SEO excerpt is not set, use the post excerpt.
		return $excerpt;

	}

	/**
	 * Returns tags and their Post data replacements, that are supported for SEOPress.
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
			'seopress_meta_title'       => $this->get_title( $post ),
			'seopress_meta_description' => $this->get_description( $post ),
		);

		/**
		 * Registers any additional status message tags, and their Post data replacements, that are supported
		 * for SEOPress.
		 *
		 * @since   3.8.1
		 *
		 * @param   array       $searches_replacements  Registered Supported Tags and their Replacements.
		 * @param   WP_Post     $post                   WordPress Post.
		 * @param   WP_User     $author                 WordPress User (Author of the Post).
		 */
		$searches_replacements = apply_filters( $this->base->plugin->filter_name . '_publish_register_seopress_searches_replacements', $searches_replacements, $post, $author );

		// Return filtered results.
		return $searches_replacements;

	}

	/**
	 * Return the Title
	 *
	 * @since   4.3.8
	 *
	 * @param   WP_Post $post   WordPress Post.
	 * @return  string              SEOPress Post Title
	 */
	private function get_title( $post ) {

		// Get title.
		return get_post_meta( $post->ID, '_seopress_titles_title', true );

	}

	/**
	 * Return the Description
	 *
	 * @since   4.3.8
	 *
	 * @param   WP_Post $post   WordPress Post.
	 * @return  string              SEOPress Post Description
	 */
	private function get_description( $post ) {

		return get_post_meta( $post->ID, '_seopress_titles_desc', true );

	}

	/**
	 * Checks if the SEOPress Plugin is active
	 *
	 * @since   4.3.8
	 *
	 * @return  bool    SEOPress Plugin Active
	 */
	private function is_active() {

		return defined( 'SEOPRESS_VERSION' );

	}

}
