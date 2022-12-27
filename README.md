# Filament Cropper Plugin

[![Latest Version on Packagist](https://img.shields.io/packagist/v/nuhel/filament-cropper.svg?style=flat-square)](https://packagist.org/packages/nuhel/filament-cropper)
[![Total Downloads](https://img.shields.io/packagist/dt/nuhel/filament-cropper.svg?style=flat-square)](https://packagist.org/packages/nuhel/filament-cropper)


## Installation

You can install the package via composer:

```bash
composer require nuhel/filament-cropper
```

This field has most of the same functionality of the [Filament File Upload](https://filamentphp.com/docs/2.x/forms/fields#file-upload) field.

![screenshot of square croppie](./images/example.png)
```php
  Cropper::make('image')
      ->getUploadedFileNameForStorageUsing(function (TemporaryUploadedFile $file): string {
          return (string)str("image_path/" . $file->hashName());
      })->enableDownload()
      ->enableOpen()
      ->enableImageRotation()
      ->enableImageFlipping()
      ->imageCropAspectRatio('16:9'),
```
Using `imageCropAspectRatio` we can set aspect ratio of the cropper.

```php
Cropper::make('avatar')
        ->avatar()
        ->enableOpen()
        ->enableDownload()
        ->modalSize('xl'),
```
We can make cropper circular using `avatar` method.
![screenshot of big modal](./images/circural-example.png)

Modal size can be customized if it is needed,
using `modalSize` method.
```php
Cropper::make('avatar')
        ->avatar()
        ->enableOpen()
        ->enableDownload()
        ->modalSize('xl')
        ->modalHeading("Crop Background Image")
```
![screenshot of big modal](./images/xl-modal-example.png)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
