<div class="liked-products-wrapper">
	<div class="liked-products-filter-wrapper">
		<form class="form" method="get">
			<input name="like-search" type="text" class="field" value="<?php echo esc_html( $keyword ); ?>"
				   placeholder="<?php _e( 'Search in product titles...',
                       'product-like-and-hide' ); ?>"/>

			<input type="submit" value="<?php _e( 'Filter', 'product-like-and-hide' ); ?>"/>
		</form>
	</div>
    <?php
    if ( $total > 0 ) {
        ?>

		<ul class="liked-products-items-wrapper">
            <?php
            // Show the bookmarks
            foreach ( $likes as $like ) {
                include( apply_filters( 'plah_like_page_item', PLAH_PLUGIN_PATH . '/templates/like-page-item.php' ) );
            }
            ?>
		</ul>

		<div class="pagination-wrapper">
            <?php
            // Show pagination
            if ( $total_page > 1 ) {
                ?>

				<span class="pagination-links">
								<?php
                                echo paginate_links( array(
                                    'base'      => add_query_arg( 'cpage', '%#%' ),
                                    'format'    => '',
                                    'prev_text' => __( '&lt;' ),
                                    'next_text' => __( '&gt;' ),
                                    'total'     => $total_page,
                                    'current'   => $cpage
                                ) )
                                ?>
							</span>
            <?php } ?>
			<span class="pagination-text">
				<?php
                printf( __( 'Page <strong>%d</strong> of <strong>%d</strong>',
                    'product-like-and-hide' ), $cpage, $total_page );
                ?>
			</span>
		</div>
        <?php
    } else {
        ?>
		<p class="no-results">
            <?php
            _e( 'Sorry! No products were found. Please use the like button on the product page to add more products to the list.', 'product-like-and-hide' );
            ?>
		</p>
        <?php
    }
    ?>

</div>
