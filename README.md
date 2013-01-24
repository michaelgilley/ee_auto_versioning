# EE Auto Version

This extension comes in handy for better cache control with your EE assets.

## Explanation

To speed up our websites we should be using Expires headers on our static files. Leveraging browser caching has many
benefits to it including frontend speed increases, lower bandwidth, and server processing usage. This is especially
the case on sites using larger resources. However, this also creates an issue with revving newer versions of resources
once they replace older versions of the same file.

To fix this issue it's best to place a cache buster on the filename in your markup. There are principally two ways to do
this. One, and arguably the most widely used, is with append the filename with a query string such as: `main.css?1234`.
The other is to modify the filename itself like so: `main.1234.css`. The previous method is commonly chosen because it's
the simplest to maintain and requires very little to no extra server configuration. However, as Steve Souders found out
it's not the best method becuase many proxy services like (Squid)[http://www.squid-cache.org/] ignore query strings on
filenames in their cacheing schema. Therefore, it's best to modify the filename itself and that's what this small 
extension does in (ExpressionEngine)[http://ellislab.com/expressionengine].

## Caveats

Implementation of this extension makes the assumption taht you are running EE2.x and that you are using at least some form
of the Apache Module Expires on your static content. This can easily be done in your `.htaccess` file like so:

    <FilesMatch "\.(gif|jpg|js|css)$">
      ExpiresActive On
      ExpiresDefault "access plus 10 years"
    </FilesMatch>

## Installation

Create a directory called `auto_version` in `system/expressionengine/third_party/` and place the included files there.

Add the following rule to your `.htaccess` file:

    #Rules for Versioned Static Files
    <IfModule mod_rewrite.c>
     RewriteRule (.+)\.(\d+)\.(js|css)$ $1.$3 [L]
    </IfModule>

This rule is specifically for use with css and js files. If you wanted to use this with image just use the following:

    <IfModule mod_rewrite.c>
      RewriteRule (.+)\.(\d+)\.(js|css|gif|png|jpe?g)$ $1.$3 [L]
    </IfModule>

Doing this will tell Apache to treat all requests such as `/assets/css/main.1234.css` as though they were `/assets/css/main.css`.

## Usage

In your EE templates simple add the `{autoversion}` tag in your link and script tags (where applicable) and the extension will do the rest. For example:

    <head>
        <link rel="stylesheet" href="{autoversion="/assets/css/main.css"}">
    </head>

In this example the extension will replace the tag with the versioned url to your asset.
