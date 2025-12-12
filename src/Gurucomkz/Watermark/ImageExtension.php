<?php

namespace Gurucomkz\Watermark;

use SilverStripe\Assets\Image;
use Intervention\Image\Image as InterventionImage;
use SilverStripe\Assets\Image_Backend;
use SilverStripe\Assets\Storage\AssetContainer;
use SilverStripe\Core\Extension;
use SilverStripe\SiteConfig\SiteConfig;

/**
 * @property-read Image $owner
 *
 */
class ImageExtension extends Extension
{

    private static function ssPos2BackendPos($pos)
    {
        $posmap = [
            'Center' => 'center',
            'BottomRight' => 'bottom-right',
            'BottomLeft' => 'bottom-left',
            'TopLeft' => 'top-left',
            'TopRight' => 'top-right',
            'TopCenter' => 'top-center',
            'Top' => 'top',
            'Bottom' => 'bottom',
            'Right' => 'right',
            'Left' => 'left',
        ];
        return $posmap[$pos];
    }

    /**
     * Apply a watermark
     *
     * @param string|null $position
     * @return AssetContainer|static
     */
    public function Watermark($position = null)
    {
        $image = $this->owner;

        /** @var SiteConfigExtension */
        $siteConfig = SiteConfig::current_site_config();
        $position = $position?:$siteConfig->WatermarkPosition;

        if (!$image || !$image->exists()) {
            return $image;
        }
        $imgW = $image->getWidth();
        $imgH = $image->getHeight();
        if (!$imgW || !$imgH) {
            return $image;
        }

        /** @var Image $watermark */
        $watermark = $siteConfig->WatermarkImage;
        if (!$watermark->exists()) {
            return $image;
        }
        if ($siteConfig->WatermarkMaxWidth <= 0 || $siteConfig->WatermarkMaxHeight <= 0) {
            return $image;
        }

        $wmW = (int)ceil($imgW / 100 * $siteConfig->WatermarkMaxWidth);
        $wmH = (int)ceil($imgH / 100 * $siteConfig->WatermarkMaxHeight);

        if ($wmW < 1 || $wmH < 1) {
            return $image;
        }

        /** @var Image */
        $fitMax = $watermark->FitMax($wmW, $wmH);
        $watermarkResource = $fitMax->getImageBackend()->getImageResource();
        if (!$watermarkResource) {
            return $image;
        }

        $wmXOffset = $siteConfig->WatermarkXOffset;
        $wmYOffset = $siteConfig->WatermarkYOffset;

        $backendAnchor = self::ssPos2BackendPos($position);
        $variant = $image->variantName(__FUNCTION__, $wmXOffset, $wmYOffset, $wmW, $wmH, $position, $watermark->ID);
        return $image->manipulateImage($variant, function (Image_Backend $backend) use ($wmXOffset, $wmYOffset, $watermarkResource, $backendAnchor) {
            // Cloning logic is taken from InterventionBackend::createCloneWithResource()

            /** @var InterventionImage $resource */
            $resource = $backend->getImageResource();

            if (!$resource) {
                return null;
            }

            $rcCopy = clone $resource;

            $rcCopy->place(
                $watermarkResource,
                $backendAnchor,
                $wmXOffset,
                $wmYOffset
            );

            $clone = clone $backend;
            $clone->setImageResource($rcCopy);

            return $clone;
        });
    }
}
