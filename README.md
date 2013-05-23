sleepy-mustache
================================================================================

Basic PHP Framework

Sleepy mustache is a PHP framework that comes with solutions for everyday php
challenges.  All the functionality is optional and tries to be as minimalist as
possible.

Included Functionality
================================================================================
* Templating Engine
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
--------------------------------------------------------------------------------
* Robo Caller SOAP API (class.robotalker.php)
* SQL Select to DB Grid (class.dbgrid.php)


Getting Started
================================================================================
There are a few globals you will want to set in the include/globals.php file.

* Setup debugging
* Set Live site URL
* Set DB credentials for live/stage
* Set Emailing info for live/stage
* Setup GA Account for live/state

Sample Code
================================================================================

Templating
--------------------------------------------------------------------------------
Templates reside inside the '/templates/' folder and should end in a .tpl
extension. The templating system works by using placeholders that later get
filled in later. The placeholders must have the following syntax:

	{{ placeholder }}

To use a template you instantiate the template class passing in the template
name. You then bind data to the placeholders and call the Template::show()
method.

	require_once('include/class.template.php');

	$page = new Template('templates/default.tpl');
	$page->bind('title', 'Sleepy Mustache');
	$page->bind('header', 'Hello world!');
	$page->show();

Here is the sample template file (templates/default.tpl)

	<html>
		<head>
			<title>{{ title }}</title>
		</head>
		<body>
			<h1>{{ header }}</h1>
		</body>
	</html>

Singleton PDO DB class (class.db.php)
--------------------------------------------------------------------------------
	$db = DB::getInstance();

Hook and Filter system (class.hooks.php)
--------------------------------------------------------------------------------
	// add a hook point
	$content = Hook::addFilter('update_content', $_POST['content']);

	// Add a module to the hook point--in /modules/<moduleName.php>
	function clean_html ($html) {
		$c = htmlentities(trim($html), ENT_NOQUOTES, "UTF-8", false);
		return $c;
	}

	Hook::applyFilter("update_content", "clean_html");

Basic Emailing (class.mailer.php)
--------------------------------------------------------------------------------
	$m = new Mailer();
	$m->addTo("test@test.com");
	$m->addFrom("from.me@test.com");
	$m->addSubject("This is a test, don't panic.");
	$m->fetchHTML("http://test.com/template.php");
	// OR
	$m->msgText("This is my message.")
	$m->send();

CSV creation (class.csv.php)
--------------------------------------------------------------------------------
	$c = new CSV();
	$data = array(
		'George',
		'Washington'
	);
	$c->add($data);
	$c->save('presidents.csv');


Debugging via Output, Email, and DB (class.debug.php)
--------------------------------------------------------------------------------
	$debug = new Debug();
	$db = DB::getInstance();
	$debug->out($db);

File System Database (class.fsdb.php)
--------------------------------------------------------------------------------
	$fruit = new stdClass();
	$fruit->name = "Apple";
	$fruit->color = "Red";
	$fruit->texture = "Crispy";
	$fruit->price = 0.50;
	$db = new FSDB();
	$db->insert('fruit', $fruit);
	$data = $db->select('fruit', 'name', 'Banana');

IP 2 Country detection
--------------------------------------------------------------------------------
	$i = new IP2CO();
	$countryCode = $i->getCountryCode($_SERVER['REMOTE_ADDR']);
	if ($countryCode != false) {
		echo $countryCode;
	} else {
		echo $_SERVER['REMOTE_ADDR'] . "(" . ip2long($_SERVER['REMOTE_ADDR']) . ") Not found in " . $i->getTable($_SERVER['REMOTE_ADDR']) . ".";
	}

Mobile device detection
--------------------------------------------------------------------------------
	$md = new MobiDetect();
	if ($md->isMobile()) {
		// goto mobile site
	}

Navigation creation
--------------------------------------------------------------------------------
	$topNavData = '{
		"pages": [
			{
				"title": "Nav 1",
				"link": "/nav1/"
			}, {
				"title": "Nav 2",
				"link": "/nav2/",
				"pages": [
					{
						"title": "Subnav 1",
						"link": "/downloads/fpo.pdf",
						"target": "_blank"
					}
				]
			}
		]
	}';
	$topNav = new Navigation($topNavData);
	$topNav->setCurrent($_SERVER['SCRIPT_NAME']);
	// In body somewhere...
	<nav class="top">
		<?php echo $topNav->show(); ?>
	</nav>