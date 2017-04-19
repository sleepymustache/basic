sleepyMUSTACHE - Basic Setup
===============================================================================

sleepyMUSTACHE is a modular PHP micro framework designed to provide solutions
for everyday PHP challenges. This basic edition is aimed at providing a bare
bones scaffolding for developers to learn the framework and as a starting point
for projects

Getting Started
-------------------------------------------------------------------------------
sleepyMUSTACHE is ready to go out of the box. Configuration is done in the
*src/settings.php* file. There you can define your dev/stage/live environments
and debugging preferences.

To start setup:

    npm install

After NPM has installed required modules, the build process can be started by
running:

    npm start

Gulp Modules
-------------------------------------------------------------------------------
sleepyMUSTACHE Basic included the following gulp modules in the build system:

* gulp-eslint - Processes files with ESLint
* gulp-imagemin - Minifies images
* gulp-notify - Adds notifications on errors
* gulp-sass - Compiles and minifies SASS
* gulp-sourcemaps - Adds sourcemaps for SASS
* gulp-webpack - Minify and use ES2015

Whats included?
-------------------------------------------------------------------------------
The basic setup includes the core and a tool for installing
[modules](https://github.com/sleepymustache/modules).

It also includes some third party libraries to help get you started including,
SimpleTest and a small JS toolset (sleepy.class.js).

### Core functionality

* Debugging
* Hooks
* Routing
* Templating


### Installing Modules

We have included a helper script (sleepy.js) to assist in installing modules.
It does this by creating git submodules.

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
[core repo](https://github.com/sleepymustache/core). A list of existing modules
can be found in the [modules repo](https://github.com/sleepymustache/modules).