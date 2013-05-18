sleepy-mustache
===============

Basic PHP Framework

Sleepy mustache is a PHP framework that comes with solutions for everyday php
challenges.  All the functionality is optional and tries to be as minimalist as
possible.

Included Functionality
---------------------
* Singleton PDO DB class (class.db.php)
* Hook and Filter system (class.hooks.php)
* Basic Emailing (class.mailer.php)
* CSV creation (class.csv.php)
* Debugging via Output, Email, and DB (class.debug.php)
* File System Database (class.fsdb.php)
* IP 2 Country detection
* Mobile device detection
* Navigation creation

Misc
----
* Robo Caller SOAP API (class.robotalker.php)
* SQL Select to DB Grid (class.dbgrid.php)


Getting Started
---------------
There are a few globals you will want to set in the include/globals.php file.

* Setup debugging
* Set Live site URL
* Set DB credentials for live/stage
* Set Emailing info for live/stage
* Setup GA Account for live/state