<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

/**
 * Represents a collection of objects that can be individually accessed by an zero-based index.
 *
 * @package vDesk\Struct\Collections\Typed
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
interface ICollection extends IEnumerable, \ArrayAccess, \Iterator {

    /**
     * Initializes a new instance of the ICollection class.
     *
     * @param iterable $Elements Initializes the ICollection with the specified set of elements.
     */
    public function __construct(?iterable $Elements = []);

    /**
     * Adds an element to the ICollection.
     *
     * @param mixed $Element The element to add.
     */
    public function Add($Element): void;

    /**
     * Inserts an element into the ICollection at the specified index.
     *
     * @param int   $Index   The zero-based index at which the element should be inserted.
     * @param mixed $Element The element to insert.
     */
    public function Insert(int $Index, $Element): void;

    /**
     * Inserts an element into the ICollection after the element at the specified index.
     *
     * @param int   $Index   The zero-based index after which the element should be inserted.
     * @param mixed $Element The element to insert.
     */
    public function InsertAfter(int $Index, $Element): void;

    /**
     * Replaces an element of the ICollection with a different element.
     *
     * @param mixed $Element     The element to replace.
     * @param mixed $Replacement The new element to set.
     */
    public function Replace($Element, $Replacement): void;

    /**
     * Replaces the element at the specified index.
     *
     * @param mixed $Index   The index of the element to replace.
     * @param mixed $Element The new value for the element at the specified index.
     */
    public function ReplaceAt(int $Index, $Element): void;

    /**
     * Removes the first occurrence of a specific element from the ICollection.
     *
     * @param mixed $Element The element to remove from the ICollection.
     *
     * @return mixed|null The removed element or null if the element can't be found or removed.
     */
    public function Remove($Element);

    /**
     * Removes an element at the specified index from the ICollection.
     *
     * @param int $Index The index at which the element should be removed.
     *
     * @return mixed The removed element
     */
    public function RemoveAt(int $Index);

    /**
     * Searches for the specified element and returns the zero-based index of the first occurrence within the entire ICollection.
     *
     * @param mixed $Element The element to locate in the ICollection.
     *
     * @return mixed The index of the first occurrence of the element within the entire ICollection, if found.
     */
    public function IndexOf($Element): int;

    /**
     * Determines whether an element is in the ICollection.
     *
     * @param mixed $Element The element to locate in the ICollection.
     *
     * @return bool True if the element is in the ICollection, otherwise, false.
     */
    public function Contains($Element): bool;

    /**
     * Removes all elements from the ICollection.
     */
    public function Clear(): void;

    /**
     * Merges the elements of another ICollection into the ICollection.
     *
     * @param \vDesk\Struct\Collections\ICollection $List The ICollection to merge.
     */
    public function Merge(ICollection $List): void;

    /**
     * Copies the elements of the ICollection into an array.
     *
     * @param int $From The index in ICollection at which copying begins.
     * @param int $To   The index in ICollection at which copying ends.
     *
     * @return array An array containing the elements of the ICollection.
     */
    public function ToArray(int $From = null, int $To = null): array;

}