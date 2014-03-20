Update Checker
==============

The Update Checker is a command line tool that checks if your application dependencies
for updates. It uses [packagist][1] behind the scenes:

    $ php update-checker check /path/to/composer.lock

You can also integrate the checker in your own application/project

 * by using the `UpdateCheckerCommand` class into your Symfony Console
   application.

 * by using the `UpdateChecker` class directly into your own code:

        use Temp\Update\UpdateChecker;

        $checker = new UpdateChecker();
        $result = $checker->check('/path/to/composer.lock');

Create Phar File
---------------------

To create a phar file you can use [Box][2]

    $ php box.phar build

[1]: https://packagist.org/
[2]: http://box-project.org/
