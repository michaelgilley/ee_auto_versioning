# EE Auto Version

This extension comes in handy for better cache control with your EE assets.

## Version 0.1

* Requires: ExpressionEngine 2.4+
* Requires: Apache ModRewrite

## Description

To speed up your website you should be using Expires headers on static files. Leveraging browser caching has many
benefits to it including frontend speed increases, lower bandwidth, and lower server processing usage. This is especially
the case on sites using larger resources. However, this also creates an issue with revving newer versions of resources
once they replace older versions of the same file.

To fix this issue it's best to place a cache buster on the filename in your markup. There are principally two ways to do
this. One, and arguably the most widely used, is with append the filename with a query string such as: `main.css?1234`.
The other is to modify the filename itself like: `main.1234.css`. The former method is commonly chosen because it's
the simplest to maintain and requires very little to no extra server configuration. However, as 
[Steve Souders](http://www.stevesouders.com/blog/?p=25) found out
it's not the best method to use because many proxy services like [Squid](http://www.squid-cache.org/) ignore query strings on
filenames. Therefore, it's best to modify the filename itself and that's what this small 
extension does in [ExpressionEngine](http://ellislab.com/expressionengine).

The extension also owes a lot to [Kevin Hale](http://goo.gl/I1n3T).

## Caveats

Implementation of this extension makes the assumption that you are running EE2.4+ and that you are using at least some form
of the Apache Module Expires on your static content. This can easily be done in your `.htaccess` file like:

```apache
<FilesMatch "\.(gif|jpe?g|js|css)$">
  ExpiresActive On
  ExpiresDefault "access plus 10 years"
</FilesMatch>
```

## Installation

1. Create a directory called `auto_version` in `system/expressionengine/third_party/` and place the included files there.
2. Navigate to `CP Home > Addons > Extensions` and enable the extension.
3. Add the following rule to your `.htaccess` file:

```apache
#Rules for Versioned Static Files
<IfModule mod_rewrite.c>
 RewriteRule (.+)\.(\d+)\.(js|css)$ $1.$3 [L]
</IfModule>
```

This rule is specifically for use with css and js files. If you wanted to use this with images use the following:

```apache
<IfModule mod_rewrite.c>
  RewriteRule (.+)\.(\d+)\.(js|css|gif|png|jpe?g)$ $1.$3 [L]
</IfModule>
```

Doing this will tell Apache to treat all requests like `/assets/css/main.1234.css` as though they were `/assets/css/main.css`.

## Usage

The autoversion tag works just like EE's native `{stylesheet}` tag. The only difference is, it doesn't parse your assets
and so cut down on database calls but it does version them.

In your EE templates add the `{autoversion}` tag and the extension will do the rest:

```html
<head>
    <link rel="stylesheet" href="{autoversion="/assets/css/main.css"}">
    <script src="{autoversion="/assets/js/main.min.js"}"></script>
</head>
```

In this example the extension will replace the tag with the versioned url to your asset.
