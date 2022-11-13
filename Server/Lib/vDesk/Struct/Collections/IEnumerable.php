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
     * Initializes a new instance of the IEnumerable class.
     *
     * @param iterable $Elements Initializes the IEnumerable with the specified set or map of elements.
     */
    public function __construct(iterable $Elements = []);

    /**
     * Returns the number of elements in a sequence.
     *
     * @return int The number of elements inside a sequence.
     */
    public function Count(): int;

    /**
     * Returns the first element in the IEnumerable.
     *
     * @param bool $Remove Flag indicating whether to remove the first element instead of just returning it.
     *
     * @return mixed The first element in the IEnumerable; otherwise if the IEnumerable is empty, null.
     */
    public function First(bool $Remove = false): mixed;

    /**
     * Returns the last element in the IEnumerable.
     *
     * @param bool $Remove Flag indicating whether to remove the last element instead of just returning it.
     *
     * @return mixed The last element in the IEnumerable; otherwise if the IEnumerable is empty, null.
     */
    public function Last(bool $Remove = false): mixed;

    /**
     * Creates a new IEnumerable that contains every element of the current instance in a reverse order.
     *
     * @return \vDesk\Struct\Collections\IEnumerable An IEnumerable that contains every element of the current instance in a reverse order.
     */
    public function Reverse(): static;

    /**
     * Returns the first element in the IEnumerable that matches a specified predicate function.
     *
     * @param callable $Predicate A predicate function to match against all elements.
     *
     * @return mixed The first element in the IEnumerable that matches specified predicate function; otherwise, null.
     * <code>
     * //Example of using a predicate function for searching an IEnumerable.
     * $Enumerable->Find(fn(T $Value, int $Index, IEnumerable $Enumerable) => $Value->ID === 12 && $Value->Name === "Foo");
     * //Returns the first element with $Element->ID = 12 and $Element->Name = "Foo" if exists.
     * </code>
     */
    public function Find(callable $Predicate): mixed;

    /**
     * Creates a new IEnumerable that contains every element of the current instance sorted according a specified predicate function.
     *
     * @param callable $Predicate The predicate function that defines the sort order.
     *
     * @return \vDesk\Struct\Collections\IEnumerable A sorted IEnumerable that contains all elements in the order according the specified predicate function.
     * <code>
     * //Example of using callback functions for sorting an IEnumerable.
     * $Enumerable->Sort(fn($First, $Second) => $First->ID <=> $Second->ID);
     * //Sorts the IEnumerable ascending by the value of the elements ID-property.
     * </code>
     */
    public function Sort(callable $Predicate): static;

    /**
     * Creates a new IEnumerable that contains every element of the current instance filtered by a specified predicate function.
     *
     * @param callable $Predicate The predicate function that specified the filtering condition.
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
     * Creates a new IEnumerable with the results of applying a specified predicate function on every element in the IEnumerable.
     *
     * @param callable $Predicate The predicate function to transform every element of the IEnumerable.
     *
     * @return \vDesk\Struct\Collections\IEnumerable An IEnumerable that contains all elements transformed by the specified predicate function.
     * <code>
     * //Example of using callback functions for filtering an IEnumerable.
     * $Enumerable->Map(fn(T $Value, int $Index, IEnumerable $Enumerable): bool => $Value->ID > 1 && $Value->ID < 6);
     * //Returns all elements whose value of $Value->ID is in the range between 1 and 6.
     * </code>
     */
    public function Map(callable $Predicate): static;

    /**
     * Applies an accumulator predicate function on every element of the IEnumerable.
     *
     * @param callable $Predicate    The accumulator predicate function to apply on every element of the IEnumerable.
     * @param mixed    $InitialValue Value to use as the first argument to the first call of the $Predicate.
     *                               If no initial value is supplied, the first element in the IEnumerable will be used.
     *
     * @return mixed The final accumulated value.
     */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed;

    /**
     * Determines whether any element of the IEnumerable matches a specified predicate function.
     *
     * @param callable $Predicate The predicate function to match against any elements of the IEnumerable.
     *
     * @return bool True if at least one element of the IEnumerable matches the predicate function; otherwise, false.
     */
    public function Any(callable $Predicate): bool;

    /**
     * Determines whether all elements of the IEnumerable matches a specified predicate function.
     *
     * @param callable $Predicate The predicate function to match against every element of the IEnumerable.
     *
     * @return bool True if every element of the IEnumerable matches the specified predicate function; otherwise, false.
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