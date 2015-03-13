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

define(['jquery', 'sleepy'], function ($, SM) {
	'use strict';

	$(function () {
		$('body').addClass((SM.isTouchDevice()) ? 'touchable': '');
	});
});