
window.Header = (function(jQuery, window) {

	/**
	 * Header jQuery element.
	 */
	let element;

	/**
	 * Function to check if this element is currently above the viewport.
	 *
	 * @returns {boolean}
	 */
	let isAboveViewport = function() {
		let carousel = jQuery('#carousel');
		let elementTop = carousel.offset().top;
		let elementBottom = elementTop + carousel.outerHeight();

		let viewportTop = jQuery(window).scrollTop();

		return elementBottom <= viewportTop;
	};

	/**
	 * Initial plugin setup.
	 *
	 */
	let setup = function() {
		element = jQuery('#header');

		// check on scroll if the infographic is in the viewport or not
		jQuery(window).on('resize scroll', function() {
			trigger();
		});

		// trigger once when loaded
		trigger();
	};

	/**
	 * Checks if the header is above the viewport or not
	 *
	 */
	let trigger = function() {
		if (isAboveViewport()) {
			if (!element.hasClass('navbar-fixed-top')) {
				element.addClass('navbar-fixed-top');
				element.next().css({'margin-top': element.height() + 'px'});
			}
		} else {
			if (element.hasClass('navbar-fixed-top')) {
				element.removeClass('navbar-fixed-top');
				element.next().css({'margin-top': '0px'});
			}
		}
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