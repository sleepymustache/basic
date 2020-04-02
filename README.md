sleepyMUSTACHE - Basic Setup
===============================================================================

sleepyMUSTACHE is a modular PHP micro-framework designed to provide solutions for
everyday PHP challenges. There are two editions: basic and routed. The basic edition
is aimed at providing a bare bones scaffolding for simple websites. While the routed
version is focused on web application development and includes conrollers, modules,
and views.

Getting Started
-------------------------------------------------------------------------------

1. sleepyMUSTACHE settings

Configuration is done in *src/settings.php*. You define your dev/stage/live
environments, debugging preferences, and set any globals you need for your
application in *settings.php*.

2. Development and tooling

NPM (node v.11.15.0), Gulp 4, and Docker are required and used to setup the
environment and automate the build process. The initial compile takes a few mins as
docker will download, compile, and setup the development environment.

``` bash
    npm run develop
```

The compiled code will live in the *dist* folder. Development is done in the *src*
folder and compiled by gulp to the *dist* folder. The dist folder is automatically
synced with the docker instance.

Gulp Modules
-------------------------------------------------------------------------------
sleepyMUSTACHE Basic includes the following gulp modules in the build system:

* gulp-eslint     - Processes files with ESLint
* gulp-imagemin   - Minifies images
* gulp-notify     - Adds system notifications for error handling
* gulp-sass       - Compiles and minifies SASS
* gulp-sourcemaps - Adds sourcemaps for SASS
* gulp-webpack    - Minify and transpiles JavaScript

Whats included?
-------------------------------------------------------------------------------
The basic framework includes the core, phpunit, a tool for installing
[modules](https://github.com/sleepymustache/modules), and the build scripts.

### Core functionality

The core consistes of 4 modules. See below for documentation:

* [Debugging](http://sleepymustache.com/documentation/class-Sleepy.Debug.html)
* [Hooks](http://www.sleepymustache.com/documentation/class-Sleepy.Hook.html)
* [Routing](http://sleepymustache.com/documentation/class-Sleepy.Router.html)
* [Templating](http://www.sleepymustache.com/documentation/class-Sleepy.Template.html)

### Installing Modules

"sleepy.js" assists in finding and installing modules.

You can get a list of available modules:

``` bash
    node sleepy --list
```

You can install modules:

``` bash
    node sleepy --add Performance
```

You can search for modules:

``` bash
    node sleepy --search DB
```

You can get more info about a module:

``` bash
    node sleepy --help Performance
```

Learning More
-------------------------------------------------------------------------------
*  Documentation about the core is available at [core
   repo](https://github.com/sleepymustache/core).
*  A list of existing modules can be found in the [modules
   repo](https://github.com/sleepymustache/modules).