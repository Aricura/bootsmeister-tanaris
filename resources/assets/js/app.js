
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */


require('./bootstrap');
require('./smoothScroll');
require('./header');
require('./infographic');



jQuery(function() {
	window.SmoothScroll.initialize();
	window.Header.initialize();
	window.Infographic.initialize();
});
