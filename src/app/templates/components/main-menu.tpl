<?php

$mainMenu = '{
  "pages": [
    {
      "title": "Home",
      "link": "/",
      "rel": "noopener"
    }, {
      "title": "Wiki",
      "link": "https://github.com/sleepymustache/basic/wiki",
      "rel": "noopener",
      "pages": [
        {
          "title": "Installation",
          "link": "https://github.com/sleepymustache/basic/wiki/installation",
          "target": "_blank",
          "rel": "noopener"
        },
        {
          "title": "Templates",
          "link": "https://github.com/sleepymustache/basic/wiki/templates",
          "target": "_blank",
          "rel": "noopener"
        }, {
          "title": "Debugging",
          "link": "https://github.com/sleepymustache/basic/wiki/debugging",
          "target": "_blank",
          "rel": "noopener"
        }, {
          "title": "Modules",
          "link": "https://github.com/sleepymustache/basic/wiki/modules",
          "target": "_blank",
          "rel": "noopener"
        }
      ]
    }, {
      "title": "Nav 3",
      "link": "/nav3/"
    }
  ]
}';

$mainNav = new \Module\Navigation\Builder($mainMenu);
?>

<nav class="main">
  <?= $mainNav->show(); ?>
</nav>
