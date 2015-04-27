<?php
/**
 * File containing the DcColourPicker LegacyConverter class
 *
 * @copyright Copyright (C) 2014 Springfoot Digital All rights reserved.
 * @license http://www.springfootdigital.com.au/Contact/Legal/MIT-License MIT License
 * @version
 */

namespace DanielClements\ColourPickerBundle\FieldType\ColourPicker;
 
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
//use eZ\Publish\Core\Persistence\Legacy\Content\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;

class LegacyConverter implements Converter
{
    public function toStorageValue( FieldValue $value, StorageFieldValue $storageFieldValue )
    {
        $storageFieldValue->dataText = $value->data;
        $storageFieldValue->sortKeyInt = $value->sortKey;
    }
    public function toFieldValue( StorageFieldValue $value, FieldValue $fieldValue )
    {
        $fieldValue->data = $value->dataText;
        $fieldValue->sortKey = $value->sortKeyInt;
    }

    public function toStorageFieldDefinition( FieldDefinition $fieldDef, StorageFieldDefinition $storageDef )
    {
    }
     
    public function toFieldDefinition( StorageFieldDefinition $storageDef, FieldDefinition $fieldDef )
    {
    }

    public function getIndexColumn()
    {
        return 'sort_key_int';
    }
}

?>