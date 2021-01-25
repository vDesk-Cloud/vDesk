<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Interface IStructuralEquatable that represents a ...
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface IStructuralEquatable {
    
    /**
     * Determines whether the current instance of the IStructuralEquatable is equal to a specified IStructuralEquatable.
     *
     * @param \vDesk\Struct\IStructuralComparable $Compare The IStructuralEquatable to compare against the current instance.
     * @param bool                                $Strict  Determines whether to compare strictly. If set to true, only fully compatible structures are allowed for comparison.
     *
     * @return bool True if the instance of the IStructuralEquatable is equal to the specified IStructuralEquatable; otherwise, false.
     */
    public function Equals(IStructuralComparable $Compare, bool $Strict = true): bool;
    
}