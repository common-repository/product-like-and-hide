<?php
/**
 * Product Like and Hide
 *
 * Allow customers to like and hide products on your e-shop
 *
 * @link              https://gianniskipouros.com
 * @since             1.0.0
 * @package           product-like-and-hide
 *
 * @wordpress-plugin
 * Plugin Name:       Product Like and Hide
 * Plugin URI:        https://gianniskipouros.com/product-like-and-hide/
 * Description:       Offer a personalized shopping experience to your customers by making it easy for them to like and find products they love, while they can hide the ones they don't.
 * Version:           1.0.1
 * Author:            Product Like and Hide
 * Author URI:        https://gianniskipouros.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       product-like-and-hide
 * Domain Path:       /languages
 */

/**
 * Main file, contains the plugin metadata and activation processes
 *
 * @package    product-like-and-hide
 */
if ( ! defined( 'PLAH_PLUGIN_VERSION' ) ) {
    /**
     * The version of the plugin.
     */
    define( 'PLAH_PLUGIN_VERSION', '1.0.1' );
}

if ( ! defined( 'PLAH_PLUGIN_FILE' ) ) {
    /**
     *  The plugin file
     */
    define( 'PLAH_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'PLAH_PLUGIN_PATH' ) ) {
    /**
     *  The server file system path to the plugin directory.
     */
    define( 'PLAH_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'PLAH_PLUGIN_URL' ) ) {
    /**
     * The url to the plugin directory.
     */
    define( 'PLAH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

if ( ! defined( 'PLAH_PLUGIN_BASE_NAME' ) ) {
    /**
     * The url to the plugin directory.
     */
    define( 'PLAH_PLUGIN_BASE_NAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'PLAH_PLUGIN_PRO_BUY_URL' ) ) {
    /**
     * The url to the plugin directory.
     */
    define( 'PLAH_PLUGIN_PRO_BUY_URL',
        get_admin_url( '', 'admin.php?page=plah-pricing' ) );
}

if ( ! defined( 'PLAH_PLUGIN_LIKES_TABLE_NAME' ) ) {
    define( 'PLAH_PLUGIN_LIKES_TABLE_NAME', 'plah_likes' );
}

if ( ! defined( 'PLAH_PLUGIN_HIDES_TABLE_NAME' ) ) {
    define( 'PLAH_PLUGIN_HIDES_TABLE_NAME', 'plah_hides' );
}

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


/**
 * Include files.
 */
function plah_include_extra_plugin_files() {

    // Include Class files
    $files = array(
        'app/main/class-plah-main',
        'app/main/class-plah-admin',
        'app/main/class-plah-admin-settings',
        'app/main/class-plah-product-like',
        'app/main/class-plah-product-hide',
    );


    // Include Includes files
    $includes = array(
        'includes/main-functions',
    );

    // Merge the two arrays
    $files = array_merge( $files, $includes );

    foreach ( $files as $file ) {
        // Include functions file.
        require PLAH_PLUGIN_PATH . $file . '.php';
    }
}

add_action( 'plugins_loaded', 'plah_include_extra_plugin_files', 12 );

/**
 * Load Product Like and Hide textdomain.
 */
function plah_language_textdomain_init() {
    // Localization
    load_plugin_textdomain( 'product-like-and-hide', false,
        dirname( plugin_basename( __FILE__ ) ) . "/languages" );
}

// Add actions
add_action( 'init', 'plah_language_textdomain_init' );

/**
 * Check if the code should be loaded
 */
function plah_load_the_code() {
    global $post;

    $should_load = true;

    // Check if it is the my likes page (includes the shortcode)
    $is_my_likes_page = false;

    if ( is_page() && isset( $post->post_content ) ) {

        $page_content = $post->post_content;
        if ( has_shortcode( $page_content, 'plah_user_likes_page' ) ) {
            $is_my_likes_page = true;
        }
    }

    // Load
    if ( ! class_exists( 'WooCommerce' ) ) {
        $should_load = false;
    } else if ( ! is_admin() ) {
        if ( ! is_product() &&
             ! is_woocommerce() &&
             ! is_product_category() &&
             ! is_product_tag() &&
             ! $is_my_likes_page
        ) {
            $should_load = false;
        }
    }

    return apply_filters( 'plah_load_the_code', $should_load );
}

/**
 * Check if the free plugin is enabled.
 */
function plah_plugin_activate() {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }
}

register_activation_hook( __FILE__, 'plah_plugin_activate' );


/**
 * Clean up on plugin uninstall
 */
function plah_plugin_uninstall() {
    global $wpdb;

    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    // Delete the likes table
    $table_name = $wpdb->prefix . PLAH_PLUGIN_LIKES_TABLE_NAME;
    $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

    // Delete the hides table
    $table_name = $wpdb->prefix . PLAH_PLUGIN_HIDES_TABLE_NAME;
    $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );

    // Delete the options
    delete_option( PLAH_Product_Like_And_Hide_Settings::$plah_option_name );
}

register_uninstall_hook( __FILE__, 'plah_plugin_uninstall' );

// INCLUDES - Need to run First
include( PLAH_PLUGIN_PATH . 'app/main/class-db-management.php' );


