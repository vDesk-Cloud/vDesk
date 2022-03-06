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
     * <code>
     * //Example of using a predicate function for searching the Collection.
     * $Enumerable->Find(fn(T $Value, int $Index, IEnumerable $Enumerable) => $Value->ID === 12 && $Value->Name === "Foo");
     * //Returns the first element with $Element->ID = 12 and $Element->Name = "Foo" if exists.
     * </code>
     */
    public function Find(callable $Predicate): mixed;

    /**
     * Creates a new IEnumerable with the sorted elements of the current instance according a specified function.
     *
     * @param callable $Predicate A function that defines the sort order.
     *
     * @return \vDesk\Struct\Collections\IEnumerable A sorted IEnumerable that contains all elements in the order according the specified predicate.
     * <code>
     * //Example of using callback functions for sorting an IEnumerable.
     * $Enumerable->Sort(fn($a, $b) => $a->ID <=> $b->ID);
     * //Sorts the IEnumerable ascending by the value of the elements ID-property.
     * </code>
     */
    public function Sort(callable $Predicate): static;

    /**
     * Filters a sequence of values based on a predicate.
     *
     * @param callable $Predicate A predicate to test all elements for a condition.
     *
     * @return \vDesk\Struct\Collections\IEnumerable An IEnumerable that contains every element from the input IEnumerable that matches the specified predicate.
     * <code>
     * //Example of using callback functions for filtering an IEnumerable.
     * $Enumerable->Filter(fn(T $Value, int $Index, IEnumerable $Enumerable): bool => $Value->ID > 1 && $Value->ID < 6);
     * //Returns all elements whose value of $Value->ID is in the range between 1 and 6.
     * </code>
     */
    public function Filter(callable $Predicate): static;

    /**
     * Creates a new IEnumerable with the results of applying a provided predicate on every element in the IEnumerable.
     *
     * @param callable $Predicate A predicate to apply on all elements of the IEnumerable.
     *
     * @return \vDesk\Struct\Collections\IEnumerable An IEnumerable that contains all elements transformed by the specified predicate.
     */
    public function Map(callable $Predicate): static;

    /**
     * Applies an accumulator predicate over the IEnumerable.
     *
     * @param callable $Predicate    An accumulator predicate to apply on all elements.
     * @param mixed $InitialValue    Value to use as the first argument to the first call of the $Predicate.
     *                               If no initial value is supplied, the first element in the IEnumerable will be used.
     *
     * @return mixed The final accumulated value.
     */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed;

    /**
     * Determines whether any element of the IEnumerable matches a specified predicate.
     *
     * @param callable $Predicate The callback function to apply on each element inside the IEnumerable.
     *
     * @return bool True if at least one element of the IEnumerable matches the predicate; otherwise, false.
     */
    public function Any(callable $Predicate): bool;

    /**
     * Determines whether all elements of a sequence satisfy a condition.
     *
     * @param callable $Predicate A function to test each element for a condition.
     *
     * @return bool True if every element of the IEnumerable matches the specified predicate; otherwise, false.
     */
    public function Every(callable $Predicate): bool;

    /**
     * Copies the elements of the IEnumerable into an array.
     *
     * @return array An array containing the elements of the IEnumerable.
     */
    public function ToArray(): array;

    /**
     * Removes all elements from the IEnumerable.
     */
    public function Clear(): void;

}

