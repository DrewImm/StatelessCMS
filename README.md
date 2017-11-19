# StatelessCMS
Headless content management system

## Introduction
StatelessCMS is a headless PHP framework designed for creating database-driven PHP applications.

## Installation
Download the latest release from [https://github.com/StatelessCMS/StatelessCMS/releases](https://github.com/StatelessCMS/StatelessCMS/releases).

If you would like the live development branch instead, download or clone from github.  [https://github.com/StatelessCMS/StatelessCMS.git](https://github.com/StatelessCMS/StatelessCMS.git)

Get a server running PHP 7.0 and a relational database.
*Note that apache is recommended, as Request::getHeaders() and Request::getToken() rely on apache_get_headers() as of v0.0.3*

## Setting up your first project
Although you are free to use any directory structure you choose, the common structure for StatelessCMS is as follows.  For now, you can just create the public folder, index.php, and the lib folder.

```
|- app
    |- Form
    |- Layout
    |- Menu
    |- Model
    |- View
|- public
    |- css
    |- js
    |- index.php
|- script
    |- test.sh
|- test
    |- js
    |- php
|- lib
    |- StatelessCMS

```

Look familiar?
If you didn't already, unzip the StatelessCMS archive to lib/StatelessCMS.

## Getting started
Open public/index.php.  Insert the following code:

```php
<?php
namespace Stateless;

require_once("../lib/StatelessCMS.php");

echo "Home";
```

## Test
Open a browser and navigate to localhost/public/
You should see Home.

## Configuring your server
Ensure your httpd document root is set to public/
Now navigate to localhost/ and you should see Home

## Enable mod_rewrite (Clean URLs)
1. Install/enable the mod_rewrite module
2. In a directory block for `public`, set `AllowOverride All`
3. Copy the following htaccess file to public/.htaccess

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
require_once("../lib/StatelessCMS.php");

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