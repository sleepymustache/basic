
var https = require('https');
var fs = require('fs');
var subprocess = require('child_process');

var sleepy,
	module;
/**
 * Displays a list of commands
 * @return void [description]
 */
function helpMe() {
	'use strict';

	var helpData = 'sleepy.js helps manage sleepyMUSTACHE modules in a git repository.\n\n';

	helpData += 'Usage:\n';
	helpData += '\tsleepy.js [--list <all|installed>]\n';
	helpData += '\tsleepy.js [--search <module>]\n';
	helpData += '\tsleepy.js [--add <module>]\n';
	helpData += '\tsleepy.js [--remove <module>]\n';
	helpData += '\tsleepy.js [--help [<module>]]\n';
	helpData += '\tsleepy.js [--clean]';
	console.log(helpData);
}
function main() {
	'use strict';

	var args = process.argv,
		command = '';

	if (args.length === 2) {
		helpMe();
		return;
	}

	command = args[2].toLowerCase();

	if (args.length > 2) {
		switch (command) {
			case '--search':
				sleepy.search(args[3]);
				break;
			case '--add':
				sleepy.add(args[3]);
				break;
			case '--remove':
				sleepy.remove(args[3]);
				break;
			case '--help':
				sleepy.help(args[3]);
				break;
			case '--clean':
				sleepy.clean();
				break;
			case '--list':
				if (args.length > 3 && args[3].toLowerCase() === 'installed') {
					sleepy.showInstalled();
				} else {
					sleepy.showAll();
				}
				break;
			default:
				helpMe();
		}
	}
}
/**
 * Represents a sleepy module
 * @return object returns a module object
 */
module = function () {
	'use strict';

	var obj = {},
		name = '',
		readme = '';

	return {
		/**
		 * Sets the name and data of the module
		 * @param  string modName A string of the module name
		 * @param  object modObj  The module data
		 * @return void [description]
		 */
		'init': function (modName, modObj) {
			name = modName;
			obj = modObj;
		},
		/**
		 * Gets the readme data and displays it
		 * @return void [description]
		 */
		'getInfo': function () {
			var request = https.get(obj.readme, function (response) {
				var body = '';

				response.on('data', function (chunk) {
					body += chunk;
				});
				response.on('end', function () {
					readme = body;
					console.log(readme);
				});
			});

			request.on('error', function (e) {
				console.error(e);
			});
		},
		/**
		 * Adds a sleepy module from git
		 * @return void [description]
		 */
		'add': function () {
			subprocess.spawn('git',
				[
					'submodule',
					'add',
					obj.url,
					'src/app/modules/' + name.replace(' ', '-').toLowerCase()
				],
				{shell: true}
			);
		},
		/**
		 * Removes a sleepy module from your project
		 * @return void [description]
		 */
		'remove': function () {
			console.log('Removing a module must be done manually');
		}
	};
};
/**
 * Initializes a Sleepy instance, invokes immediately
 * @return void [description]
 */
