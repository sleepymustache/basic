<?php

$topMenu = '{
  "pages": [
    {
      "title": "Github",
      "link": "#",
      "rel": "noopener"
    }, {
      "title": "Documentation",
      "link": "#",
      "rel": "noopener"
    }
  ]
}';

$topNav = new \Module\Navigation\Builder($topMenu);
?>

<nav class="top">
  <?= $topNav->show(); ?>
</nav>
