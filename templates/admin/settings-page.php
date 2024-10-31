<?php
/**
 * Template file for WP admin settings page
 */
require_once( PLAH_PLUGIN_PATH . '/templates/admin/admin-header.php' );

if ( ! function_exists( 'woocommerce_admin_fields' ) ) {
    ?>
	<section id="plah-admin-no-wc">
		<p><?php esc_html_e( 'Product Like and Hide is a WooCommerce extension.', 'product-like-and-hide' ); ?></p>
		<p><?php esc_html_e( 'Please enable the WooCommerce plugin.', 'product-like-and-hide' ); ?></p>
	</section>
<?php } else { ?>
	<section id="plah-admin-form">
		<form method="POST" enctype="multipart/form-data">
            <?php
            woocommerce_admin_fields( self::get_like_settings() );
            woocommerce_admin_fields( self::get_hide_settings() );
            ?>
			<div class="submit-button">
				<?php submit_button(); ?>
			</div>
		</form>
	</section>
<?php }
