/*! Product Like and Hide
 */

/**
 * @summary     Product Like and Hide
 * @description Frontend JS for Product Like and Hide
 * @version     1.0.0
 * @file        product-like-and-hide
 * @author      Giannis Kipouros
 * @contact     https://gianniskipouros.com
 *
 */


(function ($) {
	'use strict';

	// On load
	$(document).ready(function ($) {

		// Like the product
		$('.product-like-button, .like-page-button').on('click touched', function (e) {
			e.preventDefault();

			let button = $(this);
			let productID = button.data('product-id');

			let likeType = 'like';
			if(button.hasClass('liked')) {
				likeType = 'unlike';
			}

			//Send AJAX request
			let data = new FormData();
			data.append('productID', productID);
			data.append('type', likeType);
			data.append("nonce", plahAJAXObj.nonce);
			data.append("action", 'plah_like_product');

			$.ajax({
				type: 'POST',
				url: plahAJAXObj.ajax_url,
				data: data,
				context: this,
				cache: false,
				dataType: 'json',
				contentType: false,
				processData: false,
				error: function (jqXHR, textStatus, errorThrown) {
					console.error("The following error occured: " + textStatus, errorThrown);
					return;
				},
				success: function (response) {
					if(response.success) {


						// Toggle the "liked" class on the button
						// and update the text
						if(typeof response.type !== undefined) {
							if('liked' === response.type) {
								button.addClass('liked');
								button.find('.like-count').text(response.likes);
							} else {
								button.removeClass('liked');
								button.find('.like-count').text(button.data('like-text'));
								if($(button).closest('li.liked-product-item').length > 0) {
									$(button).closest('li.liked-product-item').slideUp();
								}
							}
						}
					}

					// Display response text if needed
					if(typeof response.show_notice !== undefined && response.show_notice) {
						// Get the modal
						let modal = document.getElementById("plah-notice-modal");
						modal.querySelector('.modal-body').innerHTML = response.content;

						modal.style.display = "block";
					}
				},
			});
		});

		// Hide the product
		$('.product-hide-button').on('click touched', function (e) {
			e.preventDefault();

			let button = $(this);
			let productID = button.data('product-id');

			let likeType = 'hide';
			if(button.hasClass('hidden')) {
				likeType = 'unhide';
			}

			//Send AJAX request
			let data = new FormData();
			data.append('productID', productID);
			data.append('type', likeType);
			data.append("nonce", plahAJAXObj.nonce);
			data.append("action", 'plah_hide_product');

			$.ajax({
				type: 'POST',
				url: plahAJAXObj.ajax_url,
				data: data,
				context: this,
				cache: false,
				dataType: 'json',
				contentType: false,
				processData: false,
				error: function (jqXHR, textStatus, errorThrown) {
					console.error("The following error occured: " + textStatus, errorThrown);
					return;
				},
				success: function (response) {
					if(response.success) {

						// Toggle the "liked" class on the button
						// and update the text
						if(typeof response.type !== undefined) {
							if('hidden' === response.type) {
								button.addClass('hidden');
								button.find('.hide-text').text(button.data('unhide-text'));
								if($(button).closest('ul.products').length > 0) {
									$(button).closest('li.product').slideUp();
								}
							} else {
								button.removeClass('hidden');
								button.find('.hide-text').text(button.data('hide-text'));
							}
						}
					}

					// Display response text if needed
					if(typeof response.show_notice !== undefined && response.show_notice) {
						// Get the modal
						let modal = document.getElementById("plah-notice-modal");
						modal.querySelector('.modal-body').innerHTML = response.content;

						modal.style.display = "block";
					}
				},
			});
		});

		// Notice modal functionality
		function plah_notice_modal() {
			// Get the modal
			let modal = document.getElementById("plah-notice-modal");


			// Get the <span> element that closes the modal
			let span = document.getElementsByClassName("close")[0];

			// When the user clicks on <span> (x), close the modal
			span.onclick = function () {
				modal.style.display = "none";
			}

			// When the user clicks anywhere outside of the modal, close it
			window.onclick = function (event) {
				if(event.target == modal) {
					modal.style.display = "none";
				}
			}
		}
		// Init modal
		plah_notice_modal();

	}); // End document ready
})(jQuery);
