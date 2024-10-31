<?php
/**
 * The template for displaying the hide button on the product page.
 *
 * This template can be overridden by using the filter
 * 'plah_hide_button_template'
 *
 * HOWEVER, on occasion we might need to update template files and you
 * will need to use the updated template to maintain compatibility.
 * We try to do this as little as possible, but it does
 * happen.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( ! is_singular( 'product' ) ) {
    ?>
	<div class="clear"></div>
    <?php
}
?>
<button class="plah-button product-hide-button <?php echo esc_html( $hide_class ); ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-hide-text="<?php esc_html_e( $hide_button_text ); ?>" data-unhide-text="<?php esc_html_e( $unhide_button_text ); ?>">
	<span class="hide-icon">
		<img src="<?php echo esc_url( $icon ); ?>" width="16" height="16">
	</span>
    <?php
    // If user has hided show the counter instead of the hide button text
    if ( $has_user_hidden ) {
        $hide_button_text = $unhide_button_text;
    }
    ?>
	<span class="hide-text"><?php esc_html_e( $hide_button_text ); ?></span>
</button>


