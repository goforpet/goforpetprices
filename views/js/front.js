$(document).ready(function() {
	$('.goforpet-prices .qty input[type=number]').TouchSpin({
		decimals: 0,
		buttonup_class: 'btn btn-default fa fa-plus',
		buttondown_class: 'btn btn-default fa fa-minus'
	});
	var gfppImagesContainer = $('.product-images').html();
	var gfppImage = $('.js-qv-product-cover, .js-modal-product-cover').attr('src');
	$('.goforpet-prices .product-add-to-cart').hover(
		function(e) {
			var images = $(e.target).data('product-images');
			if (images) {
				var $container = $('.product-images');
				$container.html('');
				$(images).each(function(i, e) {
					var selected = '';
					if (i == 0) {
						selected = ' selected';
						$('.js-qv-product-cover, .js-modal-product-cover').attr('src', e.large);
					}
					$container.append(
						'<li class="thumb-container"><img class="thumb js-thumb' +
							selected +
							'" data-image-medium-src="' +
							e.medium +
							'" data-image-large-src="' +
							e.large +
							'" src="' +
							e.thumb +
							'" alt="" title="" width="100" itemprop="image"/></li>'
					);
				});
			}
		},
		function() {
			$('.product-images').html(gfppImagesContainer);
			$('.js-qv-product-cover, .js-modal-product-cover').attr('src', gfppImage);
		}
	);
});
