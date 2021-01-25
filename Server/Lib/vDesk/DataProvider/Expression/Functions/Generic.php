<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression\Functions;

use vDesk\DataProvider\Expression\IAggregateFunction;
use vDesk\DataProvider;

/**
 * Represents a generic SQL function.
 *
 * @package vDesk\Connection\DataBase\Expression\Functions\MariaDB
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
final class Generic implements IAggregateFunction {
    
    /**
     * The name of the AggregateFunction\MariaDB.
     *
     * @var string
     */
    protected string $Name;
    
    /**
     * The values of the AggregateFunction\MariaDB.
     *
     * @var IAggregateFunction[]|mixed[]
     */
    protected array $Values;
    
    /**
     * Flag indicating whether the values of the
     *
     * @var bool
     */
    protected bool $EscapeFields;
    
    /**
     * The values of the AggregateFunction\MariaDB.
     *
     * @var bool
     */
    protected bool $EscapeValues;
    
    /**
     * Initializes a new instance of the AggregateFunction\MariaDB class.
     *
     * @param string $Name
     * @param mixed  ...$Values
     */
    public function __construct(string $Name, ...$Values) {
        $this->Name   = $Name;
        $this->Values = $Values;
    }
    
    /**
     * Returns the string representation of the AggregateFunction\MariaDB.
     *
     * @return string The string representation of the AggregateFunction\MariaDB.
     */
    public function __toString() {
        
        $Values  = [];
        $Matches = [];
        
        foreach($this->Values as $Value) {
            
            if($Value instanceof IAggregateFunction) {
                $Values[] = (string)$Value;
                continue;
            }
            
            if(\is_string($Value) && (int)\preg_match("/^(\w+)\.(\w+)$/", $Value, $Matches) > 0) {
                $Values[] = DataProvider::EscapeField($Matches[1]) . "." . DataProvider::EscapeField($Matches[2]);
                continue;
            }
            
            $Values[] = DataProvider::Sanitize($Value);
            
        }
        
        return \strtoupper($this->Name) . "(" . \implode(", ", $Values) . ")";
        
    }
}