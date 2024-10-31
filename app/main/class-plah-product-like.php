<?php
/**
 * Class for custom work.
 *
 * @package Product_Like
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'PLAH_Product_Like' ) ) {

    /**
     * Class for transxen core.
     */
    class PLAH_Product_Like {

        protected static $instance = null;

        public static function get_instance() {
            null === self::$instance and self::$instance = new self;

            return self::$instance;
        }


        /**
         * Constructor for class.
         */
        public function __construct() {


            add_action( 'plah_show_single_product_like_button',
                array( $this, 'display_like_button' ) );

            add_action( 'wp_ajax_plah_like_product',
                array( $this, 'ajax_like_product' ) );

            add_action( 'wp_ajax_nopriv_plah_like_product',
                array( $this, 'ajax_like_product' ) );

            // register shortcode
            add_shortcode( 'plah_user_likes_page',
                array( $this, 'display_user_likes_page' ) );
        }


        /**
         * Display like button.
         *
         * @return void
         */
        public function display_like_button() {

            $product_id = get_the_id();

            $is_allowed_to_like = plah_is_allowed_to_like( $product_id );

            // Bail out if not allowed to like
            if ( ! $is_allowed_to_like ) {
                return;
            }

            // Get number of likes
            $likes = plah_get_product_likes( $product_id );

            // Get Like icon
            $icon = plah_get_like_icon( $product_id );

            // Check whether the user has liked the product
            $has_user_liked = plah_is_product_liked( $product_id, get_current_user_id() );

            // Get like button class
            $like_class = '';
            if ( $has_user_liked ) {
                $like_class = ' liked';
            }

            // Get like button text
            $like_button_text = apply_filters( 'plah_like_button_text',
                __( 'Like', 'product-like-and-hide' ), $product_id );


            include apply_filters( 'plah_like_button_template', PLAH_PLUGIN_PATH . 'templates/like-button-template.php', $product_id );
        }

        /**
         * Handle Like Product AJAX request.
         *
         * @return json
         */
        public function ajax_like_product() {
            global $wpdb;

            $product_id = isset( $_POST['productID'] ) ? absint( $_POST['productID'] ) : 0;
            $nonce      = sanitize_text_field( $_POST['nonce'] );
            $user_id    = get_current_user_id();
            $type       = sanitize_text_field( $_POST['type'] );
            $like_table = $wpdb->prefix . PLAH_PLUGIN_LIKES_TABLE_NAME;

            // Skip if no product is selected
            if ( $product_id <= 0 ) {
                $response['success'] = false;
                $response['content'] = 'No product is selected';
                echo json_encode( $response );
                wp_die();
            }

            // Bail out if like function is disabled
            $is_allowed_to_like = plah_is_allowed_to_like( $product_id );
            if ( ! $is_allowed_to_like ) {
                $response['success'] = false;
                $response['content'] = 'Like function is disabled';
                echo json_encode( $response );
                wp_die();
            }

            // Security Validate Nonce
            if ( ! wp_verify_nonce( $nonce, 'plah_nonce' ) ) {
                $response['success'] = false;
                $response['content'] = 'Security check failed';
                echo json_encode( $response );
                wp_die();
            }

            // Check that user exists;
            if ( $user_id <= 0 ) {
                $response['success']     = false;
                $response['content']     = sprintf(
                    __( 'Please <a href=%s>log in</a> or <a href="%s">register</a> to like this product.', 'product-like-and-hide' ),
                    esc_url( wp_login_url( get_permalink() ) ),
                    esc_url( wp_registration_url() )
                );
                $response['show_notice'] = true;

                echo json_encode( $response );
                wp_die();
            }

            // Check if user already liked the product
            $is_liked = plah_is_product_liked( $product_id, $user_id );
            if ( $type == 'like' && $is_liked ) {
                $response['success'] = false;
                $response['content'] = __( 'The product is already liked by this user', 'product-like-and-hide' );
                echo json_encode( $response );
                wp_die();
            } else if (
                $type == 'unlike' && ! $is_liked ) {
                $response['success'] = false;
                $response['content'] = __( 'The product is not liked by this user', 'product-like-and-hide' );
                echo json_encode( $response );
                wp_die();
            }

            // Store the user like
            if ( $type == 'like' ) {
                $data   = array(
                    'product_id'    => $product_id,
                    'user_id'       => $user_id,
                    'date_recorded' => current_time( 'mysql' ),
                );
                $format = array(
                    '%d',
                    '%d',
                    '%s',
                );
                $wpdb->insert( $like_table, $data, $format );
                $like_id = $wpdb->insert_id;

                if ( $like_id > 0 ) {
                    $response['success'] = true;
                    $response['content'] = __( 'Product liked successfully', 'product-like-and-hide' );
                    $response['type']    = 'liked';
                    $response['likes']   = plah_get_product_likes( $product_id );
                    echo json_encode( $response );
                    exit;
                }

            } else if ( $type == 'unlike' ) {
                $data = array(
                    'product_id' => $product_id,
                    'user_id'    => $user_id,
                );

                $wpdb->delete( $like_table, $data );

                $response['success'] = true;
                $response['content'] = __( 'Product unliked successfully', 'product-like-and-hide' );
                $response['likes']   = plah_get_product_likes( $product_id );
                $response['type']    = 'unliked';
                echo json_encode( $response );
                exit;

            }

            exit;

        }

        /**
         * Display all the required content for the User Likes page
         */
        public function display_user_likes_page() {
            global $wpdb;

            // Redirect to home if not logged in
            $user_id = get_current_user_id();

            if ( empty( $user_id ) ) {
                ob_start();

                printf( __( 'Please <a href="%s">log in</a> to view your Liked Products.',
                    'product-like-and-hide' ),
                    esc_url( wp_login_url( get_permalink() ) )
                );

                return ob_get_clean();
            }

            $table_name = $wpdb->prefix . PLAH_PLUGIN_LIKES_TABLE_NAME;

            // Get member's history
            $custom_pagination = "";

            // How many items per page
            $items_per_page = abs( (int) get_option( 'posts_per_page' ) );
            if ( empty( $items_per_page ) ) {
                $items_per_page = 20;
            }

            // Filter
            $keyword = '';

            // The main Query
            $main_query = "SELECT likes.*, prod.post_title, prod.post_content FROM $table_name likes LEFT JOIN {$wpdb->posts} as prod ON product_id = prod.ID WHERE user_id = {$user_id} AND prod.post_status = 'publish' and prod.post_type = 'product'";

            $query = $main_query;

            // Apply keyword search
            if ( isset( $_GET['like-search'] ) ) {
                $keyword = trim( sanitize_text_field( $_GET['like-search'] ) );
                $query   .= ' AND prod.post_title like "%' . $keyword . '%"';
            }

            $total_query = "SELECT COUNT(1) FROM (${query}) AS combined_table";
            $total       = $wpdb->get_var( $total_query );

            $cpage = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;

            $offset = ( $cpage * $items_per_page ) - $items_per_page;

            $like_query = $query . " ORDER BY date_recorded DESC LIMIT ${offset}, ${items_per_page}";
            $likes      = $wpdb->get_results( $like_query );

            $total_page = ceil( $total / $items_per_page );

            ob_start();

            include_once( PLAH_PLUGIN_PATH . 'templates/like-page-loop.php' );

            return ob_get_clean();

        }

    }

    new PLAH_Product_Like();
}
