Client for [Pupiq](http://i.pupiq.net/)
=======================================

It's designated to be integrated in ATK14 applications - i.e. applications powered by [ATK14 Framework](http://www.atk14.net).

Installation
------------

Just use the Composer:
```bash
cd path/to/your/atk14/project/
php composer.phar require atk14/pupiq-client dev-master

ln -s ../vendor/atk14/pupiq-client/src/lib/pupiq.php lib/pupiq.php
ln -s ../vendor/atk14/pupiq-client/src/lib/pupiq_attachment.php lib/pupiq_attachment.php
ln -s ../../vendor/atk14/pupiq-client/src/app/fields/pupiq_attachment_field.php app/fields/pupiq_attachment_field.php
ln -s ../../vendor/atk14/pupiq-client/src/app/fields/pupiq_image_field.php app/fields/pupiq_image_field.php
ln -s ../../vendor/atk14/pupiq-client/src/app/widgets/pupiq_image_input.php app/widgets/pupiq_image_input.php
ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_url.php app/helpers/modifier.img_url.php
ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.img_attrs.php app/helpers/modifier.img_attrs.php
ln -s ../../vendor/atk14/pupiq-client/src/app/helpers/modifier.pupiq_img.php app/helpers/modifier.pupiq_img.php
```

If you haven't yet the Composer installed, run the following commands
```bash
cd path/to/your/atk14/project/
curl -sS https://getcomposer.org/installer | php
```

Configuration
------------

Write your PUPIQ API KEY into config/settings.php
```php
define("PUPIQ_API_KEY","1234567890abcdefghijklmopqrst");
```

Usage in templates
------------------

Consider an image in the original resolution 800x600. In the string variable $img there is URL to the image.

```smarty
To preserve aspect ratio
{!$img|pupiq_img:"80"} {* 80x60 *}
{!$img|pupiq_img:"x30"} {*  40x30 *}
{!$img|pupiq_img:"80x80"} {*  80x60 *}
 
To crop the image
{!$img|pupiq_img:"!80x80"} {* 80x80 *}
{!$img|pupiq_img:"80x80xcrop"} {* 80x80 *}
 
To magnify
{!$img|pupiq_img:"1600x1600"} {* 800x600, i.e. there is no magnification by default *}
{!$img|pupiq_img:"1600x1600,enable_enlargement"} {* 1600x1200 *}
 
To render a <img> tag by hand
<img src="{$img|img_url:"!80x80"}" width="80" height="80" alt="a nice butterfly">
<img {$img|img_attrs:"80x80"} alt="a nice butterfly">
```

