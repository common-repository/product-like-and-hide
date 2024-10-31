<?php
/**
 * Class for custom work.
 *
 * @package PLAH_Product_Admin
 */

/**
 * Exit if accessed directly.
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// If class is exist, then don't execute this.
if ( ! class_exists( 'PLAH_Product_Admin' ) ) {

    /**
     * Class for transxen core.
     */
    class PLAH_Product_Admin {

        protected static $instance = null;

        public static function get_instance() {
            null === self::$instance and self::$instance = new self;

            return self::$instance;
        }


        /**
         * Constructor for class.
         */
        public function __construct() {
            add_action( 'add_meta_boxes',
                array( $this, 'create_product_info_meta_box' ) );

            // Enqueue Back end scripts
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_style_scripts' ), 100 );
        }

        /**
         * Enqueue Admin style/script.
         *
         * @return void
         */
        public function admin_enqueue_style_scripts() {
            // Custom plugin script.
            wp_enqueue_style(
                'product-like-and-hide-admin-settings',
                PLAH_PLUGIN_URL . 'assets/css/product-like-and-hide-admin.css',
                '',
                PLAH_PLUGIN_VERSION
            );

            // Register plugin's JS script
            wp_register_script(
                'product-like-and-hide-admin',
                PLAH_PLUGIN_URL . 'assets/js/product-like-and-hide-admin.js',
                array(
                    'jquery',
                ),
                PLAH_PLUGIN_VERSION,
                true
            );

            wp_enqueue_script( 'product-like-and-hide-admin' );
        }

        /**
         * Create product info meta box for the admin product page
         */
        public function create_product_info_meta_box() {
            if ( get_post_type() !== 'product' ) {
                return;
            }

            $is_like_enabled = plah_get_setting( 'allow_likes' );
            $is_hide_enabled = plah_get_setting( 'allow_hides' );

            if ( $is_like_enabled || $is_hide_enabled ) {
                add_meta_box(
                    'product_info_meta_box',
                    __( 'Product Likes and Hides', 'product-like-and-hide' ),
                    array( $this, 'display_product_info_meta_box' ),
                    'product',
                    'side',
                    'default'
                );
            }

        }

        /**
         * Display product info meta box with the like and hide count.
         */
        public function display_product_info_meta_box() {
            $post_id         = get_the_ID();
            $is_like_enabled = plah_get_setting( 'allow_likes' );
            $is_hide_enabled = plah_get_setting( 'allow_hides' );

            if ( $post_id <= 0 ||
                 ! ( $is_like_enabled || $is_hide_enabled ) ) {
                return;
            }
            ?>
			<div class="plah-side-stats-wrapper">
                <?php
                // Show product likes
                if ( $is_like_enabled ) {
                    $counter   = plah_get_product_likes( $post_id );
                    $like_icon = plah_get_like_icon( $post_id );
                    ?>
					<div class="stats-like-wrapper">
						<span class="like-icon side-stats-icon">
							<img src="<?php echo esc_url( $like_icon ); ?>"
								 width="20" height="20">
						</span>
						<span class="like-count-text"><?php
                            _e( 'Likes', 'product-like-and-hide' ); ?>:
							<span class="like-count">
								<?php echo absint( $counter ); ?>
							</span>
						</span>
					</div>
                <?php } ?>

                <?php
                // Show product likes
                if ( $is_hide_enabled ) {
                    $counter   = plah_get_product_hides( $post_id );
                    $hide_icon = plah_get_hide_icon( $post_id );

                    ?>
					<div class="stats-hide-wrapper">
						<span class="hide-icon side-stats-icon">
							<img src="<?php echo esc_url( $hide_icon ); ?>"
								 width="20" height="20">
						</span>
						<span class="like-count-text"><?php
                            _e( 'Hides', 'product-like-and-hide' ); ?>:
							<span class="like-count">
								<?php echo absint( $counter ); ?>
							</span>
						</span>
					</div>
                <?php } ?>
			</div>
            <?php
        }
    }

    new PLAH_Product_Admin();
}
