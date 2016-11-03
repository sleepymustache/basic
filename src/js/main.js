/*global define, requirejs */

requirejs.config({
	enforceDefine: true,
	paths: {
		jquery: [
			'//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min',
			'jquery-2.1.3.min'
		]
	}
});

define(['jquery', 'sleepy'], ($, Sleepy) => {
	var SM = new Sleepy();
	window.SM = SM;
	$(() => {
		$('body').addClass(SM.isTouchDevice() ? 'touchable': '');
		debugger;
	});
});
