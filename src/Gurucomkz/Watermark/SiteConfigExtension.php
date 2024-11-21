<?php

namespace Gurucomkz\Watermark;

use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Assets\Image;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\FieldType\DBEnum;

/**
 * SiteConfigExtension
 * 
 * @property string $WatermarkPosition
 * @property int $WatermarkMaxWidth
 * @property int $WatermarkMaxHeight
 * @property int $WatermarkXOffset
 * @property int $WatermarkYOffset
 *
 * @property Image $WatermarkImage
 */
class SiteConfigExtension extends Extension
{
    private static $db = [
        'WatermarkPosition' => 'Enum("Top,Right,Bottom,Left,Center,TopLeft,TopRight,BottomRight,BottomLeft","BottomRight")',
        'WatermarkMaxWidth' => 'Int',
        'WatermarkMaxHeight' => 'Int',
        'WatermarkXOffset' => 'Int',
        'WatermarkYOffset' => 'Int',
    ];

    private static $has_one = [
        'WatermarkImage' => Image::class,
    ];
    private static $owns = [
        'WatermarkImage',
    ];

    public function updateCMSFields(FieldList $fields)
    {
        /** @var DBEnum */
        $posOptions = $this->owner->dbObject('WatermarkPosition');
        $fields->addFieldsToTab('Root.Watermarking', [
            UploadField::create('WatermarkImage'),
            DropdownField::create('WatermarkPosition', 'Watermark position', $posOptions->enumValues()),
            FieldGroup::create('Watermark max size', [
                TextField::create('WatermarkMaxWidth', 'Width (%)'),
                TextField::create('WatermarkMaxHeight', 'Height (%)'),
            ])
            ->setDescription('No watermark will appear of one if these is zero.'),
            FieldGroup::create('Watermark offset', [
                TextField::create('WatermarkXOffset', 'Horizontal Offset'),
                TextField::create('WatermarkYOffset', 'Vertical Offset'),
            ]),
        ]);
    }
}
