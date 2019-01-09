Client for [Pupiq](http://i.pupiq.net/)
=======================================

It's designated to be integrated in ATK14 applications - i.e. applications powered by [ATK14 Framework](http://www.atk14.net).

Installation
------------

Just use the Composer:

    cd path/to/your/atk14/project/
    composer require atk14/pupiq-client

    ln -s ../vendor/atk14/pupiq-client/src/lib/pupiq.php lib/pupiq.php
    ln -s ../vendor/atk14/pupiq-client/src/lib/pupiq_attachment.php lib/pupiq_attachment.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/fields/pupiq_attachment_field.php app/fields/pupiq_attachment_field.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/widgets/pupiq_attachment_input.php app/widgets/pupiq_attachment_input.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/fields/pupiq_image_field.php app/fields/pupiq_image_field.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/widgets/pupiq_image_input.php app/widgets/pupiq_image_input.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_url.php app/helpers/modifier.img_url.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_attrs.php app/helpers/modifier.img_attrs.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_height.php app/helpers/modifier.img_height.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_width.php app/helpers/modifier.img_width.php
    ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.pupiq_img.php app/helpers/modifier.pupiq_img.php

Configuration
------------

Write your PUPIQ API KEY into config/settings.php

    define("PUPIQ_API_KEY","1234567890abcdefghijklmopqrst");

Usage in templates
------------------

Consider an image in the original resolution 800x600. In the string variable $img there is URL to the image.

    To preserve aspect ratio:
    {!$img|pupiq_img:"80"} {* 80x60 *}
    {!$img|pupiq_img:"x30"} {*  40x30 *}
    {!$img|pupiq_img:"80x80"} {*  80x60 *}

    To crop the image:
    {!$img|pupiq_img:"!80x80"} {* 80x80 *}
    {!$img|pupiq_img:"80x80xcrop"} {* 80x80 *}

    Top crop image to the top or bottom line:
    {!$img|pupiq_img:"80x80xcrop,top"} {* 80x80 *}
    {!$img|pupiq_img:"80x80xcrop,bottom"} {* 80x80 *}

    To preserve aspect ratio and fill the background size with a specific colour:
    {!$img|pupiq_img:"80x80x#ffffff"} {* 80x80, the image is not cropped *}

    To add some attributes to img tag:
    {!$img|pupiq_img:"80x80,enable_enlargement":"class='image-icon',title='Nice icon',data-clickable"}

    To set a specific format:
    {!$img|pupiq_img:"80x80,format=png"}
    {!$img|pupiq_img:"80x80,format=jpg"}

    To add some attributes prepared as array (got from a controller for example):

    class SomeController extends ApplicationController {
    ....
        $this->tpl_data["image_attributes_array"] = array(
            "class" => "image-icon",
            "title" => "Nice icon",
            "data-clickable" => true
        );
    ....
    }

    {!$img|pupiq_img:"80x80,enable_enlargement":$image_attributes_array}

    To magnify:
    {!$img|pupiq_img:"1600x1600"} {* 800x600, i.e. there is no magnification by default *}
    {!$img|pupiq_img:"1600x1600,enable_enlargement"} {* 1600x1200 *}

    To render a <img> tag by hand:
    <img src="{$img|img_url:"!80x80"}" width="80" height="80" alt="a nice butterfly">
    <img {!$img|img_attrs:"80x80"} alt="a nice butterfly">

    To determine image width and height:
    Width is {$img|img_width:"80x80"} pixels
    Height is {$img|img_height:"80x80"} pixels

### Watermarks

At first you need to create one or more watermark definitions at address https://i.pupiq.net/api/cs/watermark_definitions/create_new/

The default watermark should be named "default". When you didn't mention the name of the watermark, "default" is used.

    {!$img|pupiq_img:"600x600xcrop,watermark"} {* default *}
    {!$img|pupiq_img:"600x600xcrop,watermark=default"} {* also default *}
    {!$img|pupiq_img:"600x600xcrop,watermark=logo"} {* watermark definition named logo is used *}

License
-------

Pupiq Client is free software distributed [under the terms of the MIT license](http://www.opensource.org/licenses/mit-license)

<!-- vim: set et: -->
