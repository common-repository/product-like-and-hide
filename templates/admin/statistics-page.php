<?php
/**
 * Template file for WP admin settings page
 */
include( PLAH_PLUGIN_PATH . '/templates/admin/admin-header.php' );
?>
<?php
/**
 * Display the Like statistics
 * Daily, Weekly, Monthly and Yearly
 */
if ( $is_like_enabled ) {
    ?>
	<div class="plah-wrapper">

		<div class="plah-description">
			<h2><img src="<?php echo esc_url( $like_icon ); ?>" width="24" height="24">
                <?php esc_html_e( 'Like Statistics', 'product-like-and-hide' ); ?></h2>
			<p><?php _e( 'Here you can view the <strong>daily, weekly, monthly and yearly</strong> most <strong>liked</strong> products.', 'product-like-and-hide' ); ?></p>
		</div>
	</div>
	<div class="like-stats plah-admin-stats-wrapper">
        <?php if ( is_array( $like_prod_stats ) && count( $like_prod_stats ) > 0 ) { ?>
			<ul class="column-wrapper stats">
                <?php foreach (
                    $like_prod_stats

                    as $range => $products
                ) { ?>
					<li>
						<h3>

                            <?php
                            $time_length = absint( $range );
                            if ( strstr( $range, 'h' ) ) {
                                $time_length .= " " . __( 'hours',
                                        'product-like-and-hide' );
                            } else {
                                $time_length .= " " . __( 'days',
                                        'product-like-and-hide' );
                            }
                            printf( __( 'Last %s', 'product-like-and-hide' ),
                                $time_length ); ?>
						</h3>
                        <?php if (
                            is_array( $products ) ) {
                            if ( count( $products ) > 0 ) { ?>
								<table class="stat-product-list">
									<tr>
										<th class="counter header">
                                            <?php esc_html_e( 'Likes',
                                                'product-like-and-hide' ); ?>
										</th>
										<th class="product-link header">
                                            <?php esc_html_e( 'Product', 'product-like-and-hide' ); ?>
										</th>
									</tr>
                                    <?php foreach ( $products as $product ) {
                                    ?>
									<tr>
										<td class="counter">
											<img src="<?php echo esc_url( $like_icon ); ?>" width="16" height="16">
                                            <?php echo absint( $product['counter'] ); ?>
										</td>
										<td class="product-link">
											<a href="<?php echo get_the_permalink( $product['product_id'] ); ?>"><?php echo esc_html( $product['post_title'] ); ?></a>
										</td>
                                        <?php } ?>
								</table>
                            <?php } else { ?>
								<p class="no-products"><?php
                                    _e( 'No product Likes for this timeframe.',
                                        'product-like-and-hide' ); ?>
								</p>
                                <?php
                            }
                        }
                        ?>
					</li>
                <?php } ?>
			</ul>
        <?php } ?>
	</div>
    <?php
}

/**
 * Display the Hide statistics
 * Daily, Weekly, Monthly and Yearly
 */
if ( $is_hide_enabled ) {
    ?>
	<div class="plah-wrapper">

		<div class="plah-description">
			<h2>
				<img src="<?php echo esc_url( $hide_icon ); ?>" width="24" height="24">
                <?php esc_html_e( 'Hide Statistics', 'product-like-and-hide' ); ?></h2>
			<p><?php _e( 'Here you can view the <strong>daily, weekly, monthly and yearly</strong> most <strong>hidden</strong> products.', 'product-like-and-hide' ); ?>
			</p>
		</div>
	</div>
	<div class="hide-stats plah-admin-stats-wrapper">
        <?php
        if ( is_array( $hide_prod_stats ) &&
             count( $hide_prod_stats ) > 0 ) {
            ?>
			<ul class="column-wrapper stats">
                <?php foreach ( $hide_prod_stats as $range => $products ) { ?>
					<li>
						<h3>
                            <?php
                            $time_length = absint( $range );
                            if ( strstr( $range, 'h' ) ) {
                                $time_length .= " " . __( 'hours',
                                        'product-like-and-hide' );
                            } else {
                                $time_length .= " " . __( 'days',
                                        'product-like-and-hide' );
                            }
                            printf( __( 'Last %s', 'product-like-and-hide' ),
                                $time_length ); ?>
						</h3>
                        <?php if ( is_array( $products ) ) { ?>
                            <?php if ( count( $products ) > 0 ) { ?>
								<table class="stat-product-list">
									<tr>
										<th class="counter header">
                                            <?php esc_html_e( 'Hides',
                                                'product-like-and-hide' ); ?>
										</th>
										<th class="product-link header">
                                            <?php esc_html_e( 'Product',
                                                'product-like-and-hide' ); ?>
										</th>
									</tr>
                                    <?php foreach ( $products

                                    as $product ) { ?>
									<tr>
										<td class="counter">
											<img src="<?php echo esc_url( $hide_icon ); ?>" width="16" height="16">
                                            <?php echo absint( $product['counter'] ); ?>
										</td>
										<td class="product-link">
											<a href="<?php echo get_the_permalink( $product['product_id'] ); ?>">
                                                <?php echo esc_html( $product['post_title'] ); ?></a>
										</td>
                                        <?php } ?>
								</table>
                            <?php } else { ?>
								<p class="no-products"><?php
                                    _e( 'No product hides for this timeframe.',
                                        'product-like-and-hide' ); ?>
								</p>
                                <?php
                            }
                        } ?>
					</li>
                <?php }
                ?>
			</ul>
        <?php } ?>
	</div>
    <?php
}

/**
 * Display the Disabled options notice
 */
if ( ! $is_hide_enabled && ! $is_like_enabled ) {
    ?>
	<div class="plah-wrapper">
		<div class="plah-description">
			<h2>
                <?php esc_html_e( 'Like and Hide settings are disabled', 'product-like-and-hide' ); ?></h2>
			<p><?php
                printf( __( 'Please enable the <a href="%s">Like and/or Hide Settings</a> to track likes and hides and display statistics.', 'product-like-and-hide' ),
                    admin_url( 'admin.php?page=plah_settings' )
                );
                ?>
			</p>
		</div>
	</div>
    <?php
}
