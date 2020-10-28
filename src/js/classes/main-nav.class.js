'use strict';

class MainNav {
  /**
   * Initialized the Main Navigation
   */
  constructor(sel) {
    let mainMenu = document.querySelector(sel);
    
    if (mainMenu) {
      const toggle = mainMenu.querySelector('.toggle');
      toggle.addEventListener('click', () => {
        mainMenu.classList.toggle('opened');
      });

      const hasChildren = mainMenu.querySelectorAll('.has-children');

      hasChildren.forEach((child) => {
        child.addEventListener('click', (e) => {
          if (e.offsetX > e.target.clientWidth - 42) {
            e.preventDefault();
            e.stopPropagation();
            child.classList.toggle('opened');
          }
        });
      });
    }
  }
}

export default MainNav;
