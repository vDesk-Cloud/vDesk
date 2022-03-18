<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

/**
 * Represents an interface for a sequence of elements.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
interface IEnumerable extends \Traversable {

    /**
     * Returns the number of elements in a sequence.
     *
     * @return int The number of elements inside a sequence.
     */
    public function Count(): int;

    /**
     * Returns the first element in a sequence that satisfies a specified condition.
     *
     * @param callable $Predicate A function to test each element for a condition.
     *
     * @return mixed The first element in the sequence that passes the test in the specified predicate function, null if no element
     *                    passes the test in the specified predicate function.
     */
    public function Find(callable $Predicate): mixed;

    /**
     * Sorts the elements of a sequence according a specified function.
     *
     * @param callable $Predicate A function that defines the sort order.
     *
     * @return bool True if the sorting was successful; otherwise, false.
     */
    public function Sort(callable $Predicate): bool;

    /**
     * Filters a sequence of values based on a predicate.
     *
     * @param callable $Predicate A function to test each element for a condition.
     *
     * @return \vDesk\Struct\Collections\IEnumerable An IEnumerable that contains elements from the input sequence that satisfy the
     *                                               condition.
     */
    public function Filter(callable $Predicate): IEnumerable;

    /**
     * Creates a new sequence with the results of applying a provided function on every element in a sequence.
     *
     * @param callable $Predicate A predicate to apply on each element inside a sequence.
     *
     * @return \vDesk\Struct\Collections\IEnumerable An IEnumerable that contains all elements after applying the specified predicate
     *                                               function on each element of a sequence.
     */
    public function Map(callable $Predicate): IEnumerable;

    /**
     * Applies an accumulator function over a sequence.
     *
     * @param callable $Predicate    An accumulator function to be invoked on each element.
     * @param mixed    $InitialValue Value to use as the first argument to the first call of the $Predicate. If no initial value is
     *                               supplied, the first element in a sequence will be used.
     *
     * @return mixed The final accumulator value.
     */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed;

    /**
     * Determines whether any element of a sequence satisfies a condition.
     *
     * @param callable $Predicate The callback function to apply on each element inside the IList.
     *
     * @return bool True if at least one element inside the IList matches the predicate.
     */
    public function Any(callable $Predicate): bool;

    /**
     * Determines whether all elements of a sequence satisfy a condition.
     *
     * @param callable $Predicate A function to test each element for a condition.
     *
     * @return bool True if every element of the sequence passes the test in the specified predicate, or if the sequence is empty;
     *              otherwise, false.
     */
    public function Every(callable $Predicate): bool;

}

