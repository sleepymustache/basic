'''
Create a python module to add submodules to sleepyMUSTACHE

Usage:

    sleepy [--list <all|installed>]
    sleepy [--search <module>]
    sleepy [--add <module>]
    sleepy [--remove <module>]
    sleepy [--help [<module>]}
'''

import json
import urllib.request
import subprocess

class Sleepy(object):
    """
    Represents a sleepy install
    """

    def __init__(self):
        super(Sleepy, self).__init__()
        self.modules = dict()
        self.data = json.loads(urllib.request.urlopen(
            "https://raw.githubusercontent.com/sleepymustache/modules/master/modules.json"
        ).read().decode('utf-8'))

        for key, value in self.data.items():
            self.modules[key.lower()] = Module(key, value)

    def show_all(self):
        """
        Show all the available modules
        """
        for key in self.data.items():
            print(key[0])

    def show_installed(self):
        """
        Show currently installed modules
        """
        import os

        print("Installed Modules:")
        dirs = os.listdir('app/modules')
        for directory in dirs:
            for key in self.modules.items():
                if key[0].replace(' ', '-').lower() == directory:
                    print('  ' + key[0])

    def search(self, string):
        """
        Search modules
        """
        print("Search Results: ")

        for key in self.modules:
            if string.lower() in key.lower():
                print("  " + key)

    def help(self, topic):
        """
        Show README.md for a module
        """
        self.modules[topic.lower()].get_info()

    def add(self, module):
        """
        Add a module to git
        """
        self.modules[module.lower()].add()

    def remove(self, module):
        """
        Remove a module from git
        """
        self.modules[module.lower()].remove()

class Module(object):
    """
    Represents a module
    """

    obj = dict()
    name = ""
    readme = ""

    def __init__(self, name, obj):
        super(Module, self).__init__()
        self.name = name
        self.obj = obj

    def get_info(self):
        """
        Display the README.md file
        """
        if self.readme == "":
            self.readme = urllib.request.urlopen(self.obj['readme'])
            self.readme = self.readme.read().decode('utf-8')

        print(self.readme)

    def add(self):
        """
        Add a module to git
        """
        subprocess.call(
            [
                'git',
                'submodule',
                'add',
                self.obj['url'],
                'app/modules/' + self.name.replace(" ", "-").lower()
            ],
            shell=True
        )

    @classmethod
    def remove(cls):
        """
        Remove a module from git
        """
        print("Removing module must be done manually")
        #lines = [
        #    '[submodule "app/modules/performance"]',
        #    "\tpath = app/modules/performance",
        #    'url = ' + self.obj['url']
        #]



def main():
    """
    Entry point into program
    """
    import sys

    args = sys.argv
    slpy = Sleepy()

    if len(args) == 1:
        help_me()
        sys.exit(1)

    command = args[1].lower()

    if len(args) > 2:
        if command == "--search":
            slpy.search(args[2])
            sys.exit()
        if command == "--add":
            slpy.add(args[2])
            sys.exit()
        if command == "--remove":
            slpy.remove(args[2])
            sys.exit()
        if command == "--help":
            slpy.help(args[2])
            sys.exit()

    if command == "--list":
        if len(args) > 2 and args[2].lower() == "installed":
            slpy.show_installed()
            sys.exit()
        else:
            slpy.show_all()
            sys.exit()

    help_me()

def help_me():
    """
    Shows the help text
    """
    help_data = """
    sleepy.py helps manage sleepyMUSTACHE modules in a git repository.

    Usage:
        sleepy [--list <all|installed>]
        sleepy [--search <module>]
        sleepy [--add <module>]
        sleepy [--remove <module>]
        sleepy [--help [<module>]}
    """

    print(help_data)

main()
