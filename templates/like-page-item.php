<?php
/* Used for the Video bookmark list items
*/

$item_id = $like->product_id;


$like_link = get_the_permalink( $item_id );

if ( ! $like_link ) {
    return;
}

// Get product categories
$product_categories = get_the_terms( $item_id, 'product_cat' );

?>
<li class="liked-product-item" id="liked-product-<?php echo absint( $like->id ); ?>">
    <?php
    if ( has_post_thumbnail( $item_id ) ) {
        ?>
		<div class="thumbnail">
			<a href="<?php echo esc_url( $like_link ); ?>">
                <?php
                echo get_the_post_thumbnail( $item_id, 'large' );
                ?>
			</a>
		</div>
    <?php } ?>
	<div class="details">
		<h3 class="title">
			<a href="<?php echo esc_url( $like_link ); ?>" class="like-link">
                <?php echo esc_html( $like->post_title ); ?>
			</a>
		</h3>
		<div class="meta">
            <?php if ( is_array( $product_categories ) && count( $product_categories ) > 0 ) { ?>
				<div class="post-category">
					<ul>
                        <?php
                        foreach ( $product_categories as $key => $category ) {
                            ?>
							<li>
								<a href="<?php echo get_term_link( $category ); ?>">
                                    <?php echo esc_html( $category->name ); ?>
								</a>
							</li>
                            <?php
                        }
                        ?>
					</ul>
				</div>
            <?php } ?>

			<div class="post-content">
                <?php
                $content = get_the_content( 'Read more', false, $item_id );
                $content = apply_filters( 'the_content', $content );
                echo wp_trim_words( $content, 32, '...' );
                ?>
			</div>
		</div>
	</div>
	<div class="actions">
		<span class="like-page-button liked" data-product-id="<?php echo absint( $like->product_id ); ?>">
			<span class="dashicons dashicons-trash"></span>
		</span>
	</div>
</li>
