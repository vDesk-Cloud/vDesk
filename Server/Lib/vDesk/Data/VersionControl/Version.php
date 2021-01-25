<?php
declare(strict_types=1);

namespace vDesk\Data\VersionControl;

use vDesk\Struct\Number;
use vDesk\Struct\Properties;
use vDesk\Struct\Property\Getter;
use vDesk\Struct\Type;

/**
 * Class Version represents a version! Wooohooo!
 *
 * @package vDesk\Data\VersionControl
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Version {
    
    use Properties;
    
    /** @var null|mixed */
    private $_sNumber = null;
    
    /** @var null|string */
    private $_sContent = null;
    
    /**
     * Initializes a new instance of the Version class.
     *
     * @param string $Number  Initializes the Version with the specified number.
     * @param string $Content Initializes the Version with the specified content.
     */
    public function __construct(string $Number, string $Content) {
        
        $this->AddProperties([
            "Number"  => [
                Get => Getter::Create($this->_sNumber),
                Set => function($Value): void {
                    if(!Number::IsNumeric($Value)) {
                        throw new \InvalidArgumentException("The value for Version::Number must be a numeric value!");
                    }
                    $this->_sNumber = $this->_sNumber ?? $Value;
                }
            ],
            "Content" => [
                Get => Getter::Create($this->_sContent, Type::String, true),
                Set => function(string $Value): void {
                    $this->_sContent = $this->_sContent ?? $Value;
                }
            ]
        ]);
        $this->_sNumber  = $Number;
        $this->_sContent = $Content;
    }
    
}