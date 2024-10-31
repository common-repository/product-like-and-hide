<?php
/**
 * Class for Product Like and Hide Settings.
 *
 * @package Product_Like_And_Hide
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class exists, then don't execute this.
if ( ! class_exists( 'PLAH_Product_Like_And_Hide_Settings' ) ) {

    /**
     * Class
     */
    class PLAH_Product_Like_And_Hide_Settings {

        protected static $instance = null;

        static $plah_option_name;

        public static function get_instance() {
            null === self::$instance and self::$instance = new self;

            return self::$instance;
        }


        /**
         * Constructor for class.
         */
        public function __construct() {
            self::$plah_option_name = 'plah_admin_settings';

            if ( is_admin() ) {
                add_action( 'admin_menu',
                    array( __CLASS__, 'add_settings_menu_entry' ), 100 );
                add_action( 'admin_init', array( $this, 'update_settings' ) );

                //add_action( 'woocommerce_admin_field_icon', array( $this, 'generate_icon_html' ) );

            }
        }

        public static function add_settings_menu_entry() {
            add_menu_page(
                __( 'Product Like and Hide', 'product-like-and-hide' ),
                __( 'Like and Hide', 'product-like-and-hide' ),
                'manage_woocommerce', // Required user capability
                'like-and-hide', // Menu slug
                array( __CLASS__, 'plah_submenu_plah_page_callback' ),
                PLAH_PLUGIN_URL . 'assets/images/menu-logo.png'
            );

            add_submenu_page(
                "like-and-hide",
                __( 'Settings', 'product-like-and-hide' ),
                __( 'Settings', 'product-like-and-hide' ),
                'manage_woocommerce',
                "plah_settings",
                array( __CLASS__, 'plah_submenu_settings_page_callback' )
            );

            add_submenu_page(
                "like-and-hide",
                __( 'Statistics', 'product-like-and-hide' ),
                __( 'Statistics', 'product-like-and-hide' ),
                'manage_woocommerce',
                "plah_statistics",
                array( __CLASS__, 'plah_submenu_statistics_page_callback' )
            );
        }

        public static function plah_submenu_settings_page_callback() {

            $template = apply_filters(
                'plah_submenu_settings_page_template',
                PLAH_PLUGIN_PATH . 'templates/admin/settings-page.php'
            );

            include( $template );
        }

        public static function plah_submenu_plah_page_callback() {
            $template = apply_filters(
                'plah_submenu_plah_page_callback',
                PLAH_PLUGIN_PATH . 'templates/admin/main-page.php'
            );

            include( $template );
        }

        public static function plah_submenu_statistics_page_callback() {
            $like_prod_stats = array(
                '24h'  => self::get_products( 'like', '24h' ),
                '7d'   => self::get_products( 'like', '7d' ),
                '30d'  => self::get_products( 'like', '30d' ),
                '365d' => self::get_products( 'like', '365d' ),
            );
            $hide_prod_stats = array(
                '24h'  => self::get_products( 'hide', '24h' ),
                '7d'   => self::get_products( 'hide', '7d' ),
                '30d'  => self::get_products( 'hide', '30d' ),
                '365d' => self::get_products( 'hide', '365d' ),
            );

            // Get Like icon
            $like_icon = plah_get_like_icon( 0 );
            $hide_icon = plah_get_hide_icon( 0 );

            $is_like_enabled = plah_is_allowed_to_like( 0 );
            $is_hide_enabled = plah_is_allowed_to_hide( 0 );

            $template = apply_filters(
                'plah_submenu_statistics_page_callback',
                PLAH_PLUGIN_PATH . 'templates/admin/statistics-page.php'
            );

            include( $template );
        }

        private static function get_like_settings() {

            $settings = get_option( self::$plah_option_name );

            // Set checkbox value to yes in order to be checked
            $allow_to_like = '';
            if ( isset( $settings['allow_likes'] ) && $settings['allow_likes'] === 1 ) {
                $allow_to_like = 'yes';
            }

            $allow_non_logged_in_likes = '';
            if ( isset( $settings['allow_non_logged_in_likes'] ) && $settings['allow_non_logged_in_likes'] === 1 ) {
                $allow_non_logged_in_likes = 'yes';
            }

            $settings = array(
                'section_title' => array(
                    'name' => __( 'Configure Product Like',
                        'product-like-and-hide' ),
                    'type' => 'title',
                    'desc' => '',
                    'id'   => 'plah_settings_section_title'
                ),
                'allow_likes'   => array(
                    'name'  => __( "Allow users to like products",
                        'product-like-and-hide' ),
                    'type'  => 'checkbox',
                    'id'    => 'plah_allow_likes',
                    'value' => $allow_to_like,
                    'class' => "plah-ui-toggle",
                ),
            );

            $settings['section_end'] = array(
                'type' => 'sectionend',
                'id'   => 'plah_settings_section_end'
            );

            return apply_filters( 'plah_admin_settings_like_form', $settings );
        }

        private static function get_hide_settings() {

            $settings = get_option( self::$plah_option_name );

            // Set checkbox value to yes in order to be checked
            $allow_hides = '';
            if ( isset( $settings['allow_hides'] ) && $settings['allow_hides'] === 1 ) {
                $allow_hides = 'yes';
            }


            $settings = array(
                'section_title' => array(
                    'name' => __( 'Configure Product Hide',
                        'product-like-and-hide' ),
                    'type' => 'title',
                    'desc' => '',
                    'id'   => 'plah_settings_section_title'
                ),
                'allow_likes'   => array(
                    'name'  => __( "Allow users to hide products",
                        'product-like-and-hide' ),
                    'type'  => 'checkbox',
                    'id'    => 'plah_allow_hides',
                    'value' => $allow_hides,
                    'class' => "plah-ui-toggle",
                ),
            );

            $settings['section_end'] = array(
                'type' => 'sectionend',
                'id'   => 'plah_settings_section_end'
            );

            return apply_filters( 'plah_admin_settings_hide_form', $settings );
        }

        /**
         * Store Product Like and Hide settings to DB
         */
        public function update_settings() {

            // Bail out if not the Product Like and Hide settings page
            if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'plah_settings' || ! $_POST ) {
                return;
            }

            $settings = get_option( self::$plah_option_name );

            // Set admin settings
            if ( isset( $_FILES['product-like-icon'] ) && ! empty( $_FILES['product-like-icon']['name'] ) ) {

                /**
                 * Get allowed mime types
                 */
                $allowed_file_types = apply_filters( 'plah_icon_mime_types', array(
                    'jpg'  => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'gif'  => 'image/gif',
                    'png'  => 'image/png'
                ),
                );

                // Handle the file upload
                $uploaded_file = wp_handle_upload(
                    $_FILES['product-like-icon'],
                    array(
                        'test_form' => false,
                        'mimes'     => $allowed_file_types,
                    )
                );

                // Check if the file was uploaded successfully
                $settings['like_icon'] = ( isset( $_POST['product-like-icon'] ) ?  sanitize_url( $_POST['product-like-icon'] ) : 0 );

                // Check if the file was uploaded successfully
                if ( isset( $uploaded_file['file'] ) ) {
                    // The file was uploaded successfully
                    // Save the file URL in the database
                    // or do something else with it
                    $settings['like_icon'] = ( isset( $uploaded_file['url'] ) ? esc_url( $uploaded_file['url'] ) : 0 );
                }

            }


            $settings['allow_likes']               = ( isset( $_POST['plah_allow_likes'] ) ? 1 : 0 );
            $settings['allow_non_logged_in_likes'] = ( isset( $_POST['plah_allow_non_logged_in_likes'] ) ? 1 : 0 );
            $settings['allow_hides']               = ( isset( $_POST['plah_allow_hides'] ) ? 1 : 0 );

            // Save settings
            update_option( self::$plah_option_name, $settings );

        }

        /**
         * Get products liked or hidden by everyone for a specific
         * range of time.
         */
        public static function get_products( $type = 'like', $range = '24h' ) {
            global $wpdb;

            $products = array();

            $table_name = '';
            if ( $type == 'like' ) {
                $table_name = $wpdb->prefix . PLAH_PLUGIN_LIKES_TABLE_NAME;
            } else if ( $type == 'hide' ) {
                $table_name = $wpdb->prefix . PLAH_PLUGIN_HIDES_TABLE_NAME;
            }

            if ( $table_name != '' ) {

                $interval_array = array(
                    '24h'  => '1 DAY',
                    '7d'   => '7 DAY',
                    '30d'  => '30 DAY',
                    '365d' => '365 DAY'
                );

                // Check if the interval is valid otherwise fall back to 1 day
                if ( in_array( $range, array_keys( $interval_array ) ) ) {
                    $interval = $interval_array[ $range ];
                } else {
                    $interval = '1 DAY';
                }

                // Build the query
                $query = $wpdb->prepare(
                    "SELECT
                        likes.product_id,
                        count( likes.product_id ) AS counter,
                        prod.post_title
                    FROM
                        {$table_name} likes
                        LEFT JOIN {$wpdb->posts} AS prod 
                            ON product_id = prod.ID 
                    WHERE
                        `date_recorded` > DATE_SUB( NOW(), 
                            INTERVAL {$interval} ) 
                        AND prod.post_status = 'publish' 
                        AND prod.post_type = 'product' 
                    GROUP BY
                        product_id 
                    ORDER BY
                        counter DESC
                    LIMIT 10"
                );

                $products = $wpdb->get_results( $query, 'ARRAY_A' );
            }

            return apply_filters( 'plah_admin_get_product_stats', $products, $type, $range );
        }
    }

    new PLAH_Product_Like_And_Hide_Settings();
}
