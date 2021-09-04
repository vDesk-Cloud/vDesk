<?php
declare(strict_types=1);

namespace vDesk\DataProvider\Expression;

use vDesk\DataProvider\IExpression;

/**
 * Interface for abstract SQL "DELETE" Expressions.
 *
 * @package vDesk\DataProvider
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IDelete extends IExpression {

    /**
     * Applies one table to the IDelete.
     *
     * @param string $Table The table to delete a row from.
     *
     * @return \vDesk\DataProvider\Expression\IDelete The current instance for further chaining.
     */
    public function From(string $Table): IDelete;

    /**
     * Applies a set of conditions to the IDelete.
     *
     * @param mixed array ...$Conditions The conditions to apply.
     *
     * @return \vDesk\DataProvider\Expression\IDelete The current instance for further chaining.
     */
    public function Where(array ...$Conditions): IDelete;

}