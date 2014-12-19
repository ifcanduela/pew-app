# Pew-Pew-Pew

 > An amateur PHP framework.

The repository only hosts a base app to use with [ifcanduela/pew](https://github.com/ifcanduela/pew).

# Quickstart

You need PHP 5.4 and [Composer](http://getcomposer.org/).

1. Clone this repository.
2. Modify the `composer.json` file as you need.
3. Run `composer install`.
4. Start creating folders and files in `app/views`. 
5. Run `php -S 127.0.0.1:8000` in the `www` folder.

A file called `about.php` in `app/views/site` will be rendered when you request `http://localhost/site/about`.

More complex stuff is achieved by configuring a database, adding routes and creating controllers and models.
