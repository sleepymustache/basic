'use strict';

class Sleepy {
  /**
   * Gets a cookie
   * @param  {string} cookieName The name of the cookie
   * @return {string}            The value of the cookie
   */
  getCookie(cookieName) {
    const ARRcookies = document.cookie.split(';');
    for (let i = 0; i < ARRcookies.length; i++) {
      const y = ARRcookies[i].substr(ARRcookies[i].indexOf('=') + 1);
      let x = ARRcookies[i].substr(0, ARRcookies[i].indexOf('='));
      x = x.replace(/^\s+|\s+$/g, '');
      if (x === cookieName) return decodeURI(y);
    }
  }

  /**
   * Gets a querystring from the url
   * @param  {string} key      The querystring key
   * @param  {string} default_ The default if there is none setCookie
   * @return {string}          The value of the querystring referenced by the key
   */
  getQuerystring(key, default_) {
    if (default_ === null) default_ = '';

    key = key.replace(/[[]/, '\\[').replace(/[\]]/, '\\]');

    const regex = new RegExp('[\\?&]' + key + '=([^&#]*)');
    const qs = regex.exec(window.location.href);

    if (qs === null) return default_;

    return qs[1];
  }

  /**
   * Checks if touch events are enabled
   * @return {boolean} Are touch events enabled?
   */
  isTouchDevice() {
    return 'ontouchstart' in window ? 1 : 0;
  }

  /**
   * Loads an array of CSS files then runs a callback
   * @param  {string[]} files    The URLS of the css files to load
   * @param  {function()} callback The callback
   * @return {void}
   */
  loadCSS(files, callback) {
    let filesToLoad = 0;

    function decrementAndCallGlobalCallback() {
      filesToLoad--;

      if (!filesToLoad) {
        if (typeof callback === 'function') {
          callback();
        }
      }
    }

    function appendStylesheet(url) {
      const stylesheets = document.getElementsByTagName('link');

      filesToLoad++;

      for (let i = 0; i < stylesheets.length; i++) {
        if (stylesheets[i].href.search(url) !== -1) {
          decrementAndCallGlobalCallback();
          return;
        }
      }

      const oLink = document.createElement('link');
      oLink.href = url;
      oLink.rel = 'stylesheet';
      oLink.type = 'text/css';

      oLink.onload = () => {
        decrementAndCallGlobalCallback();
      };

      oLink.onreadystatechange = () => {
        if (this.readyState === 'loaded' || this.readyState === 'complete') {
          decrementAndCallGlobalCallback();
        }
      };

      document.getElementsByTagName('head')[0].appendChild(oLink);
    }

    for (let index in files) {
      if (files.hasOwnProperty(index)) {
        appendStylesheet(files[index]);
      }
    }
  }

  /**
   * Waits for the DOM to be ready
   * @param  {function(event)} cb The callback
   * @return {void}
   */
  ready(cb) {
    if (document.attachEvent ? document.readyState === 'complete' : document.readyState !== 'loading') {
      cb();
    } else {
      document.addEventListener('DOMContentLoaded', cb);
    }
  }

  /**
   * Sets a cookie
   * @param  {string} cookieName The name of the cookie
   * @param  {string} value      The value of the cookie
   * @param  {number} exdays     The numbers of days until the cookie expires
   * @return {void}
   */
  setCookie(cookieName, value, exdays) {
    const exdate = new Date();
    exdate.setDate(exdate.getDate() + exdays);
    const CookieValue = encodeURI(value) + (exdays === null ? '' : '; expires=' + exdate.toUTCString());
    document.cookie = cookieName + '=' + CookieValue + '; path=/';
  }
}

export default Sleepy;
