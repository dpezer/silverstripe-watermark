<?php
namespace Gurucomkz\Watermark;

use SilverStripe\Assets\Image;

class Shortcode
{

    public static function Watermark($arguments, $content = null)
    {

        if (!isset($arguments['file'])) {
            return '';
        }

        /** @var Image|ImageExtension */
        $image = Image::get()->filter("Name", $arguments['file'])->first();
        if (!isset($image)) {
            return '';
        }
        if (isset($arguments['position'])) {
            return $image->Watermark($arguments['position']);
        } else {
            return $image->Watermark();
        }
    }
}
