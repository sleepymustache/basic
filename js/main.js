/*global Modernizr requirejs require console*/
/*jshint node:false*/

requirejs.config({
	enforceDefine: true,
	paths: {
		jquery: [
			'//ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min',
			'jquery-1.8.2.min'
		]
	}
});

define(['jquery', 'sleepy'], function ($, SM) {

});