sleepy = (function () {
	'use strict';

	var modules = {},
		data = {},
		request;
	/**
	 * Removes directory recursively
	 * @param  string path The path of the directory
	 * @return void [description]
	 */
	function removeDir(path) {
		var dir,
			item,
			currentPath;

		if (fs.lstatSync(path).isDirectory()) {
			dir = fs.readdirSync(path);

			for (item in dir) {
				if (dir.hasOwnProperty(item)) {
					currentPath = path + '/' + dir[item];

					if (fs.lstatSync(currentPath).isDirectory()) {
						// recurse when sub directory is found
						removeDir(currentPath);
					} else {
						// delete file
						fs.unlinkSync(currentPath);
						console.log(' ', currentPath);
					}
				}
			}
			// delete sub directory when it is empty
			fs.rmdirSync(path);
			console.log(' ', path);
		} else {
			// not a folder
			console.log(path, 'must be a folder');
		}
	}
	/**
	 * Removes files that end with "_test.php" from the given folder and subfolders
	 * @param  string folderPath The path of the folder
	 * @return void [description]
	 */
	function removeTestFiles(folderPath) {
		var item,
			currentPath,
			dir;

		if (fs.lstatSync(folderPath).isDirectory()) {
			dir = fs.readdirSync(folderPath);

			for (item in dir) {
				if (dir.hasOwnProperty(item)) {
					currentPath = folderPath + '/' + dir[item];

					if (fs.lstatSync(currentPath).isDirectory()) {
						removeTestFiles(currentPath);
						continue;
					}
					if (currentPath.toLowerCase().includes('_test.php')) {
						// delete files
						console.log(' ', currentPath);
						fs.unlinkSync(currentPath);
					}
				}
			}
		} else {
			console.log(folderPath, 'must be a directory');
		}
	}
	request = https.get({
		hostname: 'raw.githubusercontent.com',
		path: '/sleepymustache/modules/master/modules.json'

	}, function (response) {
		var body = '',
			key;

		response.on('data', function (chunk) {
			body += chunk;
		});
		response.on('end', function () {
			data = JSON.parse(body);

			for (key in data) {
				if (data.hasOwnProperty(key)) {
					modules[key.toLowerCase()] = module();
					modules[key.toLowerCase()].init(key, data[key]);
				}
			}
			main();
		});
	});

	request.on('error', function (e) {
		console.error(e);
	});

	return {
		/**
		 * Display a list of all modules
		 * @return void [description]
		 */
		'showAll': function () {
			var key;

			for (key in data) {
				if (data.hasOwnProperty(key)) {
					console.log(key);
				}
			}
		},
		/**
		 * Displays a list of the modules currently installed in the project
		 * @return void [description]
		 */
		'showInstalled': function () {
			var directory,
				m;

			console.log('Installed Modules:')
			fs.readdir('src/app/modules', function (e, dirs) {
				for (directory in dirs) {
					if (dirs.hasOwnProperty(directory)) {
						for (m in modules) {
							if (dirs[directory].replace('-', ' ') === m) {
								console.log(' ', m);
							}
						}
					}

				}
			});
		},
		/**
		 * Checks command input and runs getInfo method of Module
		 * @param  string modName The module name
		 * @return void [description]
		 */
		'help': function (modName) {
			if (!modName) {
				helpMe();
				return;
			}
			if (modules.hasOwnProperty(modName.toLowerCase())) {
				modules[modName.toLowerCase()].getInfo();
			} else {
				console.log('Could not find module');
			}
		},
		/**
		 * Checks command input and runs Module add method
		 * @param  string modName The module name
		 * @return void [description]
		 */
		'add': function (modName) {
			if (!modName) {
				console.log('Missing module name');
				return;
			}
			if (modules.hasOwnProperty(modName.toLowerCase())) {
				modules[modName.toLowerCase()].add();
			} else {
				console.log('Could not find module');
			}
		},
		/**
		 * Checks command input and runs Module remove method
		 * @param  string modName The module name
		 * @return void [description]
		 */
		'remove': function (modName) {
			if (!modName) {
				console.log('Missing module name');
				return;
			}
			if (modules.hasOwnProperty(modName.toLowerCase())) {
				modules[modName.toLowerCase()].remove();
			} else {
				console.log('Could not find installed module');
			}
		},
		/**
		 * Searches for modules
		 * @param  string searchString A string to search
		 * @return void [description]
		 */
		'search': function (searchString) {
			var m;

			if (!searchString) {
				console.log('Missing a search string');
				return;
			}
			console.log('Search Results:')
			for (m in modules) {
				if (m.toLowerCase().includes(searchString.toLowerCase())) {
					console.log(' ', m);
				}
			}
		},
		/**
		 * Removes files for production deployment
		 * @return void [description]
		 */
		'clean': function () {
			var scss = 'src/scss',
				tests = 'src/app/tests',
				app = 'src/app',
				js = 'src/js',
				build = 'src/build',
				errors = [],
				i;
			/**
			 * Checks if the directory exists
			 * @param  string path The path to the folder
			 * @param  function cb A callback function
			 * @return void [description]
			 */
			function checkDir(path, cb) {
				var dir;

				try {
					dir = fs.lstatSync(path);

					if (dir.isDirectory()) {
						cb(path);
					}
				} catch (e) {
					errors.push(e);
				}
			}

			checkDir(build, function () {
				console.log('Files removed:');
				checkDir(scss, removeDir);
				checkDir(tests, removeDir);
				checkDir(js, removeDir);
				checkDir(app, removeTestFiles);
			});
			// errors
			if (errors.length) {
				for (i = 0; i < errors.length; i++) {
					console.log(errors[i]);
				}
			}
		}
	};
}());

