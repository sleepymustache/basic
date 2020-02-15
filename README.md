sleepyMUSTACHE - Basic Setup
===============================================================================

sleepyMUSTACHE is a modular PHP micro-framework designed to provide solutions for everyday PHP
challenges. There are two editions: basic and routed. The basic edition is aimed at providing a
bare bones scaffolding for simple websites. While the routed version is focused on web application
development by including conrollers, modules, and views.

Getting Started
-------------------------------------------------------------------------------
Configuration is done in *src/settings.php* and *gulpfile.js*. You define your dev/stage/live
environments, debugging preferences, and set any globals you need for your application in
*settings.php*. Build settings are managed in *gulpfile.js*

1. Initialize git and node modules:

``` bash
    git submodule init
    git submodule update
    npm install
```

2. In *gulpfile.js*, modify the configuration setting as necessary:

``` javascript
    const devUrl = 'http://basic.local.com';  // This should match the web server hostname
    const enableTests = false;                // Set to true to enable simpletest
```

3. Compile the code and start the develop/watch process by running:

``` bash
    npm develop
```

The compiled code will live in the *dist* folder. Development is done in the *src* folder and
compiled by gulp to the *dist* folder.

4. Configure the web server so that:

  * The web server root should point to the *dist* folder inside the repo
  * The web server hostname matches the *devUrl* in *gulpfile.js*


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
The basic framework includes the core, SimpleTest, a tool for installing
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
*  Documentation about the core is available at [core repo](https://github.com/sleepymustache/core).
*  A list of existing modules can be found in the [modules
   repo](https://github.com/sleepymustache/modules).