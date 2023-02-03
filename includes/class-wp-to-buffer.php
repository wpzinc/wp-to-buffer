<?php
/**
 * WP to Buffer class.
 *
 * @package WP_To_Buffer_Pro
 * @author WP Zinc
 */

/**
 * Main WP to Buffer class, used to load the Plugin.
 *
 * @package   WP_To_Buffer
 * @author    WP Zinc
 * @version   1.0.0
 */
class WP_To_Buffer {

    /**
     * Holds the class object.
     *
     * @since   3.1.4
     *
     * @var     object
     */
    public static $instance;

    /**
     * Plugin
     *
     * @since   3.0.0
     *
     * @var     object
     */
    public $plugin = '';

    /**
     * Dashboard
     *
     * @since   3.1.4
     *
     * @var     object
     */
    public $dashboard = '';

    /**
     * Classes
     *
     * @since   3.4.9
     *
     * @var     array
     */
    public $classes = '';

    /**
     * Constructor. Acts as a bootstrap to load the rest of the plugin
     *
     * @since   1.0.0
     */
    public function __construct() {

        // Plugin Details
        $this->plugin                   = new stdClass;
        $this->plugin->name             = 'wp-to-buffer';
        $this->plugin->filter_name      = 'wp_to_buffer';
        $this->plugin->displayName      = 'WP to Buffer';
        
        $this->plugin->settingsName     = 'wp-to-buffer-pro'; // Settings key - used in both Free + Pro, and for oAuth
        $this->plugin->account          = 'Buffer';
        $this->plugin->version           = WP_TO_BUFFER_PLUGIN_VERSION;
        $this->plugin->buildDate         = WP_TO_BUFFER_PLUGIN_BUILD_DATE;
        $this->plugin->requires          = '5.0';
        $this->plugin->tested            = '6.1.1';
        $this->plugin->folder            = WP_TO_BUFFER_PLUGIN_PATH;
        $this->plugin->url               = WP_TO_BUFFER_PLUGIN_URL;
        $this->plugin->documentation_url= 'https://www.wpzinc.com/documentation/wordpress-buffer-pro';
        $this->plugin->support_url      = 'https://www.wpzinc.com/support';
        $this->plugin->upgrade_url      = 'https://www.wpzinc.com/plugins/wordpress-to-buffer-pro';
        $this->plugin->review_name      = 'wp-to-buffer';
        $this->plugin->review_notice     = sprintf(
            /* translators: Plugin Name */
            __( 'Thanks for using %s to schedule your social media statuses on Buffer!', 'wp-to-social-pro' ),
            $this->plugin->displayName
        );

        // Default Settings.
        $this->plugin->default_schedule = 'queue_bottom';

        // Upgrade Reasons.
        $this->plugin->upgrade_reasons = array(
            array(
                __( 'Post to Instagram and Pinterest', 'wp-to-social-pro' ), 
                __( 'Pro supports Direct Posting to Instagram Business Profiles and Pinterest Boards', $this->plugin->name ),
            ),
            array(
                __( 'Multiple, Customisable Status Messages', 'wp-to-social-pro' ), 
                __( 'Each Post Type and Social Network can have multiple, unique status message and settings', $this->plugin->name ),
            ),
            array(
                __( 'Conditionally send Status Messages', 'wp-to-social-pro' ), 
                __( 'Only send status(es) to Buffer based on Post Author(s), Taxonomy Term(s) and/or Custom Field Values', $this->plugin->name ),
            ),
            array(
                __( 'More Scheduling Options', 'wp-to-social-pro' ), 
                __( 'Each status update can be added to the start/end of your Buffer queue, posted immediately or scheduled at a specific time', $this->plugin->name ),
            ),
            array(
                __( 'Dynamic Status Tags', 'wp-to-social-pro' ), 
                __( 'Dynamically build status updates with data from the Post Author and Custom Fields', $this->plugin->name ),
            ),
            array(
                __( 'Separate Statuses per Social Network', 'wp-to-social-pro' ), 
                __( 'Define different statuses for each Post Type and Social Network', $this->plugin->name ),
            ),
            array(
                __( 'Per-Post Settings', 'wp-to-social-pro' ), 
                __( 'Override Settings on Individual Posts: Each Post can have its own Buffer settings', $this->plugin->name ),
            ),
            array(
                __( 'Repost Old Posts', 'wp-to-social-pro' ), 
                __( 'Automatically Revive Old Posts that haven\'t been updated in a while, choosing the number of days, weeks or years to re-share content on social media.', $this->plugin->name ),
            ),
            array(
                __( 'Bulk Publish Old Posts', 'wp-to-social-pro' ), 
                __( 'Manually re-share evergreen WordPress content and revive old posts with the Bulk Publish option', $this->plugin->name ),
            ),
            array(
                __( 'The Events Calendar and Event Manager Integration', 'wp-to-social-pro' ), 
                __( 'Schedule Posts to Buffer based on your Event\'s Start or End date, and display Event-specific details in your status updates', $this->plugin->name ),
            ),
            array(
                __( 'SEO Integration', 'wp-to-social-pro' ), 
                __( 'Display SEO-specific information in your status updates from All-In-One SEO Pack, Rank Math, SEOPress and Yoast SEO', $this->plugin->name ),
            ),
            array(
                __( 'WooCommerce Integration', 'wp-to-social-pro' ), 
                __( 'Display Product-specific information in your status updates', $this->plugin->name ),
            ),
            array(
                __( 'Autoblogging and Frontend Post Submission Integration', 'wp-to-social-pro' ), 
                __( 'Pro supports autoblogging and frontend post submission Plugins, including User Submitted Posts, WP Property Feed, WPeMatico and WP Job Manager', $this->plugin->name ),
            ),
            array(
                __( 'Shortcode Support', 'wp-to-social-pro' ), 
                __( 'Use shortcodes in status updates', $this->plugin->name ),
            ),
            array(
                __( 'Full Image Control', 'wp-to-social-pro' ), 
                __( 'Choose to display the WordPress Featured Image with your status updates, or define up to 4 custom images for each Post.', $this->plugin->name ),
            ),
            array(
                __( 'WP-Cron and WP-CLI Compatible', 'wp-to-social-pro' ), 
                __( 'Optionally enable WP-Cron to send status updates via Cron, speeding up UI performance and/or choose to use WP-CLI for reposting old posts', $this->plugin->name ),
            ),
        );
    
        // Dashboard Submodule
        if ( ! class_exists( 'WPZincDashboardWidget' ) ) {
			require_once $this->plugin->folder . '_modules/dashboard/class-wpzincdashboardwidget.php';
		}
        $this->dashboard = new WPZincDashboardWidget( $this->plugin, 'https://www.wpzinc.com/wp-content/plugins/lum-deactivation' );

        // Defer loading of Plugin Classes
        add_action( 'init', array( $this, 'initialize' ), 1 );
        add_action( 'init', array( $this, 'upgrade' ), 2 );

        // Localization.
        add_action( 'plugins_loaded', array( $this, 'load_language_files' ) );

    }

