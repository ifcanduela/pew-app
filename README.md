# Pew-Pew-Pew

 > An amateur PHP framework.

Pew-Pew-Pew is a PHP 5.3 framework with a focus on the automation of simple static-page sites or Ajax-centered web apps. It's similar to other MVC frameworks written in PHP, like CakePHP or CodeIgniter, but a little bit simpler to use (at the cost of many features, of course).

I've used this for all my latest personal projects and it's usable -- although a little bit unstable, so I'm building a nice [PHPUnit](https://github.com/sebastianbergmann/phpunit/) test suite.

The repo contains a sample application called **docs** that represents an attempt at documenting the framework. The code is commented too, so knowing what is inside is as easy as reading.

If you're asking yourself, *"Should I use this?"*, I have a simple answer for you: *no*.

### Static pages

After dropping the `app`, `sys` and `www` folders and the `index.php` and `.htaccess` files somewhere inside your webroot folder, you can start adding static pages to the `app/views/pages` directory, in the shape of `.php` files. Those will be available through `http://host/pages/pagename`.

### Controllers

Controllers go in the `app/controllers` directory, and need a `.class.php` extension (but it's customizable). A basic controller looks like this:

    <?php

    class Pets extends Controller
    {
        function index()
        {
            $this->data['pets'] = $this->model->find_all();
        }

        function cats()
        {
            $this->data['pets'] = $this->model->find_all_by_species('cat');
            $this->view = 'pets';
        }
    }

### Look and feel

Edit the default layout (`app/views/default.layout.php`) to make it as you like. I usually put CSS and JS files in `www`, but they can be wherever you want. Include them using the handy functions `url()` or `www()`:

    # the url() function prints the base/canonical URL of the app
    <link rel="stylesheet" href="<?php url('www/css/styles.css'); ?>">

    # the www() function prints the URL of the www folder (which can be changed)
    <script src="<?php www('js/jquery.js'); ?>"></script>

***

*In progress*