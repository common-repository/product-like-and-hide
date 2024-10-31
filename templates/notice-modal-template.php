<?php
/**
 * The template for displaying Like/Hide button response.
 *
 * This template can be overridden by using the filter
 * 'plah_add_notice_modal'
 *
 * HOWEVER, on occasion we might need to update template files, and you
 * will need to use the updated template to maintain compatibility.
 * We try to do this as little as possible, but it does
 * happen.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!-- The Modal -->
<div id="plah-notice-modal" class="plah-modal">

    <!-- Modal content -->
	<div class="modal-content">
		<div class="modal-header">
			<span class="close">&times;</span>
			<h2><?php _e('Product Like', 'product-like-and-hide');?></h2>
		</div>
		<div class="modal-body">
			[content]
		</div>
	</div>

</div>
