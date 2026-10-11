<?php
/**
 * Index Page
 *
 * PHP version 7.0.0
 *
 * @category Page 
 * @package  Sleepy
 * @author   Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @license  http://opensource.org/licenses/MIT; MIT
 * @link     https://sleepymustache.com
 */

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/sleepy/bootstrap.php';

use \Sleepy\Core\Template;

$page = new Template('homepage');

// SEO
$page->bind(
    [
        'title'       => 'Sleepy Mustache',
        'description' => 'This is the description',
        'keywords'    => "log, sleepy mustache, framework"
    ]
);

// Content
$page->bind('header', 'sleepy<span>MUSTACHE</span>');
$page->bind(
    'teasers',
    [
        [
            "title"   => "Getting Started",
            "image"   => "https://unsplash.com/photos/cUJc1mb3KVg/download?w=320",
            "link"    => 'http://www.sleepymustache.com/',
            "author"  => "Jaime A. Rodriguez",
            "date"    => date('m/d/Y', time()),
            "description" => "
                Congratulations on successfully installing sleepyMUSTACHE! You can
                visit the <a
                href=\"http://www.sleepymustache.com/documentation/index.html\">
                documentation page</a> to learn more or hit the ground running by
                viewing the <a
                href=\"http://www.sleepymustache.com/#getting-started\">getting
                started</a> section.",
            "tags"    => [
                [
                    'name' => "Configuration",
                    'link' => "http://www.sleepymustache.com/#getting-started"
                ]
            ]
        ], [
            "title"   => "Sample Modules",
            "image"   => "https://unsplash.com/photos/MixbiPC3AEE/download?w=320",
            "link"    => "#",
            "author"  => "Jaime A. Rodriguez",
            "date"    => date('m/d/Y', time() - 30 * 24 * 60 * 60),
            "description" => "
                By default there are 2 sample modules included with the framework.
                These modules demonstrate how to create your own modules, and
                implement existing functionality. You may safely delete them.",
            "tags"    => [
                [
                    'name' => "modules",
                    'link' => "http://www.sleepymustache.com/#default-modules"
                ], [
                    'name' => "fixes",
                    'link' =>
                        "https://github.com/jaimerod/sleepy-mustache/commits/master"
                ]
            ]
        ]
    ]
);

$page->show();
