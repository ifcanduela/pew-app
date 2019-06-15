# Pew-Pew-Pew

> A PHP 7 framework for simple websites.

The repository hosts the base app to start a Pew project. The core framework can 
be found at [ifcanduela/pew](https://github.com/ifcanduela/pew).

## Kickstart

You need PHP 7.0 with the `mbstring` extension enabled and [Composer](http://getcomposer.org/).

```bash
$ composer create-project ifcanduela/pew-app my-project-name
$ cd my-project-name
$ php serve
```

## Documentation

Check the [online documentation](https://pew.ifcanduela.com/ "Pew docs") for more.

## Migrations

Install [Phinx](https://phinx.org/) using `composer require robmorgan/phinx`. A default setup and a
migration to create an SQLite database with a `users` table are included.

## Assets

To compile assets, install the NPM dependencies with `npm install` and run one of the npm scripts:

1. `npm run watch` to compile as you go.
2. `npm run dev` to compile a development version of the assets, including source maps.
3. `npm run prod` to get a production, minified version of the assets.

The default setup uses Webpack to compile LessCSS and Javascript (with support for VueJS single-file components).

## License

[MIT](LICENSE).
