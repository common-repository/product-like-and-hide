<?php
/**
 * The template for displaying the like button on the product page.
 *
 * This template can be overridden by using the filter
 * 'plah_like_button_template'
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
?>
<button class="plah-button product-like-button <?php echo $like_class; ?>" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-like-text="<?php echo $like_button_text; ?>">
	<span class="like-icon">
		<img src="<?php echo esc_url( $icon ); ?>" width="16" height="16">
	</span>
    <?php
    // If user has liked show the counter instead of the Like button text
    if ( $has_user_liked ) {
        $like_button_text = $likes;
    }
    ?>
	<span class="like-count"><?php echo esc_html( $like_button_text ); ?></span>

</button>


