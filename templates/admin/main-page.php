<?php
/**
 * Template file for WP admin settings page
 */
include( PLAH_PLUGIN_PATH . '/templates/admin/admin-header.php' );
?>
	<div class="plah-wrapper">
		<div class="plah-description">
			<h2><?php esc_html_e( 'Welcome to Product Like and Hide', 'product-like-and-hide' ); ?></h2>
			<p><?php _e( 'The <strong>Product Like and Hide</strong> is a WooCommerce add-on that allows customers to <strong>like</strong> or <strong>hide products</strong> on your store. Customers can easily "like" a product by <strong>clicking the like button</strong> on the product page.', 'product-like-and-hide' ); ?> <?php _e( 'This feature can be used to <strong>create a wishlist, or to simply show which products are the most popular</strong> among customers. ', 'product-like-and-hide' ); ?></p>
			<p><?php _e( 'Additionally, <strong>Product Like and Hide</strong> also allow customers to <strong>hide products</strong> that they are not interested in, which can help to <strong>declutter the browsing experience</strong>.', 'product-like-and-hide' ); ?></p>
			<p><?php _e( '<strong>Product Like and Hide</strong> helps customers find the products they love faster, and improve their overall user experience on your website.', 'product-like-and-hide' ); ?></p>
		</div>
		<div class="plah-features">
			<h3><?php _e( "Features", 'product-like-and-hide' ); ?></h3>
			<ul>
				<li>&#9989; <?php _e( 'Allow customers to like products.', 'product-like-and-hide' ); ?></li>
				<li>&#9989; <?php _e( 'Allow customers to hide products.', 'product-like-and-hide' ); ?></li>
				<li>&#9989; <?php _e( 'Display the number of likes on the product page.', 'product-like-and-hide' ); ?></li>
				<li>&#9989; <?php _e( 'A Shortcode that displays the user\'s liked products on a page.', 'product-like-and-hide' ); ?></li>
				<li>&#9989; <?php _e( 'Display a login or register message to not logged-in users that try to like a product.', 'product-like-and-hide' ); ?></li>
				<li>&#9989; <?php _e( 'Daily, Weekly, Monthly and Yearly admin statistics of most liked and hidden products.', 'product-like-and-hide' ); ?></li>

				<li>&#9989; <?php _e( 'Great support.', 'product-like-and-hide' ); ?></li>
			</ul>

		</div>
		<div class="shortcodes">
			<h3><?php _e( "Shortcodes", 'product-like-and-hide' ); ?></h3>
			<p><?php _e( "<strong>[plah_user_likes_page]</strong>: Displays the current user's liked products", 'product-like-and-hide' ); ?></p>
		</div>
		<div class="plah-link-to-settings">
			<h3><?php _e( "Settings", 'product-like-and-hide' ); ?></h3>
			<p><a href="<?php echo get_admin_url( '', 'admin.php?page=plah_settings' ); ?>"><?php
                    $text = 'Configure Product Like and Hide settings now!';
                    esc_html_e( $text ); ?>
				</a>
			</p>
		</div>
		<div class="plah-description">
			<h2><?php esc_html_e( 'Support', 'product-like-and-hide' ); ?></h2>
			<p><?php esc_html_e( 'Let our support team help you with any problem or inquiry you might have.', 'product-like-and-hide' ); ?></p>
			<p><a href="https://wordpress.org/support/plugin/product-like-and-hide/"><?php esc_html_e( 'WordPress.org Forum', 'product-like-and-hide' ); ?></a></p>
		</div>
		<div class="plah-description">
			<h2><?php esc_html_e( 'Like us?', 'product-like-and-hide' ); ?></h2>
			<p><?php esc_html_e( 'If you like using Product Like and Hide please leave us a 5-star rating on our WordPress plugin directory plugin page. It will help us a lot!', 'product-like-and-hide' ); ?></p>
			<p><a href="https://wordpress.org/support/plugin/product-like-and-hide/reviews/#new-post"><?php esc_html_e( 'Rate Us', 'product-like-and-hide' ); ?></a></p>
		</div>
	</div>
<?php
