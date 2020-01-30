sleepyMUSTACHE - Basic Setup
===============================================================================

sleepyMUSTACHE is a modular PHP micro framework designed to provide solutions for everyday PHP
challenges. There are two editions: basic and routed.This basic edition is aimed at providing a bare
bones scaffolding for developers to learn the framework and as a starting point for projects

Getting Started
-------------------------------------------------------------------------------
sleepyMUSTACHE is simple to configure. Configuration is done in both *src/settings.php* and *gulpfile.js*.

You define your dev/stage/live environments, debugging preferences, and set any globals you need for your application in *settings.php*. Build settings are managed in *gulpfile.js*

1. Initialize git and node modules:

    git submodule init
    git submodule update
    npm install

2. In *gulpfile.js*, modify the configuration setting as necessary:

    const devUrl = 'http://basic.local.com';
    const enableTests = false;

3. Start the develop process by running:

    npm develop

4. Configure the web server so that *devUrl* points to the *dist* folder that *npm develop* created


Gulp Modules
-------------------------------------------------------------------------------
sleepyMUSTACHE Basic included the following gulp modules in the build system:

* gulp-eslint - Processes files with ESLint
* gulp-imagemin - Minifies images
* gulp-notify - Adds system notifications for error handling
* gulp-sass - Compiles and minifies SASS
* gulp-sourcemaps - Adds sourcemaps for SASS
* gulp-webpack - Minify and transpiles JavaScript

Whats included?
-------------------------------------------------------------------------------
The basic setup includes the core and a tool for installing
[modules](https://github.com/sleepymustache/modules).

It also includes some third party libraries to help get you started including, SimpleTest and a
small JS toolset (sleepy.class.js).

### Core functionality

* [Debugging](http://sleepymustache.com/documentation/class-Sleepy.Debug.html)
* [Hooks](http://www.sleepymustache.com/documentation/class-Sleepy.Hook.html)
* [Routing](http://sleepymustache.com/documentation/class-Sleepy.Router.html)
* [Templating](http://www.sleepymustache.com/documentation/class-Sleepy.Template.html)

### Installing Modules

There is a helper script (sleepy.js) to assist in finding and installing modules.

You can get a list of available modules:

	node sleepy --list

You can install modules:

	node sleepy --add Performance

You can search for modules:

	node sleepy --search DB

You can get more info about a module:

	node sleepy --help Performance

Learning More
-------------------------------------------------------------------------------
Documentation about the core functionality is available in the
[core repo](https://github.com/sleepymustache/core). A list of existing modules can be found in the
[modules repo](https://github.com/sleepymustache/modules).
