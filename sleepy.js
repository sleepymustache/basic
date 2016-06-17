
var https = require('https');
var fs = require('fs');
var subprocess = require('child_process');

var sleepy,
	module;

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
		if (command === '--search') {
			sleepy.search(args[3]);
			return;
		}
		if (command === '--add') {
			sleepy.add(args[3]);
			return;
		}
		if (command === '--remove') {
			sleepy.remove(args[3]);
			return;
		}
		if (command === '--help') {
			sleepy.help(args[3]);
			return;
		}
		if (command === '--clean') {
			sleepy.clean();
			return;
		}
		if (command === '--list') {
			if (args.length > 3 && args[3].toLowerCase() === 'installed') {
				sleepy.showInstalled();
			} else {
				sleepy.showAll();
			}
			return;
		}
	}
	helpMe()
}
module = function () {
	'use strict';

	var obj = {},
		name = '',
		readme = '';

	return {
		'init': function (modName, modObj) {
			name = modName;
			obj = modObj;
		},
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
		'remove': function () {
			console.log('Removing a module must be done manually');
		}
	};
};
sleepy = (function () {
	'use strict';

	var modules = {},
		data = {},
		request;

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
		'showAll': function () {
			var key;

			for (key in data) {
				if (data.hasOwnProperty(key)) {
					console.log(key);
				}
			}
		},
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
		'clean': function () {
			var scss = 'src/scss',
				tests = 'src/app/tests',
				app = 'src/app',
				js = 'src/js',
				build = 'src/build',
				errors = [],
				i;

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
			// log errors
			if (errors.length) {
				for (i = 0; i < errors.length; i++) {
					console.log(errors[i]);
				}
			}
		}
	};
}());

