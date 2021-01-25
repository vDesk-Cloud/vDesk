<?php
declare(strict_types=1);

namespace vDesk\Struct;

/**
 * Interface IStructuralComparable that represents a ...
 *
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
interface IStructuralComparable {
    
    /**
     * Compares the current instance of the IStructuralComparable to another IStructuralComparable and
     * determines if the IStructuralComparable is equal, before or after the current instance.
     *
     * @param \vDesk\Struct\IStructuralComparable $Compare The IStructuralComparable to compare against the current instance.
     * @param bool                                $Strict  Determines whether to compare strictly. If set to true, only fully compatible structures are allowed for comparison.
     *
     * @return int 1, if the current instance is greater than; -1, if lesser than; or 0, if is equal to the specified IStructuralComparable.
     */
    public function Compare(IStructuralComparable $Compare, bool $Strict = true): int;
    
}