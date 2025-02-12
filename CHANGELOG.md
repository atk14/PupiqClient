Change Log
==========

All notable changes to PupiqClient will be documented in this file.

## [1.14.14] - 2024-08-20

* Form widget for PupiqImageField improved

## [1.14.13] - 2024-08-20

* f9d6ee7 - PHP8.1 compatibility issue

## [1.14.12] - 2024-05-27

* 259f222 - Added support for GIF images

## [1.14.11] - 2024-03-07

* d889ab9 - Markup for the removal checkboxes tuned

## [1.14.10] - 2024-03-06

* 3c05e26 - Markup for the removal checkbox tuned

## [1.14.9] - 2023-11-01

* 6929557 - A chessboard background used in PupiqImageInput

## [1.14.8] - 2023-08-01

* 1de1ebb - Socket timeout increased
* 484b98f - Required PHP >=5.6 and api/api-data-fetcher >=1.10.8 <2.0
* 6eaa94c - Using mod_rewrite instead of ErrorDocument
* 7d89553 - Using trigger_error() in PupiqErrorHandler

## [1.14.7] - 2022-12-05

* a40ea43 - Added mime types for AVIF images and APK files

## [1.14.6] - 2022-11-28

* cf65cb6 - PHP8.1 compatibility
* 6e784fd - Added support for AVIF images 

## [1.14.5] - 2022-09-05

* 03727e7 - Added method Pupiq::getFormat() and helper img_format

## [1.14.4] - 2022-08-17

* 009e955 - Added mime type text/calendar

## [1.14.3] - 2022-03-03

- 835ab22, 15108a3 - Using the right language in API calls

## [1.14.2] - 2021-10-20

- 3da4289 - Added more mime types into PupiqAttachment

## [1.14.1] - 2021-10-20

- Added mime type detection for webp images into PupiqAttachment

## [1.14] - 2021-03-19

- Added support for WebP images

## [1.13] - 2021-02-08

- Added AsyncPupiqAttachmentField (package atk14/async-file-field needs to be installed in order to use AsyncPupiqAttachmentField)

## [1.12.4] - 2021-02-07

- Dependency updated

## [1.12.3] - 2020-08-19

- Fix: svg image can't be converted into png or jpg (the image stays as svg)

## [1.12.2] - 2020-05-08

- In PupiqImageInput, thumbnail transformation corrected

## [1.12.1] - 2020-04-12

- "Transparent or colorful background" option fixed for SVG images
- For SVG images, the default value of enable_enlargement is true

## [1.12] - 2020-04-12

- Added support for SVG images

## [1.11] - 2020-02-24

- Added method PupiqAttachment::getAttachmentId()

## [1.10] - 2019-12-06

- Helper img_color: multiple colors can be specified in the desired order

## [1.9] - 2019-10-22

- Added methods Pupiq::getOriginalInfo() and Pupiq::downloadOriginal()

## [1.8.3] - 2019-09-25

- Markup of form widgets tuned (more)

## [1.8.2] - 2019-09-20

- Markup of form widgets tuned

## [1.8.1] - 2019-09-07

- PupiqImageInput and PupiqAttachmentInput tuned for usage in remote (XHR) forms

## [1.8] - 2019-08-19

- Added method Pupiq::ToObject()
- Added method Pupiq::getColors()
- Added helper img_color

## [1.7] - 2019-04-08

- Image border can be specified as transparent with a fallback color for jpg images (e.g. ```800x800xtransparent_or_#ffffff```)

## [1.6] - 2018-03-12

- New helpers (Smarty modifiers) added: img_width and img_height
- Added files for easier Local Proxy set up

## [1.5] - 2018-11-07

- Specific image format can be set in transformation string
- In PupiqImageField the removal checkbox is applicable only when the image or attachment is not required

## [1.4.2] - 2018-05-29

- Protected class variables, testing, minor fix

## [1.4] - 2018-04-18

- Added support for watermarks
- Added constant Pupiq::VERSION
- Default alt of an <img> tag is empty string (it was "Image")

## [1.3] - 2017-09-08

- Added support for xlsx attachments
- PupiqImageInput tuned
- Constant PUPIQ_HTTPS is automatically configured in expected way

## [1.2] - 2016-09-25

- Constant PUPIQ_HTTPS added

## [1.1] - 2016-09-15

- PupiqClient rewritten for usage of ApiDataFetcher

## [1.0] - 2016-09-15

- First version of the PupiqClient
