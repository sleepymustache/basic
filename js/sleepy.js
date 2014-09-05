define([], function () {
	return {
		'getQuerystring': function (key, default_) {
			var regex,
				qs;
			if (default_ === null) {
				default_ = "";
			}
			key = key.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
			regex = new RegExp("[\\?&]" + key + "=([^&#]*)");
			qs = regex.exec(window.location.href);
			if (qs === null) {
				return default_;
			} else {
				return qs[1];
			}
		},

		is_touch_device: function () {
			return ('ontouchstart' in window) ? 1 : 0;
		},

		'loadCSS': function (files, callback) {
			var filesToLoad = 0,
				index,
				newStylesheetIndex = document.styleSheets.length - 1,

				appendStylesheet = function (url) {
					var i,
						oLink,
						stylesheets;

					filesToLoad++;

					stylesheets = document.getElementsByTagName('link');

					for (i = 0; i < stylesheets.length; i++) {
						if (stylesheets[i].href.search(url) !== -1) {
							decrementAndCallGlobalCallback();
							return;
						}
					}

					oLink = document.createElement("link");
					oLink.href = url;
					oLink.rel = "stylesheet";
					oLink.type = "text/css";

					oLink.onload = oLink.onload = function () {
						decrementAndCallGlobalCallback();
					};

					oLink.onreadystatechange = function () {
						if (this.readyState == 'loaded' || this.readyState == 'complete') {
							decrementAndCallGlobalCallback();
						}
					};

					document.getElementsByTagName("head")[0].appendChild(oLink);
				},

				decrementAndCallGlobalCallback = function () {
					filesToLoad--;

					if (!filesToLoad) {
						if (typeof callback === 'function') {
							callback();
						}
					}
				};

			for (index in files) {
				appendStylesheet(files[index]);
			}
		},

		// sets a cookie
		'setCookie': function (c_name, value, exdays) {
			var exdate,
				c_value;
			exdate = new Date();
			exdate.setDate(exdate.getDate() + exdays);
			c_value = escape(value) + ((exdays === null) ? "" : "; expires=" + exdate.toUTCString());
			document.cookie = c_name + "=" + c_value + "; path=/";
		},

		// reads a cookie
		'getCookie': function (c_name) {
			var i,
				x,
				y,
				ARRcookies = document.cookie.split(";");
			for (i = 0;i < ARRcookies.length; i++) {
				x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
				y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
				x = x.replace(/^\s+|\s+$/g, "");
				if (x === c_name) {
					return unescape(y);
				}
			}
		},
	};
});