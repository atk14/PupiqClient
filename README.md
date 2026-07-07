Pupiq Client
============

[![Tests](https://github.com/atk14/PupiqClient/actions/workflows/tests.yml/badge.svg)](https://github.com/atk14/PupiqClient/actions/workflows/tests.yml)

PHP client for [Pupiq](https://i.pupiq.net/) — a cloud image and attachment hosting service. Provides image upload, on-the-fly resizing, format conversion, watermarks, dominant colour detection, and local proxy caching.

Designed to integrate with [ATK14 Framework](http://www.atk14.net) applications, but the core `Pupiq` class can be used in any PHP project.

Installation
------------

Use Composer:

    cd path/to/your/atk14/project/
    composer require atk14/pupiq-client

Then create symlinks for the ATK14 fields, widgets, and template helpers you need:

    ln -s ../../vendor/atk14/pupiq-client/src/app/fields/pupiq_image_field.php app/fields/pupiq_image_field.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/widgets/pupiq_image_input.php app/widgets/pupiq_image_input.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/fields/pupiq_attachment_field.php app/fields/pupiq_attachment_field.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/widgets/pupiq_attachment_input.php app/widgets/pupiq_attachment_input.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/fields/async_pupiq_attachment_field.php app/fields/async_pupiq_attachment_field.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/widgets/async_pupiq_attachment_input.php app/widgets/async_pupiq_attachment_input.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_url.php app/helpers/modifier.img_url.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_attrs.php app/helpers/modifier.img_attrs.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_height.php app/helpers/modifier.img_height.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_width.php app/helpers/modifier.img_width.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_color.php app/helpers/modifier.img_color.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_format.php app/helpers/modifier.img_format.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.pupiq_img.php app/helpers/modifier.pupiq_img.php

Configuration
-------------

Add your Pupiq API key to `config/settings.php`:

    define("PUPIQ_API_KEY", "1234567890abcdefghijklmopqrst");

The following constants are optional:

| Constant | Default | Description |
|---|---|---|
| `PUPIQ_API_URL` | `https://i.pupiq.net/api/` | Pupiq API endpoint |
| `PUPIQ_LANG` | `auto` | Language for API responses (`auto`, `cs`, `en`, …) |
| `PUPIQ_IMG_HOSTNAME` | derived from `PUPIQ_API_URL` | Hostname used in generated image URLs |
| `PUPIQ_HTTPS` | auto-detected | Force HTTPS for generated image URLs |
| `PUPIQ_DEFAULT_WATERMARK_DEFINITION` | `default` | Name of the default watermark definition |
| `PUPIQ_API_VERIFY_PEER` | `true` | Verify the SSL/TLS certificate of the API server |
| `PUPIQ_API_VERIFY_PEER_NAME` | `true` | Verify the hostname in the SSL/TLS certificate |
| `PUPIQ_API_SOCKET_TIMEOUT` | `30.0` | API request timeout in seconds |

Set `PUPIQ_API_VERIFY_PEER` and `PUPIQ_API_VERIFY_PEER_NAME` to `false` only in development environments with self-signed certificates.

Usage in PHP
------------

### Uploading images

    // Upload from a URL
    $image = Pupiq::CreateImage("https://example.com/images/flower.jpg");

    // Upload a local file
    $image = Pupiq::CreateImage("/path/to/flower.jpg");

    // Upload a temporary file (e.g. from $_FILES) with a specific filename
    $image = Pupiq::CreateImage("/path/to/uploaded_file.tmp", $err_msg, "flower.jpg");
    $image = Pupiq::CreateImage(["path" => "/path/to/temp_file", "name" => "flower.jpg"]);

    if (!$image) {
        // $err_msg contains the error description
    }

    $url = $image->getUrl(); // store this URL in the database

### Uploading attachments

    $attachment = Pupiq::CreateAttachment("/path/to/file.pdf", "sample.pdf", $err_msg);

    if (!$attachment) {
        // $err_msg contains the error description
    }

    $url = $attachment->getUrl(); // store this URL in the database

### Rendering images from PHP

    $image = Pupiq::ToObject($url); // wrap a stored URL

    echo $image->getImgTag("100x100");
    // <img src="..." width="100" height="75" alt="" />

    echo $image->getImgTag("!100x100", ["alt" => "Flower", "attrs" => ["class" => "photo"]]);
    // <img src="..." width="100" height="100" alt="Flower" class="photo" />

    echo $image->getUrl("100x100");   // URL only
    echo $image->getFormat("100x100,format=webp"); // "webp"

Usage in templates
------------------

The following examples assume an 800×600 source image whose URL is stored in `$img`.

### Resizing

    {* Preserve aspect ratio *}
    {!$img|pupiq_img:"80"}       {* → 80×60 *}
    {!$img|pupiq_img:"x30"}      {* → 40×30 *}
    {!$img|pupiq_img:"80x80"}    {* → 80×60 *}

### Cropping

    {!$img|pupiq_img:"!80x80"}           {* 80×80, cropped to centre *}
    {!$img|pupiq_img:"80x80xcrop"}       {* 80×80, cropped to centre *}
    {!$img|pupiq_img:"80x80xcrop,top"}   {* 80×80, cropped from the top *}
    {!$img|pupiq_img:"80x80xcrop,bottom"}{* 80×80, cropped from the bottom *}

### Background fill

    {* Fit image and fill background with a colour *}
    {!$img|pupiq_img:"80x80x#ffffff"}        {* 80×80, white background *}

    {* Transparent background (PNG/WebP/AVIF only) *}
    {!$img|pupiq_img:"80x80xtransparent"}

    {* Transparent with a JPEG fallback colour *}
    {!$img|pupiq_img:"80x80xtransparent_or_#ffffff"}

### Format conversion

    {!$img|pupiq_img:"80x80,format=webp"}
    {!$img|pupiq_img:"80x80,format=png"}
    {!$img|pupiq_img:"80x80,format=jpg"}

### Enlargement

By default images are never magnified beyond their original size.

    {!$img|pupiq_img:"1600x1600"}                    {* → 800×600 (no enlargement) *}
    {!$img|pupiq_img:"1600x1600,enable_enlargement"} {* → 1600×1200 *}

### Attributes and manual `<img>` tags

    {* Pass HTML attributes as a string *}
    {!$img|pupiq_img:"80x80,enable_enlargement":"class='image-icon',title='Nice icon',data-clickable"}

    {* Pass HTML attributes as an array from the controller *}
    {!$img|pupiq_img:"80x80,enable_enlargement":$image_attributes_array}

    {* Render tag by hand *}
    <img src="{$img|img_url:"!80x80"}" width="80" height="80" alt="a nice butterfly">
    <img {!$img|img_attrs:"80x80"} alt="a nice butterfly">

    {* Determine width and height only *}
    Width is {$img|img_width:"80x80"} pixels
    Height is {$img|img_height:"80x80"} pixels

### Detecting dominant colours

`img_color` returns the dominant colour of an image as a hex string.

    {$img|img_color}              {* most dominant colour *}
    {$img|img_color:"light_muted"}{* a specific palette slot *}

Available palette names: `vibrant`, `light_vibrant`, `dark_vibrant`, `muted`, `light_muted`, `dark_muted`.

If the requested slot is unavailable a fallback can be specified:

    {$img|img_color:"light_vibrant"|default:"#FFFFFF"}

Multiple slots can be listed in preference order:

    {$img|img_color:"light_vibrant or light_muted or muted"|default:"#FFFFFF"}

### Watermarks

Create watermark definitions at `https://i.pupiq.net/api/cs/watermark_definitions/create_new/` first.

    {!$img|pupiq_img:"600x600xcrop,watermark"}          {* default watermark *}
    {!$img|pupiq_img:"600x600xcrop,watermark=default"}  {* same as above *}
    {!$img|pupiq_img:"600x600xcrop,watermark=logo"}     {* named watermark *}

Set up local proxy
------------------

With a local proxy, images are cached and served directly from your server.

**1. Create directories and symlinks:**

    cd path/to/your/atk14/project/
    mkdir i a
    chmod 777 i a
    ln -s ../vendor/atk14/pupiq-client/src/i/error.php i/error.php
    ln -s ../vendor/atk14/pupiq-client/src/i/.htaccess i/.htaccess
    ln -s ../vendor/atk14/pupiq-client/src/a/error.php a/error.php
    ln -s ../vendor/atk14/pupiq-client/src/a/.htaccess a/.htaccess

**2. Add to `.gitignore`:**

    i/*
    !i/.htaccess
    !i/error.php
    a/*
    !a/.htaccess
    !a/error.php

**3. Exclude proxy paths from the ATK14 dispatcher in `.htaccess`** (add before the `RewriteRule` that points to `dispatcher.php`):

    RewriteCond %{REQUEST_URI} !^\/i\/
    RewriteCond %{REQUEST_URI} !^\/a\/

Example of the relevant `.htaccess` block:

    RewriteCond %{REQUEST_URI} ^\/
    RewriteCond %{REQUEST_URI} !^\/public\/
    RewriteCond %{REQUEST_URI} !^\/server-status\/
    RewriteCond %{REQUEST_URI} !^\/server-info\/
    RewriteCond %{REQUEST_URI} !^\/i\/
    RewriteCond %{REQUEST_URI} !^\/a\/
    RewriteRule (.*) dispatcher.php [L]

**4. Define `PUPIQ_PROXY_HOSTNAME` in `config/settings.php`:**

    // ATK14 application
    define("PUPIQ_PROXY_HOSTNAME", $HTTP_REQUEST->getHttpHost() ?: ATK14_HTTP_HOST);

    // Non-ATK14 application
    define("PUPIQ_PROXY_HOSTNAME", "your.hostname.com");

License
-------

Pupiq Client is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license).

<!-- vim: set et: -->
