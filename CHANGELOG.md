Change Log
==========

All notable changes to PupiqClient will be documented in this file.

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
