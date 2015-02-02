Client for [Pupiq](http://i.pupiq.net/)
=======================================

It's designated to be integrated in ATK14 applications - i.e. applications powered by [ATK14 Framework](http://www.atk14.net).

Installation
------------

Just use the Composer:

```
$ cd path/to/your/atk14/project/
$ php composer.phar require atk14/pupiq-client dev-master

$ ln -s vendor/atk14/pupiq-client/src/app/fields/pupiq_image_field.php app/fields/
$ ln -s vendor/atk14/pupiq-client/src/app/wigets/pupiq_image_input.php app/wigets/
$ ln -s vendor/atk14/pupiq-client/src/app/helpers/modifier.img_url.php app/helpers/
$ ln -s vendor/atk14/pupiq-client/src/app/helpers/modifier.pupiq_img.php app/helpers/
```

Write your PUPIQ API KEY into config/settings.php

```php
define("PUPIQ_API_KEY","1234567890abcdefghijklmopqrst");
```

If you haven't yet the Composer installed, run the following commands
```
$ cd path/to/your/atk14/project/
$ curl -sS https://getcomposer.org/installer | php
```