    /**
     * Initializes required classes
     *
     * @since   3.4.9
     */
    public function initialize() {

        $this->classes = new stdClass;

        // Initialize required classes
        $this->classes->admin       = new WP_To_Social_Pro_Admin( self::$instance );
        $this->classes->ajax        = new WP_To_Social_Pro_AJAX( self::$instance );
        $this->classes->api         = new WP_To_Social_Pro_Buffer_API( self::$instance );
        $this->classes->common      = new WP_To_Social_Pro_Common( self::$instance );
        $this->classes->cron        = new WP_To_Social_Pro_Cron( self::$instance );
        $this->classes->date        = new WP_To_Social_Pro_Date( self::$instance );
        $this->classes->image     	= new WP_To_Social_Pro_Image( self::$instance );
        $this->classes->install     = new WP_To_Social_Pro_Install( self::$instance );
        $this->classes->log         = new WP_To_Social_Pro_Log( self::$instance ); 
        $this->classes->media_library = new WP_To_Social_Pro_Media_Library( self::$instance );
        $this->classes->notices     = new WP_To_Social_Pro_Notices( self::$instance );  
        $this->classes->post        = new WP_To_Social_Pro_Post( self::$instance );
        $this->classes->publish     = new WP_To_Social_Pro_Publish( self::$instance );
        $this->classes->screen      = new WP_To_Social_Pro_Screen( self::$instance );
        $this->classes->settings    = new WP_To_Social_Pro_Settings( self::$instance );
        $this->classes->twitter_api = new WP_To_Social_Pro_Twitter_API( self::$instance );
        $this->classes->validation  = new WP_To_Social_Pro_Validation( self::$instance );

        // Run the migration routine from Free + Pro v2.x --> Pro v3.x
        if ( is_admin() ) {
            $this->classes->settings->migrate_settings();
        }
        
    }

    /**
     * Runs the upgrade routine once the plugin has loaded
     *
     * @since   3.2.5
     */
    public function upgrade() {

        // Run upgrade routine
        $this->get_class( 'install' )->upgrade();

    }

    /**
     * Loads plugin textdomain
     *
     * @since   3.8.4
     */
    public function load_language_files() {

        load_plugin_textdomain( 'wp-to-social-pro', false, $this->plugin->name . '/languages/' );

    }

    /**
     * Returns the given class
     *
     * @since   3.4.9
     *
     * @param   string $name   Class Name.
     */
    public function get_class( $name ) {

        // If the class hasn't been loaded, throw a WordPress die screen
        // to avoid a PHP fatal error.
        if ( ! isset( $this->classes->{ $name } ) ) {
            // Define the error.
            $error = new WP_Error(
                'wp_to_buffer_get_class',
                sprintf(
                    /* translators: %1$s: Plugin Name, %2$s: PHP class name */
                    __( '%1$s: Error: Could not load Plugin class %2$s', 'wp-to-social-pro' ),
                    $this->plugin->displayName,
                    $name
                )
            );

            // Depending on the request, return or display an error.
            // Admin UI.
            if ( is_admin() ) {
                wp_die(
                    esc_html( $error->get_error_message() ),
                    sprintf(
                        /* translators: Plugin Name */
                        esc_html__( '%s: Error', 'wp-to-social-pro' ),
                        esc_html( $this->plugin->displayName )
                    ),
                    array(
                        'back_link' => true,
                    )
                );
            }

            // Cron / CLI.
            return $error;
        }

        // Return the class object.
        return $this->classes->{ $name };

    }

    /**
     * Returns the singleton instance of the class.
     *
     * @since   3.1.4
     *
     * @return  object Class.
     */
    public static function get_instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
            self::$instance = new self;
        }

        return self::$instance;

    }

}
