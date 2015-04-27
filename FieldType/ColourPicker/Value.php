<?php
/**
 * File containing the DcColourPicker Value class
 *
 * @copyright Copyright (C) 2014 Springfoot Digital All rights reserved.
 * @license http://www.springfootdigital.com.au/Contact/Legal/MIT-License MIT License
 * @version
 */

namespace DanielClements\ColourPickerBundle\FieldType\ColourPicker;

use eZ\Publish\Core\FieldType\Value as BaseValue;

/**
 * Value for DcColourPicker field type
 */
class Value extends BaseValue
{
    /**
     * Text content
     *
     * @var string
     */
    public $hexValue;

    /**
     * Construct a new Value object and initialize it
     *
     * @param string $hexValue
     */
    public function __construct( $hexValue = '' )
    {
        parent::__construct( array(
            'hexValue' => $hexValue
        ) );
        //$this->hexValue = $hexValue;
    }

    /**
     * @see \eZ\Publish\Core\FieldType\Value
     */
    public function __toString()
    {
        return (string)$this->hexValue;
    }
}