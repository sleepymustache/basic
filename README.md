# sleepyMUSTACHE 2.0 - Basic Setup

sleepyMUSTACHE is a PHP micro-framework designed to provide solutions for rapidly building websites which are fast and
secure. Our main goal is to create a framework that is enjoyable for developers to use, while giving them fine grained
control.

There are two editions: basic and routed.

The basic edition is aimed at providing a bare bones scaffolding for simple websites. While the routed version is
focused on web application and services development and includes controllers, modules, and views. Both versions consists
of the core and various modules which are easy to understand and create.

## Getting Started

1. sleepyMUSTACHE settings

Configuration is done in *src/settings.php*. You define your dev/stage/live environments, debugging preferences, and set
any globals you need for your application in *settings.php*.

2. Development and tooling

NPM (node v.14), Gulp 4, and Docker are required and used to setup the environment and automate the build process.
The initial compile takes a few mins as Docker will download, compile, and setup the development environment.

``` bash
    npm run develop
```

Development is done in the *src* folder and compiled by gulp to the *dist* folder. The dist folder is automatically
synced with the docker instance. Visual Studio Code will be preconfigured to hide the dist folder to prevent confusion.

## Gulp Modules

sleepyMUSTACHE Basic includes the following gulp modules in the build system:

* gulp-eslint     - Processes files with ESLint
* gulp-imagemin   - Minifies images
* gulp-notify     - Adds system notifications for error handling
* gulp-sass       - Compiles and minifies SASS
* gulp-sourcemaps - Adds sourcemaps for SASS
* gulp-webpack    - Minify and transpiles JavaScript

## Whats included?

The basic framework includes the core, phpunit, a tool for installing
[modules](https://github.com/sleepymustache/modules) (sleepy.js), and the build scripts.

### Core functionality

The core consists of a few classes. See below for documentation:

* [Debugging](http://sleepymustache.com/documentation/class-Sleepy.Debug.html)
* [Hooks](http://www.sleepymustache.com/documentation/class-Sleepy.Hook.html)
* AutoLoader
* Modules
* [Templating](http://www.sleepymustache.com/documentation/class-Sleepy.Template.html)

### Installing Modules

"sleepy.js" assists in finding and installing modules. The list of Modules is located on Github. We encourage you to
submit your modules as pull requests to have them added to the [repository](https://github.com/sleepymustache/modules).

Using the sleepy.js script, you can get a list of available modules:

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