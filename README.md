# StatelessCMS
Object-oriented PHP building-blocks for websites and webapps.

## Introduction
StatelessCMS is a headless PHP framework designed for creating database-driven PHP applications, "headless" meaning there is no existing front-end.  StatelessCMS isn't an app or a CMS itself - it just gives you the building blocks to create one yourself.

## Installation
### Minimum Requirements
Get a server running PHP 7.0 and a SQL database.
*Note that apache is recommended, as Request::getHeaders() and Request::getToken() rely on apache_get_headers() as of v0.0.3*

### Composer Install
The easiest way to install StatelessCMS is to install with Composer:
```
composer require stateless/cms
```

### Web Download
Download the latest release from [https://github.com/StatelessSoftware/StatelessCMS/releases](https://github.com/StatelessSoftware/StatelessCMS/releases).

### Development
If you would like the live development branch instead, download or clone from github.  [https://github.com/StatelessSoftware/StatelessCMS.git](https://github.com/StatelessSoftware/StatelessCMS.git)

## Setting up your first project
Although you are free to use any directory structure you choose, the common basic structure for StatelessCMS is as follows.  Don't worry too much about the specifics - we'll create each part step by step.

```
|- app
    |- Controller
    |- Form
    |- FormInput
    |- Layout
    |- Menu
    |- Model
    |- View
    |- app.php
    |- functions.php
|- conf
    |- app.conf.php
|- public
    |- css
    |- js
    |- .htaccess
    |- index.php
|- vendor
    |- stateless

```
If you didn't already, install StatelessCMS to vendor/stateless (follow installation instructions above)

## Getting started
Open public/index.php.  Insert the following code:

```php
<?php
namespace Stateless;

require_once("../vendor/autoload.php");

echo "Home";
```

*If you downloaded and installed StatelessCMS without using Composer, change ../vendor/autoload.php to your StatelessCMS.php file*

### About public/index.php
This file is the entry point, meaning it is the start of the program.  For now, we will use it to show our examples.  Later, we can use it to startup our application.

## Test
Open a browser and navigate to localhost/public/
You should see Home.

## Configuring your server
Ensure your httpd document root is set to public/
Now navigate to localhost/ and you should see Home

## Enable mod_rewrite (Clean URLs)
1. In a directory block for `public`, set `AllowOverride All`
2. Copy the following htaccess file to public/.htaccess

**You may need to install mod_rewrite**

```
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.php$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule . /index.php [L]
</IfModule>
```

## Several pages
Now we will create additional pages with clean urls.  Replace `index.php` with the following code

```php
<?php
namespace Stateless;
require_once("../vendor/autoload.php");

switch (Request::getPath()) {
    case "/":
        echo "Home";
    break;

    case "/about":
        echo "Read all about us!";
    break;

    case "/contact":
        echo "Contact us.";
    break;

    default:
        Response::header(404);
        echo "Page not found.";
    break;

}
```

Load up `localhost/about` in your browser, you should see "Read all about us!"