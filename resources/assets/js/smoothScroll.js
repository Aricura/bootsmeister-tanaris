
window.SmoothScroll = (function(jQuery, window) {

	/**
	 * Initial plugin setup.
	 *
	 */
	let setup = function() {
		jQuery('.js-smooth-scroll').on('click', function(e) {
			// prevent the default action
			e.preventDefault();

			// get the target object
			let target = jQuery(jQuery(this).attr('href'));

			// animate to the target object
			jQuery('html, body').animate({
				scrollTop: target.offset().top
			}, 500);
		});
	};

	/**
	 * Initializes the plugin once.
	 *
	 * @private
	 */
	let _init = function() {
		let interval = window.setInterval(function() {
			setup();
			window.clearInterval(interval);
		}, 100);
	};

	return {
		initialize: _init
	};
})(jQuery, window);