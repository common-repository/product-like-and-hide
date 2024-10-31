<?php
/**
 * Class for custom work.
 *
 * @package Product_Like_And_Hide
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'PLAH_Product_Like_And_Hide' ) ) {

    /**
     * Class for transxen core.
     */
    class PLAH_Product_Like_And_Hide {

        protected static $instance = null;

        public static function get_instance() {
            null === self::$instance and self::$instance = new self;

            return self::$instance;
        }


        /**
         * Constructor for class.
         */
        public function __construct() {

            // Enqueue front-end scripts
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style_scripts' ), 100 );

            add_action( 'woocommerce_after_add_to_cart_button',
                array( $this, 'display_plah_single_product_buttons' ) );

            add_action( 'wp_footer', array( $this, 'add_notice_modal' ) );
        }


        /**
         * Enqueue style/script.
         *
         * @return void
         */
        public function enqueue_style_scripts() {

            // Do not load anything if not needed
            if ( ! plah_load_the_code() ) {
                return;
            }


            // Custom plugin script.
            wp_enqueue_style(
                'product-like-and-hide-core-style',
                PLAH_PLUGIN_URL . 'assets/css/product-like-and-hide.css',
                '',
                PLAH_PLUGIN_VERSION
            );

            // Register plugin's JS script
            wp_register_script(
                'product-like-and-hide-custom-script',
                PLAH_PLUGIN_URL . 'assets/js/product-like-and-hide.js',
                array(
                    'jquery',
                ),
                PLAH_PLUGIN_VERSION,
                true
            );

            wp_localize_script( 'product-like-and-hide-custom-script',
                'plahAJAXObj', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'plah_nonce' ),
            ) );

            wp_enqueue_script( 'product-like-and-hide-custom-script' );

        }



        public function display_plah_single_product_buttons() {
            $product_id = get_the_id();

            $is_allowed_to_like = plah_is_allowed_to_like( $product_id );
            $is_allowed_to_hide = plah_is_allowed_to_hide( $product_id );

            if ( $is_allowed_to_like || $is_allowed_to_hide ) {
                ?>
                <div class="plah-product-buttons">
                    <?php
                    // If applicable show like button
                    if ( $is_allowed_to_like ) {
                        do_action( 'plah_show_single_product_like_button' );
                    }
                    // If applicable show hide button
                    if ( $is_allowed_to_hide ) {
                        do_action( 'plah_show_single_product_hide_button' );
                    }
                    ?>
                </div>
                <?php
            }
        }

        /**
         * Add modal to footer to display the response message.
         */
        public function add_notice_modal() {
            include apply_filters( 'plah_add_notice_modal', PLAH_PLUGIN_PATH . 'templates/notice-modal-template.php' );
        }

    }

    new PLAH_Product_Like_And_Hide();
}
