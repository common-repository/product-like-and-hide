<?php
/**
 * Class for managing the DB.
 *
 * @package PLAH_DB_Management
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'PLAH_DB_Management' ) ) {

    /**
     * Class for the plugin's core.
     */
    class PLAH_DB_Management {


        /**
         * Constructor for class.
         */
        public function __construct() {

            // Register Activation hooks

            register_activation_hook( PLAH_PLUGIN_FILE, array( 'PLAH_DB_Management', 'create_likes_table' ) );
            register_uninstall_hook( PLAH_PLUGIN_FILE, array( 'PLAH_DB_Management', 'delete_likes_table' ) );

            register_activation_hook( PLAH_PLUGIN_FILE, array( 'PLAH_DB_Management', 'create_hides_table' ) );
            register_uninstall_hook( PLAH_PLUGIN_FILE, array( 'PLAH_DB_Management', 'delete_hides_table' ) );
        }


        /**
         * Create the custom table for logging the product likes
         * Since 1.0.0
         */
        static function create_likes_table() {
            global $wpdb;

            $table_name = $wpdb->prefix . PLAH_PLUGIN_LIKES_TABLE_NAME;

            // Check if table exists.
            $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

            if ( $wpdb->get_var( $query ) === $table_name ) {
                return true;
            }

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE `$table_name` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `product_id` bigint(20) NULL DEFAULT NULL,
              `user_id` bigint(20) NULL DEFAULT NULL,            
              `date_recorded` datetime(0) NULL DEFAULT NULL,
              PRIMARY KEY (`id`) USING BTREE,
              INDEX `check_point`(`product_id`, `user_id`) USING BTREE
                     
            ) ENGINE = InnoDB  $charset_collate ROW_FORMAT = COMPACT;
            ";


            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $result = dbDelta( $sql );

        }


        // Delete DB tables on Plugin removal
        static function delete_activity_awards_table() {
            global $wpdb;

            $tables = array(
                $wpdb->prefix . URS_AWARD_POINTS_TABLE_NAME,
            );


            foreach ( $tables as $table_name ) {

                $sql = "DROP TABLE IF EXISTS $table_name";
                $wpdb->query( $sql );
            }

        }

        /**
         * Create the custom table for logging the product hides
         * Since 1.0.0
         */
        static function create_hides_table() {
            global $wpdb;

            $table_name = $wpdb->prefix . PLAH_PLUGIN_HIDES_TABLE_NAME;

            // Check if table exists.
            $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );

            if ( $wpdb->get_var( $query ) === $table_name ) {
                return true;
            }

            $charset_collate = $wpdb->get_charset_collate();

            $sql = "CREATE TABLE `$table_name` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT,
              `product_id` bigint(20) NULL DEFAULT NULL,
              `user_id` bigint(20) NULL DEFAULT NULL,            
              `date_recorded` datetime(0) NULL DEFAULT NULL,
              PRIMARY KEY (`id`) USING BTREE,
              INDEX `check_point`(`product_id`, `user_id`) USING BTREE
                     
            ) ENGINE = InnoDB  $charset_collate ROW_FORMAT = COMPACT;
            ";


            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            $result = dbDelta( $sql );

        }

        // Delete DB tables on Plugin removal
        static function delete_hashtags_table() {
            global $wpdb;

            $tables = array(
                $wpdb->prefix . URS_HASHTAGS_TABLE_NAME,
            );

            foreach ( $tables as $table_name ) {
                $sql = "DROP TABLE IF EXISTS $table_name";
                $wpdb->query( $sql );
            }
        }
    }

    new PLAH_DB_Management();
}
