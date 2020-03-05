/* globals process */

'use strict';

const https = require('https');
const fs = require('fs');
const subprocess = require('child_process');

/**
 * Displays a list of commands
 * @return {void}
 */
function helpMe() {
  let helpData = 'sleepy.js helps manage sleepyMUSTACHE modules in a git repository.\n\n';

  helpData += 'Usage:\n';
  helpData += '\tsleepy.js [--list <all|installed>]\n';
  helpData += '\tsleepy.js [--search <module>]\n';
  helpData += '\tsleepy.js [--add <module>]\n';
  helpData += '\tsleepy.js [--remove <module>]\n';
  helpData += '\tsleepy.js [--help [<module>]]\n';
  helpData += '\tsleepy.js [--clean]';

  console.log(helpData);
}

/**
 * Represents a sleepy module
 * @return {object} returns a module object
 */
class Module {
  constructor(name, obj) {
    this.obj = obj;
    this.name = name;
    this.readme = '';
    this.installed = false;
  }

  /**
   * Gets the readme data and displays it
   * @return {void}
   */
  getInfo() {
    const request = https.get(this.obj.readme, (response) => {
      let body = '';

      response.on('data', chunk => {
        body += chunk;
      });

      response.on('end', () => {
        this.readme = body;
        console.log(this.readme);
      });
    });

    request.on('error', (e) => {
      console.error(e);
    });
  }

  /**
   * Adds a sleepy module from git
   * @return {void}
   */
  add() {
    // ignore if dependencies already was installed
    if (this.installed) {
      return;
    }

    subprocess.spawnSync('git', [
      'submodule',
      'add',
      this.obj.url,
      'src/app/module/' + this.name.replace(' ', '-').toLowerCase()
    ], {
      shell: true
    });

    this.installed = true;
  }

  /**
   * Removes a sleepy module from your project
   * @return {void}
   */
  remove() {
    console.log('Removing a module must be done manually');
  }
}

/**
 * Initializes a Sleepy instance, invokes immediately
 * @return {void}
 */
class Sleepy {
  constructor(cb) {
    this.modules = {};
    this.data;

    let request = https.get({
      hostname: 'raw.githubusercontent.com',
      path: '/sleepymustache/modules/master/modules.json'
    }, response => {
      let body = '';

      response.on('data', chunk => {
        body += chunk;
      });

      response.on('end', () => {
        this.data = JSON.parse(body);

        for (let key in this.data) {
          if (this.data.hasOwnProperty(key)) {
            this.modules[key.toLowerCase()] = new Module(key, this.data[key]);
          }
        }

        cb();
      });
    });

    request.on('error', e => {
      console.error(e);
    });
  }

  /**
   * Display a list of all modules
   * @return {void}
   */
  showAll() {
    for (let key in this.data) {
      if (this.data.hasOwnProperty(key)) {
        console.log(key);
      }
    }
  }

  /**
   * Displays a list of the modules currently installed in the project
   * @return {void}
   */
  showInstalled() {
    console.log('Installed Modules:');

    fs.readdir('src/app/modules', (e, dirs) => {
      for (let directory in dirs) {
        if (dirs.hasOwnProperty(directory)) {
          for (let m in this.modules) {
            if (this.modules.hasOwnProperty(m)) {
              if (dirs[directory].replace('-', ' ') === m) {
                console.log(' ', m);
              }
            }
          }
        }
      }
    });
  }

  /**
   * Checks command input and runs getInfo method of Module
   * @param  {string} modName The module name
   * @return {void}
   */
  help(modName) {
    if (!modName) {
      helpMe();
      return;
    }

    if (this.modules.hasOwnProperty(modName.toLowerCase())) {
      this.modules[modName.toLowerCase()].getInfo();
    } else {
      console.log('Could not find module');
    }
  }

  /**
   * Checks command input and runs Module add method recursively through dependencies
   * @param  {string} modName The module name
   * @return {void}
   */
  add(modName) {
    if (!modName) {
      console.log('Missing module name');
      return;
    }

    if (this.modules.hasOwnProperty(modName.toLowerCase())) {
      let dependencies = this.modules[modName.toLowerCase()].obj.dependencies;

      // recursively add dependencies
      for (var i = 0; i < dependencies.length; i++) {
        this.add(dependencies[i]);
      }

      this.modules[modName.toLowerCase()].add();
    } else {
      console.log('Could not find module');
    }
  }

  /**
   * Checks command input and runs Module remove method
   * @param  {string} modName The module name
   * @return {void}
   */
  remove(modName) {
    if (!modName) {
      console.log('Missing module name');
      return;
    }

    if (this.modules.hasOwnProperty(modName.toLowerCase())) {
      this.modules[modName.toLowerCase()].remove();
    } else {
      console.log('Could not find installed module');
    }
  }

  /**
   * Searches for modules
   * @param  {string} searchString A string to search
   * @return {void}
   */
  search(searchString) {
    if (!searchString) {
      console.log('Missing a search string');
      return;
    }

    console.log('Search Results:');

    for (let m in this.modules) {
      if (m.toLowerCase().includes(searchString.toLowerCase())) {
        console.log(' ', m);
      }
    }
  }

  /**
   * Removes files for production deployment
   * @return {void}
   */
  clean() {
    const build = 'src/build';
    const errors = [];

    /**
     * Checks if the directory exists
     * @param  {string} path The path to the folder
     * @param  {function} cb A callback function
     * @return {void}
     */
    function checkDir(path, cb) {
      try {
        let dir = fs.lstatSync(path);

        if (dir.isDirectory()) {
          cb(path);
        }
      } catch (e) {
        errors.push(e);
      }
    }

    checkDir(build, () => {
      const scss = 'src/scss';
      const tests = 'src/app/tests';
      const app = 'src/app';
      const js = 'src/js';

      /**
       * Removes directory recursively
       * @param  {string} path The path of the directory
       * @return {void}
       */
      function removeDir(path){
        if (fs.lstatSync(path).isDirectory()) {
          let dir = fs.readdirSync(path);

          for (let item in dir) {
            if (dir.hasOwnProperty(item)) {
              let currentPath = path + '/' + dir[item];

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
          console.log(path, ' must be a folder');
        }
      }

      /**
       * Removes files that end with "_test.php" from the given folder and subfolders
       * @param  {string} folderPath The path of the folder
       * @return {void}
       */
      function removeTestFiles(folderPath) {
        if (fs.lstatSync(folderPath).isDirectory()) {
          let dir = fs.readdirSync(folderPath);

          for (let item in dir) {
            if (dir.hasOwnProperty(item)) {
              let currentPath = folderPath + '/' + dir[item];

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

      console.log('Files removed:');
      checkDir(scss, removeDir);
      checkDir(tests, removeDir);
      checkDir(js, removeDir);
      checkDir(app, removeTestFiles);
    });

    // errors
    if (errors.length) {
      for (let i = 0; i < errors.length; i++) {
        console.log(errors[i]);
      }
    }
  }
}

function main(sleepy) {
  const args = process.argv;
  let command = '';

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

const sleepy = new Sleepy(() => {
  main(sleepy);
});
