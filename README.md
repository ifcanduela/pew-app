# Pew-Pew-Pew

 > An amateur PHP framework.

Pew-Pew-Pew is a PHP 5.3 framework with a focus on the automation of simple static-page sites. The idea is similar to other MVC frameworks written in PHP, like CakePHP or CodeIgniter, but much, much simpler to use (at the cost of many features, of course).

I've used this for all my latest personal projects and it's usable -- although a little bit unstable, so I'm slowly building a nice [PHPUnit](https://github.com/sebastianbergmann/phpunit/) test suite.

# Quickstart

Drop the files into your server's document folder or any sub-folder. Open `app/config/config.php` and change some settings. The open the `app/views` folder and start creating folders and files. A file called `about.php` in `app/views/site` will be rendered when you request `http://localhost/site/about`.

More complex stuff is achieved by configuring a database, creating controllers and using models.