sleepyMUSTACHE - Basic Setup
===============================================================================

sleepyMUSTACHE is a modular PHP micro framework designed to provide solutions for everyday PHP challenges. This basic edition is aimed at providing a bare bones scaffolding for developers to learn the framework and as a starting point for projects

Getting Started
-------------------------------------------------------------------------------
sleepyMUSTACHE is ready to go out of the box. Configuration is done in the *src/settings.php* file. There you can define your dev/stage/live environments and debugging preferences.

Whats included?
-------------------------------------------------------------------------------
The basic setup includes the core and a tool for installing [modules](https://github.com/sleepymustache/modules).

It also includes some third party libraries to help get you started including, jQuery, requirejs, normalize, html4shiv, and a small JS toolset (sleepy.js).

### Core functionality

* Debugging
* Hooks
* Routing
* Templating


### Installing Modules

We have included a Python 3.4 script (sleepy.py) to assist in installing modules. It does this by creating git submodules.

You can get a list of available modules:

	sleepy.py --list

You can install modules:

	sleepy.py --add Performance

You can search for modules:

	sleepy.py --search DB

You can get more info about a module:

	sleepy.py --help Performance

Learning More
-------------------------------------------------------------------------------
Documentation about the core functionality is available in the [core repo](https://github.com/sleepymustache/core). A list of existing modules can be found in the [modules repo](https://github.com/sleepymustache/modules).