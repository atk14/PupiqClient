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
