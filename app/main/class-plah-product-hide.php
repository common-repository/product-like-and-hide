<?php
/**
 * Class for custom work.
 *
 * @package PLAH_Product_Hide
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'PLAH_Product_Hide' ) ) {

    /**
     * Class for transxen core.
     */
    class PLAH_Product_Hide {

        protected static $instance = null;

        public static function get_instance() {
            null === self::$instance and self::$instance = new self;

            return self::$instance;
        }


        /**
         * Constructor for class.
         */
        public function __construct() {

            add_action( 'plah_show_single_product_hide_button',
                array( $this, 'display_hide_button' ) );

            add_action( 'wp_ajax_plah_hide_product',
                array( $this, 'ajax_hide_product' ) );

            add_action( 'wp_ajax_nopriv_plah_hide_product',
                array( $this, 'ajax_hide_product' ) );

            add_filter( 'pre_get_posts', array( $this, 'hide_products' ) );

            add_action( 'woocommerce_after_shop_loop_item',
                array( $this, 'display_hide_button' ), 20 );
        }


        /**
         * Display like button.
         *
         * @return void
         */
        public function display_hide_button() {
            global $product;

            $product_id = $product->get_id();

            $is_allowed_to_hide = plah_is_allowed_to_hide( $product_id );

            // Bail out if not allowed to like
            if ( ! $is_allowed_to_hide ) {
                return;
            }

            // Get Like icon
            $icon = plah_get_hide_icon( $product_id );

            // Check whether the user has liked the product
            $has_user_hidden = plah_is_product_hidden( $product_id, get_current_user_id() );

            // Get like button class
            $hide_class = '';
            if ( $has_user_hidden ) {
                $hide_class = ' hidden';
            }

            // Get hide button text
            $hide_button_text   = apply_filters( 'plah_hide_button_text',
                __( 'Hide', 'product-like-and-hide' ), $product_id );
            $unhide_button_text = apply_filters( 'plah_unhide_button_text',
                __( 'Unhide', 'product-like-and-hide' ), $product_id );


            include apply_filters( 'plah_hide_button_template', PLAH_PLUGIN_PATH . 'templates/hide-button-template.php', $product_id );
        }

        /**
         * Handle Like Product AJAX request.
         *
         * @return json
         */
        public function ajax_hide_product() {
            global $wpdb;

            $product_id = isset( $_POST['productID'] ) ? absint( $_POST['productID'] ) : 0;
            $nonce      = sanitize_text_field( $_POST['nonce'] );
            $user_id    = get_current_user_id();
            $type       = sanitize_text_field( $_POST['type'] );
            $like_table = $wpdb->prefix . PLAH_PLUGIN_HIDES_TABLE_NAME;

            // Skip if no product is selected
            if ( $product_id <= 0 ) {
                $response['success'] = false;
                $response['content'] = 'No product is selected';
                echo json_encode( $response );
                wp_die();
            }

            // Bail out if like function is disabled
            $is_allowed_to_hide = plah_is_allowed_to_hide( $product_id );
            if ( ! $is_allowed_to_hide ) {
                $response['success'] = false;
                $response['content'] = 'Hide function is disabled';
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
                    __( 'Please <a href=%s>log in</a> or <a href="%s">register</a> to hide this product.', 'product-like-and-hide' ),
                    esc_url( wp_login_url( get_permalink() ) ),
                    esc_url( wp_registration_url() )
                );
                $response['show_notice'] = true;

                echo json_encode( $response );
                wp_die();
            }

            // Check if user already liked the product
            $is_hidden = plah_is_product_hidden( $product_id, $user_id );
            if ( $type == 'hide' && $is_hidden ) {
                $response['success'] = false;
                $response['content'] = __( 'The product is already hidden by this user', 'product-like-and-hide' );
                echo json_encode( $response );
                wp_die();
            } else if (
                $type == 'unhide' && ! $is_hidden ) {
                $response['success'] = false;
                $response['content'] = __( 'The product is not hidden by this user', 'product-like-and-hide' );
                echo json_encode( $response );
                wp_die();
            }

            // Store the user like
            if ( $type == 'hide' ) {
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
                    $response['content'] = __( 'Product hid successfully', 'product-like-and-hide' );
                    $response['type']    = 'hidden';
                    echo json_encode( $response );
                    exit;
                }

            } else if ( $type == 'unhide' ) {
                $data = array(
                    'product_id' => $product_id,
                    'user_id'    => $user_id,
                );

                $wpdb->delete( $like_table, $data );

                $response['success'] = true;
                $response['content'] = __( 'Product unhidden successfully', 'product-like-and-hide' );
                $response['type']    = 'unhidden';

                echo json_encode( $response );
                exit;

            }

            exit;

        }

        /**
         * Hide products from the shop page.
         *
         * @param object $query
         *
         * @return object
         */
        public function hide_products( object $query ) {

            $user_id         = get_current_user_id();
            $hidden_products = array();

            if ( ! is_admin() && $query->is_main_query() && ! is_singular()  ) {

                if ( $user_id > 0 ) {
                    $hidden_products = plah_get_hidden_products( $user_id );

                    if ( ! empty( $hidden_products ) ) {

                        // Fetch existing hidden posts
                        $hidden_posts = $query->get( 'post__not_in' );

                        // Add the hidden products to the existing hidden posts
                        $hidden_posts = array_merge( $hidden_posts, $hidden_products );

                        // Set the query to exclude the hidden posts
                        $query->set( 'post__not_in', $hidden_posts );
                    }
                }
            }

            return apply_filters( 'plah_product_hide_hide_products', $query, $user_id, $hidden_products );
        }

    }

    new PLAH_Product_Hide();
}
