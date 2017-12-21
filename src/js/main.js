'use strict';

import Sleepy from './classes/sleepy.class.js';

const SM = new Sleepy();

SM.ready(() => {
  document.getElementsByTagName('body')[0].className = (SM.isTouchDevice()) ? 'touchable' : '';
});
