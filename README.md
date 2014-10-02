[] (\mainpage <|:{)

sleepyMUSTACHE
===============================================================================

Doxygen [Documentation] (http://www.sleepymustache.com/documentation/html/index.html) is available.

sleepyMUSTACHE is a PHP micro framework that has solutions for everyday PHP
challenges. Most of the functionality is optional and tries to be as 
minimalist as possible.

Getting Started
-------------------------------------------------------------------------------
Setup will be performed automatically the first time the site is accessed. After installation is complete, it is good practice to delete the */app/setup/* folder for security reasons.

Functionality
-------------------------------------------------------------------------------

### Core Functionality

The core is the basic functions that are used to build modules. They cannot be removed.

* **Debugging** -
    Easily send debug information via the browser, email, or database.

* **Hooks** -
    Hooks allow you to run a function at certain spots in your code. We call these spots *hook points*. Multiple functions may be executed at any *hook point*.

* **Templating** -
    Basic templating functionality lets you separate business logic from the
    view. It replaces placeholders like "{{ title }}" with data.

* **Routing** -
    A very basic routing class that allows you to build database driven applications.

### The Module System

Enabling and disabling modules is handled vis the folder structure. There is a */app/module/** folder that contains 2 subdirectories called */app/module/enabled* and */app/module/disabled*. To enable a module move it to the */app/module/enabled* folder. Conversely, you can disable a module by moving it to the */app/module/disabled* folder.

### Available Modules

Modules use *hook points* to inject functionality into your application and modify data.

* **CSS Compress** -
    Compressed the output CSS only if the application is in the "LIVE" environment.

* **Navigation** -
    Creates a UL based on a JSON object that can be used for navigation.

* **URL Class** -
    Adds a class based on the current page. For example, if your application is currently on the */user/jaime/index.php* page, the class *user-jaime-index* will be added to the body. Additionally, if you are omitting *index.php* from your URLs, e.g. */user/admin*, the class would be *user-admin-index*.

* **CSV** -
    Create, Read, Update, Delete (CRUD) class for CSV files with very basic querying capabilities.

* **DB** -
    Create, Read, Update, Delete (CRUD) class using PDO and mySQL.

* **DB Grid** -
    Turns a SQL Select statement into a table. The table information can be transformed using *hook filter*, making this a powerful module for visualizing and organizing data.

* **File System Database** -
    A basic database that uses flat files and JSON documents. Provides simple functionality when a full blown database is overkill.

* **IP 2 Country** -
    Uses the end-users IP address to detect the country of origin.

* **Mailer** -
    Provides basic email functionality with RFC email validation. Combining the templating engine with the Mailer class allows for a elegant solution to HTML email templates.

* **Memcache** -
    Improves performance by implementing caching of pages (10 second cache expiration by default)

* **Mobile detection** -
    Can detect mobile and tablet devices on the server-side.

* **HTML Compress** -
    Compresses the output HTML if we are in the *live* environment.

* **Head Inserter - Joey Bomber** -
    Allows you to insert HTML at the end of the HEAD tag.

* **Robots Dev Hide - Joey Bomber** -
    Instructs search engines *NOT* to index a site while it is in the Staging environment. It does not affect live/production sites.

* **Users** -
    Basic user and roles functionality includes a sample schema, authentication, roles, and permissions.

### Sample Modules

These module have little-to-no practical use, but help demonstrate how to build
simple modules with *hook filters*.

* **Wizard Title** -
    This module prepends an ASCII wizard to the title of the page.

* **Sample Navigation** -
    Demonstrates how to use the navigation module to build dynamic menus.
    
### Constants

* **ENV**
	What is the current environment. Values: DEV, STAGE, LIVE

* **URLBASE**
	The base URL to the sleepyMUSTACHE base directory

* **DIRBASE**
	The base directory to the sleepyMUSTACHE base directory

* **DBHOST**
	The mySQL host URL

* **DBUSER**
	the mySQL username

* **DBPASS**
	the mySQL password

* **DBNAME**
	the mySQL database name

* **EMAIL_FROM**
	The email address to use for the "from" field

* **EMAIL_TO**
	The email address to use for the "to" field

* **EMAIL_CC**
	The email address to use for the "cc" field

* **EMAIL_BCC**
	The email address to use for the "bcc" field

* **GA_ACCOUNT**
	The Google Analytics GA Account ID

Sample Code
-------------------------------------------------------------------------------

### Hooks

The *Hooks* system is made up of *hook filters* and *hook actions*. *Hook actions* are points in the code where you can assign functions to run. For example, we can put a *hook action* after a record is saved to the database, then assign a function to the *hook action* that will send an email after the DB update.

	namespace Example;
	
	// Save to the database
	$db->save();
	
	// add a hook action
	\Sleepy\Hook::addAction('record_saved');
	
	// In the module file, add a function to the hook action
	function send_email() {
		// send an email saying a record was updated
	}
	
	\Sleepy\Hook::doAction(
		'record_saved',
		'\Example\send_email'
	);

*Hook filters* are similar to *hook actions* but pass data as
parameters to the functions that get assigned to the hook. After manipulating
this data you must return the edited data back to the program.

	namespace Example;
	
	// add a hook filter
	$content = \Sleepy\Hook::addFilter('update_content', $_POST['content']);
	
	// Add a function to the hook filter
	function clean_html ($html) {
		$c = htmlentities(trim($html), ENT_NOQUOTES, "UTF-8", false);
		return $c;
	}
	
	\Sleepy\Hook::applyFilter(
		'update_content',
		'\Example\clean_html'
	);

The *modules/enabled* directory provides a convenient location to put code that
utilized the hooks system. Code inside of the *modules/enabled* directory are
automatically added to the program at runtime.


### Templating

Templates reside inside the */app/templates/* folder and should end in a .tpl
extension. The templating system works by using placeholders that later get
replaced with text. The placeholders must have the following syntax:

	{{ placeholder }}

To use a template you instantiate the template class passing in the template
name. You then bind data to the placeholders and call the *Template::show()*
method.

	require_once('include/sleepy.php');
	$page = new \Sleepy\Template('default');
	$page->bind('title', 'sleepyMUSTACHE');
	$page->bind('header', 'Hello world!');
	$page->show();

Here is the sample template file (templates/default.tpl):

	<html>
		<head>
			<title>{{ title }}</title>
		</head>
		<body>
			<h1>{{ header }}</h1>
			<p>This page has been viewed {{ hits }} times.</p>
		</body>
	</html>

We added a *{{ hits }}* placeholder in the template above. For this example, we
want to replace the placeholder with the number of times this page was viewed.
We can add that functionality using *Hooks*.

	// filename: /modules/enabled/hit-counter/hits.php
	namespace Hits;
	
	/**
	 * Adds the number of hits to the page.
	 * @return string The total amount of hits
	 */
	function get() {
		$hits = new FakeClass();
		return $hits->getTotal();
	}

	// Next we attach the function to the hook point
	\Sleepy\Hook::applyFilter(
		'render_placeholder_hits',
		'\Hits\get'
	);

The first parameter of *\Sleepy\Hook::applyFilter()*, the *hook filter*, ends in 'hits' which correlates to the name of the placeholder. This *hook filter* is defined in *class.template.php*. The second parameter is the name of the function to run when we render the placeholder.

The templating engine allows you to iterate through multidimensional array data using #each placeholders.

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
	
	// in the .tpl file
	{{ #each f in fruits }}
		<p>I like {{ f.color }}, because my {{ f.name }} is {{ f.color }}.</p>
	{{ /each }}

### Databases

The database connection settings are defined in the */app/include/global.php* file. After the *LIVE_URL* is set in the file the framework will detect which DB to use based on the current URL.

To get a database instance, use:

	$db = \DB\DB::getInstance();

The DB class is static and will automatically handle suppressing multiple
database connections.

### Sending emails

The Mailer class simplifies sending emails by generating headers for you
and using an easy to use object to clearly define your email. The Mailer can
send emails using an HTML template or text.

	$m = new \Mailer\Message();
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

	$c = new \CSV\Document();
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

	$db = \DB\DB::getInstance();
	\Sleepy\Debug::out($db);

### File System Database (class.fsdb.php)

Sometimes using a database is overkill.  A simple solution is to use the *FSDB*.
It is very simple and does not allow complex queries, however it is fast, easy
to use, and requires no setup, except checking that proper permissions are set.

	$fruit = new stdClass();

	$fruit->name = "Apple";
	$fruit->color = "Red";
	$fruit->texture = "Crispy";
	$fruit->price = 0.50;

	$db = new \FSDB\Connection();

	$db->insert('fruit', $fruit);
	$data = $db->select('fruit', 'name', 'Apple');

### Country detection

*Country detection* uses the *FSDB* to do a quick lookup of the current country.

	$i = new IP2Country\Converter();
	$countryCode = $i->getCountryCode($_SERVER['REMOTE_ADDR']);
	
	if ($countryCode != false) {
		echo $countryCode;
	} else {
		echo $_SERVER['REMOTE_ADDR'] . "(" . ip2long($_SERVER['REMOTE_ADDR']) . ") Not found in " . $i->getTable($_SERVER['REMOTE_ADDR']) . ".";
	}

### Mobile detection

Mobile detection is done by comparing the UA (user-agent) to a list of currently available mobile and tablet UA. This module is deprecated and will be phased out in v2.0.

	$md = new MobiDetect\Detector();
	
	if ($md->isMobile()) {
		// Do something for mobile only
	}

### Navigation

The navigation is generated from JSON. It renders the JSON into a unordered
list with some classes added for the current active page.

	// Add a placeholder in your template
	{{ TopNav }}
	
	// Create a php file in */modules/enabled/*
	namespace Example;
	
	require_once('include/class.navigation.php');
	
	// create a function to add to the *hook filter*
	function showNav() {
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
	
		$topNav = new \Navigation\Builder($topNavData);
		$topNav->setCurrent($_SERVER['SCRIPT_NAME']);
	
		return $topNav->show();
	}
	
	\Sleepy\Hook::applyFilter(
		'render_placeholder_TopNav',
		'\Example\showNav'
	);