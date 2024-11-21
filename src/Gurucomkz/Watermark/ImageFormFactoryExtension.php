<?php

namespace Gurucomkz\Watermark;

use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\HTMLReadonlyField;

/**
 * FocusPoint Asset Form Factory extension.
 * Extends the CMS detail form to allow focus point selection.
 */
class ImageFormFactoryExtension extends Extension
{

    /**
     * Add FocusPoint field for selecting focus.
     */
    public function updateFormFields(FieldList $fields, $controller, $formName, $context)
    {
        $image = isset($context['Record']) ? $context['Record'] : null;
        if ($image && $image->appCategory() === 'image') {
            $wmField = HTMLReadonlyField::create(
                'Watermark Shortcode',
            )->setValue($this->WatermarkShortCode($image->ID));

            $titleField = $fields->fieldByName('Editor.Details.Title');
            if ($titleField) {
                if ($titleField->isReadonly()) {
                    $wmField = $wmField->performReadonlyTransformation();
                }
                $fields->insertAfter(
                    'Title',
                    $wmField
                );
            }
        }
    }

    public function WatermarkShortCode($ID)
    {
        return "[watermark id=" . $ID . "]";
    }
}
