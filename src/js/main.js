'use strict';

import Sleepy from './classes/sleepy.class.js';
import MainNav from './classes/main-nav.class';

const SM = new Sleepy();

SM.ready(() => {
  new MainNav('nav.main');

  document.getElementsByTagName('body')[0].className =
    (SM.isTouchDevice()) ? 'touchable' : ''; 
});
