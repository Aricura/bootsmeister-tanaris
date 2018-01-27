
window.Infographic = (function(jQuery, window) {

	/**
	 * Infographic jQuery element.
	 */
	let element;

	/**
	 * Interval timer id.
	 */
	let interval;

	/**
	 * Current wheel index.
	 */
	let index = -1;

	/**
	 * Flag if the animation should never run again (until browser refresh).
	 *
	 * @type {boolean}
	 */
	let stopForever = false;

	/**
	 * Function to check if this element is currently in the viewport or not.
	 *
	 * @returns {boolean}
	 */
	let isInViewport = function() {
		let elementTop = element.offset().top;
		let elementBottom = elementTop + element.outerHeight();

		let viewportTop = jQuery(window).scrollTop();
		let viewportBottom = viewportTop + jQuery(window).height();

		return elementBottom > viewportTop && elementTop < viewportBottom;
	};

	/**
	 * Initial plugin setup.
	 *
	 */
	let setup = function() {
		element = jQuery('#infographic');

		// check on scroll if the infographic is in the viewport or not
		jQuery(window).on('resize scroll', function() {
			trigger();
		});

		// trigger once when loaded
		trigger();

		// click event handler for all dots
		jQuery('.js-wheel-dot').on('click', function() {
			// get the index of the dot clicked
			index = jQuery('.js-wheel-dot').index(this);
			// animate to this dot
			animate();
			// stop the animation
			clearInterval(interval);
			// stop animations forever
			stopForever = true;
		});
	};

	/**
	 * Checks if the infographic is in the viewport and either plays or pauses the animation.
	 *
	 */
	let trigger = function() {
		if (isInViewport() && !stopForever) {
			play();
		} else {
			pause();
		}
	};

	/**
	 * Pause the animation.
	 */
	let pause = function() {
		// pause the infographic one
		if (element.hasClass('is-active')) {
			element.removeClass('is-active');
			// disable the wheel
			//element.find('.js-wheel').removeClass('is-active');
			// stop the animation
			clearInterval(interval);
		}
	};

	/**
	 * Play the animation.
	 */
	let play = function() {
		// start the animation once
		if (!element.hasClass('is-active')) {
			// enable the infographic
			element.addClass('is-active');
			// enable the wheel
			element.find('.js-wheel').addClass('is-active');

			// immediately start with the first dot
			if (index < 0) {
				setTimeout(function() {
					index = 0;
					animate();
				}, 500);
			}

			// interval to loop through the dots
			interval = setInterval(function() {
				index++;
				animate();
			}, 5000);
		}
	};

	/**
	 * Animate the next/new dot.
	 */
	let animate = function() {
		// get all dots + panels
		let dots = jQuery('.js-wheel-dot');
		let panels = jQuery('.js-infographic-panel');

		// avoid an index overflow
		if (index >= dots.length) {
			index = 0;
		}

		// get the new dot + panel
		let newDot = dots.eq(index);
		let newPanel = panels.eq(index);

		// reset all dots
		dots.removeClass('is-active');

		// highlight the new dot
		newDot.addClass('is-enabled');
		newDot.addClass('is-active');

		// reset all panels
		panels.removeClass('is-active');

		// highlight the new panel
		newPanel.addClass('is-active');
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