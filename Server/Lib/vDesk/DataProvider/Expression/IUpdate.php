<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\DataProvider\IExpression;

/**
 * Interface that represents an UPDATE SQL expression.
 *
 * @package vDesk\DataProvider\Expression
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
interface IUpdate extends IExpression {

    /**
     * Initializes a new instance of the IUpdate class.
     *
     * @param string $Table Initializes the IUpdate with the specified table to update.
     */
    public function __construct(string $Table);
    
    /**
     * Applies one or multiple fields and values to update to the IUpdate.
     *
     * @param mixed[] $Fields The fields and values to update.
     *
     * @return \vDesk\DataProvider\Expression\IUpdate The current instance for further chaining.
     */
    public function Set(array $Fields): IUpdate;
    
    /**
     * Applies one or multiple fields to conditionally update or key-value-pairs to the IUpdate.
     *
     * @param mixed $Fields The fields to update.
     *
     * @return \vDesk\DataProvider\Expression\IUpdate The current instance for further chaining.
     */
    public function SetIf(array $Fields): IUpdate;
    
    
    /**
     * Applies a set of conditions to the IUpdate.
     *
     * @param array ...$Conditions
     *
     * @return \vDesk\DataProvider\Expression\IUpdate The current instance for further chaining.
     */
    public function Where(array ...$Conditions): IUpdate;

}