WP Themety
==========

Wordpress tools

Installation
============

Get and install the _Composer_ here: https://getcomposer.org/

Download the latest release of **Wordpress** from https://wordpress.org/download/ , unzip/install it

Create/append composer.json file:

```json
{
  "require": {
    "php": ">=5.4",
    "wp_themety/core": "dev-master"
  },
  "repositories": [
    { "type": "git", "url": "git@github.com:Sano000/wp_themety.git" }
  ],

  "autoload": {
    "psr-0": {
      "": "wp-app"
    }
  },
  "scripts": {
    "wp_themety": "Themety\\Tasks::manager"
  }
}
```

**wp-app** - configuration directory

Run:

```
composer install
```

Then install WP Themety configs to **wp-app** directory:

```
composer wp_themety install wp-app
```

And in the final add autoload file to a theme **functions.php** file


```
# If Composer installed into a theme folder:
require __DIR__ . '/vendor/autoload.php';
Themety\Themety::init(array(
    'appPath' => __DIR__ . '/wp-app/'
));
```
or

```
# if Composer installed into the webroot path
require ABSPATH . 'vendor/autoload.php';
Themety\Themety::init();
```


Getting started
===============

Check **wp-app** directory
