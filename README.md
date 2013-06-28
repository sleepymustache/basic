[] (\mainpage <|:{)

sleepy-mustache
================================================================================

-Doxygen [Documentation] (http://www.sleepymustache.com/documentation/html/index.html) is available.

Sleepy mustache is a PHP framework that comes with solutions for everyday php
challenges.  All the functionality is optional and tries to be as minimalist as
possible.

Included Functionality
--------------------------------------------------------------------------------

* Hooks
* Templating Engine
* Singleton PDO DB class
* Emailing
* CSV creation
* Debugging via Output, Email, and DB
* File System Database
* IP 2 Country detection
* Mobile device detection
* Navigation

### Misc

* Robo Caller SOAP API (class.robotalker.php)
* SQL Select to DB Grid (class.dbgrid.php)


Getting Started
--------------------------------------------------------------------------------
There are a few globals you will want to set in the include/globals.php file.

* Setup debugging
* Set Live site URL
* Set DB credentials for live/stage
* Set Emailing info for live/stage
* Setup GA Account for live/state

Sample Code
--------------------------------------------------------------------------------
### Hooks

The *Hooks* system is made up of *hook filters* and *hook actions*. *Hook
actions* are points in the code where you can assign functions to run. For
example, we can put a *hook action* after a record is saved to the database,
then assign a function to the *hook action* that will send an email after
the DB update.

	// Save to the database
	$db->save();

	// add a hook action
	$content = Hook::addAction('record_saved');

	// Add a function to the hook action
	function send_email() {
		// send an email saying a record was updated
	}

	Hook::doAction(
		'record_saved',
		'send_email'
	);

*Hook filters* are similar to *hook actions* but pass data as
parameters to the functions that get assigned to the hook. After manipulating
this data you should return the edited data back to the program.

	// add a hook filter
	$content = Hook::addFilter('update_content', $_POST['content']);

	// Add a function to the hook filter
	function clean_html ($html) {
		$c = htmlentities(trim($html), ENT_NOQUOTES, "UTF-8", false);
		return $c;
	}

	Hook::applyFilter(
		'update_content',
		'clean_html'
	);

The *modules* directory provides a convenient location to put code that utilized
the hooks system. Code inside of the *modules* directory are automatically added
to the program at runtime.


### Templating

Templates reside inside the *'/templates/'* folder and should end in a .tpl
extension. The templating system works by using placeholders that later get
filled in later. The placeholders must have the following syntax:

	{{ placeholder }}

To use a template you instantiate the template class passing in the template
name. You then bind data to the placeholders and call the *Template::show()*
method.

	require_once('include/sleepy.php');

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
			<p>This page has been viewed {{ hits }} times.</p>
		</body>
	</html>

We added a *{{ hits }}* placeholder in the template above. We can add that
functionality using Hooks.

	// filename: /modules/hits.php
	function hook_render_placeholder_topNav() {
		$hits = new FakeClass();

		return $hits->getTotal();
	}

	Hook::applyFilter(
		'render_placeholder_hits',
		'hook_render_placeholder_hits'
	);

The first parameter of *Hook:applyFilter*, the hook filter, ends in 'hits' which
correlates to the name of the placeholder. This hook filter is defined in
'*class.template.php*'. The second parameter is the name of the function to run
when we render the placeholder.

You can iterate through data using #each placeholders

	// Bind the data like this
	$page->bind('fruits', array(
		array(
			"name" => "apple",
			"color" => "red"
		), array(
			"name" => "banana",
			"color" => "yellow"
		)
	));

	// in the template
	{{ #each fruit in fruits }}
		<p>I like {{ fruit.color }}, because my {{ fruit.name }} is {{ fruit.color }}.</p>
	{{ /each }}

### Databases

The database connection settings are defined in the */include/global.php* file.
After the *LIVE_URL* is set in *global.php* the framework will detect which DB
to use based on the current URL.

To get a database instance, use:

	$db = DB::getInstance();

The DB class is static and will automatically handle suppressing multiple
instances.

### Sending emails

The Mailer class simplifies sending emails by generating headers for you
and using an easy to use object to clearly define your email. The Mailer can
send emails using an HTML template or text.

	$m = new Mailer();
	$m->addTo("test@test.com");
	$m->addFrom("from.me@test.com");
	$m->addSubject("This is a test, don't panic.");
	$m->fetchHTML("http://test.com/template.php");
	// OR
	$m->msgText("This is my message.")
	$m->send();

### CSV

The CSV class ensures that all records are properly escaped and allows you to
easily manipulate data inside of a CSV file.

	$c = new CSV();
	$data = array(
		'George',
		'Washington'
	);
	$c->add($data);

	// Saves to the filesystem
	$c->save('presidents.csv');

	// OR

	// Sends the file to the browser, does not save to the filesystem
	$c->show();


### Debugging

The *Debug* static class allows you to debug on-screen, via email, or by logging
to a database.

	$db = DB::getInstance();
	Debug::out($db);

### File System Database (class.fsdb.php)

Sometimes using a database is overkill.  A simple solution is to use the *FSDB*.
It is very simple and does not allow complex queries, however it is fast, easy
to use and requires no setup, except checking that proper permissions are set.

	$fruit = new stdClass();

	$fruit->name = "Apple";
	$fruit->color = "Red";
	$fruit->texture = "Crispy";
	$fruit->price = 0.50;

	$db = new FSDB();

	$db->insert('fruit', $fruit);
	$data = $db->select('fruit', 'name', 'Banana');

### Country detection

*Country detection* uses the *FSDB* to do a quick lookup of the current country.

	$i = new IP2CO();

	$countryCode = $i->getCountryCode($_SERVER['REMOTE_ADDR']);

	if ($countryCode != false) {
		echo $countryCode;
	} else {
		echo $_SERVER['REMOTE_ADDR'] . "(" . ip2long($_SERVER['REMOTE_ADDR']) . ") Not found in " . $i->getTable($_SERVER['REMOTE_ADDR']) . ".";
	}

### Mobile detection

Mobile detection is done by comparing the UA (user-agent) to a list of currently
available mobile and tablet UA.

	$md = new MobiDetect();

	if ($md->isMobile()) {
		// goto mobile site
	}

### Navigation

The navigation is generated by from JSON. It renders the JSON into a unordered
list with some classes added for the current active page.

	// Add a placeholder in your template
	{{ TopNav }}

	// Create a php file in */modules/*
	require_once('include/class.navigation.php');

	// create a function to add to the *hook filter*
	function hook_render_placeholder_TopNav() {

		// Page data is passed via JSON
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

		return $topNav->show();
	}

	Hook::applyFilter(
		'render_placeholder_TopNav',
		'hook_render_placeholder_TopNav'
	);