<?php
/**
 * File containing the DcColourPicker Type class
 *
 * @copyright Copyright (C) 2014 Springfoot Digital All rights reserved.
 * @license http://www.springfootdigital.com.au/Contact/Legal/MIT-License MIT License
 * @version
 */

namespace DanielClements\ColourPickerBundle\FieldType\ColourPicker;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\Core\FieldType\Value as BaseValue;

/**
 * The DcColourPicker field type.
 *
 * This field type represents a hex colour value.
 */
class Type extends FieldType
{
    /**
     * Validation configuration schema.
     */
    protected $validatorConfigurationSchema = array(
        'StringLengthValidator' => array(
            'minStringLength' => array(
                'type' => 'int',
                'default' => 6
            ),
            'maxStringLength' => array(
                'type' => 'int',
                'default' => 7
            )
        )
    );
    
    /**
     * Validates the validatorConfiguration
     *
     * @param mixed $validatorConfiguration
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateValidatorConfiguration( $validatorConfiguration )
    {
        $validationErrors = array();

        foreach ( $validatorConfiguration as $validatorIdentifier => $constraints )
        {
            if ( $validatorIdentifier !== 'StringLengthValidator' )
            {
                    $validationErrors[] = new ValidationError(
                        "Validator '%validator%' is unknown",
                        null,
                        array(
                        "validator" => $validatorIdentifier
                    )
                );
                
                continue;
            }
        
            foreach ( $constraints as $name => $value )
            {
                switch ( $name )
                {
                    case "minStringLength":
                    case "maxStringLength":
                        if ( $value !== false && !is_integer( $value ) )
                        {
                                $validationErrors[] = new ValidationError(
                                "Validator parameter '%parameter%' value must be of integer type",
                                null,
                                array(
                                    "parameter" => $name
                                )
                            );
                        }
                        break;
    
                    default:
                        $validationErrors[] = new ValidationError(
                            "Validator parameter '%parameter%' is unknown",
                            null,
                            array(
                                "parameter" => $name
                            )
                        );
                }
            }
        }
        
        return $validationErrors;
    }
    
    /**
     * Validating the data
     */
    public function validate( FieldDefinition $fieldDefinition, SPIValue $fieldValue )
    {
        $errors = array();
        
        // Return if the value is empty / null object
        if ( $this->isEmptyValue( $fieldValue ) )
        {
            return $errors;
        }
     
        $validatorConfiguration = $fieldDefinition->getValidatorConfiguration();

        // Setting up constraints value for the string length
        $constraints = isset( $validatorConfiguration['StringLengthValidator'] )
            ? $validatorConfiguration['StringLengthValidator']
            : array();
        
        // Checking max length is ok
        if ( isset( $constraints['maxStringLength'] ) &&
            $constraints['maxStringLength'] !== false &&
            $constraints['maxStringLength'] !== 0 &&
            strlen( $fieldValue->hexValue ) > $constraints['maxStringLength'] )
        {
            $validationErrors[] = new ValidationError(
                "The string can not exceed %size% character.",
                "The string can not exceed %size% characters.",
                array(
                    "size" => $constraints['maxStringLength']
                )
            );
        }
        
        // Checking min length is ok
        if ( isset( $constraints['minStringLength'] ) &&
            $constraints['minStringLength'] !== false &&
            $constraints['minStringLength'] !== 0 &&
            strlen( $fieldValue->hexValue ) < $constraints['minStringLength'] )
        {
            $validationErrors[] = new ValidationError(
                "The value can not be shorter than %size% character.",
                "The value can not be shorter than %size% characters.",
                array(
                    "size" => $constraints['minStringLength']
                )
            );
        }        
        
        // If we have no errors check the string is actually a hex colour value
        if (count($validationErrors) == 0)
        {
            if ( !preg_match('/^#[a-f0-9]{6}$/i', $hexValue) and !preg_match('/^[a-f0-9]{6}$/i', $color) )
            {
                $validationErrors[] = new ValidationError(
                    "The value must be a hex colour.",
                    null,
                    null
                );
            }
        }

     
        return $errors;
    }
    
    /*
     * Name for use in URI or as content name
     */
    public function getName( SPIValue $value )
    {
        return str_replace('#', '', $value->hexValue);
    }
     
    /**
     * Sort value. Using the RGB value.
     * 
     * I don't think you'll ever want to sort by a colour value though.
     */
    protected function getSortInfo( CoreValue $value )
    {
        list($r, $g, $b) = sscanf($value->hexValue, "#%02x%02x%02x");
        return $r.$g.$b;
    }
    
    /*
     * Serialization methods
     */
    public function fromHash( $hash )
    {
        if ( $hash === null )
            return $this->getEmptyValue();

        return new Value( $hash );
    }
     
    public function toHash( SPIValue $value )
    {
        if ( $this->isEmptyValue( $value ) )
            return null;

        return $value->hexValue;
    }
    
    /**
     * @param \SFDigital\ColourPickerBundle\FieldType\ColourPicker\Value $value
     * @return \eZ\Publish\SPI\Persistence\Content\FieldValue
     */
    public function toPersistenceValue( SPIValue $value )
    {
        if ( $value === null )
        {
            return new PersistenceValue(
                array(
                    "data" => null,
                    "externalData" => null,
                    "sortKey" => null,
                )
            );
        }
        
        // Value is not null
        return new PersistenceValue(
            array(
                "data" => $this->toHash( $value ),
                "sortKey" => $this->getSortInfo( $value ),
            )
        );
    }
    
    /**
    * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
    * @return \SFDigital\ColourPickerBundle\FieldType\ColourPicker\Value
    */
    public function fromPersistenceValue( PersistenceValue $fieldValue )
    {
        if ( $fieldValue->data === null )
        {
            return $this->getEmptyValue();
        }
        
        return new Value( $fieldValue->data );
    }
    
    /**
     * Method for returning the identification string for the FieldType
     *
     * @return string
     */
    public function getFieldTypeIdentifier()
    {
        return 'dccolourpicker';
    }

    /**
     * Creating the new hex value from the supplied input
     */
    protected function createValueFromInput( $hexValue )
    {
        if ( is_string( $hexValue ) )
        {
            // Hex value with hash as start
            if ( preg_match('/^#[a-f0-9]{6}$/i', $hexValue) )
                $inputValue = new Value( $hexValue );
            
            // Hex value with no hash
            if ( preg_match('/^[a-f0-9]{6}$/i', $color) )
                $inputValue = new Value( '#'.$hexValue );
        }
        
        return $inputValue;
    }

    /**
     * Check that the hex value that has been sent is of type string
     * 
     * @throws eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    protected function checkValueStructure( BaseValue $value )
    {
        // Test if value is a string and is a hex colour value
        if ( !( is_string( $value->hexValue ) and preg_match('/^#[a-f0-9]{6}$/i', $value->hexValue) ) )
        {
            throw new eZ\Publish\Core\Base\Exceptions\InvalidArgumentType(
                '$value->hexValue',
                'string',
                $value->hexValue
            );
        }
    }

    /**
     * Returning the null object
     */
    public function getEmptyValue()
    {
        return new Value;
    }
    
    /**
     * Returns whether the field type is searchable
     *
     * @return boolean
     */
    public function isSearchable()
    {
        return false;
    }
}