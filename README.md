# Add watermarks to images in SilverStripe

Adds image watermarking to the list of available command for image manipulation in SilverStripe 4/5.

## Requirements

SilverStripe ~4 || ~5 || ~6

## Installation

```
composer require gurucomkz/watermark
```
Run `dev/build`, go to Backend Settings and configure the "Watermarking" tab.

## Usage

Module makes `Watermark()` function available when manipulating images. By default it sets the watermark's position as set in CMS Admin, but that can be overriden by supplying a parameter with the position, i.e. `$Image.Watermark('BottomLeft')`

## Examples
```
$Image.Watermark() //just add a watermark
$Image.Watermark().Fill(300,300) //cut image after watermarking - watermark may be cut
$Image.Watermark('TopLeft').Watermark('BottomRight') //put 2 watermarks
$Image.Fill(300,300).Watermark() //first cut, then put a watermark
$Image.FocusFill(300,300).Watermark() //works with jonom/focuspoint as well
```

## To Do

 * i18n capability
