<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

/**
 * Represents a key-value based collection of objects that can be individually accessed by index.
 *
 * @package vDesk\Struct\Collections
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
interface IDictionary extends IEnumerable, \ArrayAccess, \Iterator {

    /**
     * Initializes a new instance of the IDictionary class.
     *
     * @param iterable $Elements Initializes the IDictionary with the specified set of elements.
     */
    public function __construct(?iterable $Elements = []);

    /**
     * Adds an element to the IDictionary.
     *
     * @param string $Key     The key to access the element.
     * @param mixed  $Element The element to add.
     */
    public function Add(string $Key, $Element): void;

    /**
     * Inserts an element into the IDictionary at the position of the element with the specified key.
     *
     * @param string $Before  The key of the element of which the new element will be inserted before.
     * @param string $Key     The key of the element to insert.
     * @param mixed  $Element The element to insert.
     */
    public function Insert(string $Before, string $Key, $Element): void;

    /**
     * Inserts an element into the IDictionary after the position of the element with the specified key.
     *
     * @param string $After The key of the element of which the new element will be inserted after.
     * @param string $Key   The key of the element to insert.
     * @param mixed  $Value The element to insert.
     */
    public function InsertAfter(string $After, string $Key, $Value): void;

    /**
     * Replaces an element of the IDictionary with a different element.
     *
     * @param mixed $Element     The old value of the element to replace.
     * @param mixed $Replacement The element to set.
     */
    public function Replace($Element, $Replacement): void;

    /**
     * Replaces the element with the specified key of the IDictionary.
     *
     * @param string $Key     The key of the element to replace.
     * @param mixed  $Element The element to replace with the element with the specified key.
     */
    public function ReplaceAt(string $Key, $Element): void;

    /**
     * Removes the first occurrence of a specific element from the IDictionary.
     *
     * @param mixed $Element The element to remove from the IDictionary.
     *
     * @return mixed|null The removed element or null if the element can't be found or removed.
     */
    public function Remove($Element);

    /**
     * Removes an element with the specified key from the IDictionary.
     *
     * @param string $Key The key of the element to remove.
     *
     * @return mixed The removed element.
     */
    public function RemoveAt(string $Key);

    /**
     * Searches for the specified element and returns the key of the first occurrence within the entire IDictionary.
     *
     * @param mixed $Element The element to locate in the IDictionary.
     *
     * @return string|null The key of the first occurrence of the element within the entire IDictionary, if found; otherwise, null.
     */
    public function KeyOf($Element): ?string;

    /**
     * Changes the key associated with the specified element in the IDictionary.
     *
     * @param mixed  $Element The element to change the key of.
     * @param string $Key     The new key for the element.
     */
    public function ChangeKey($Element, string $Key): void;

    /**
     * Determines whether an element is in the IDictionary.
     *
     * @param mixed $Element The element to check.
     *
     * @return bool True if the element is in the IDictionary, otherwise, false.
     */
    public function Contains($Element): bool;

    /**
     * Determines whether an element with the specified key is in the IDictionary.
     *
     * @param string $Key The key to check.
     *
     * @return bool True if an element with the specified key is in the IDictionary, otherwise, false.
     */
    public function ContainsKey(string $Key): bool;

    /**
     * Removes all elements from the IDictionary.
     */
    public function Clear(): void;

    /**
     * Merges the elements of another IDictionary into the IDictionary.
     *
     * @param \vDesk\Struct\Collections\IDictionary $Map The IDictionary to merge.
     */
    public function Merge(IDictionary $Map): void;

    /**
     * Copies the elements of the IDictionary into an array.
     *
     * @param string $From The key in IDictionary at which copying begins.
     * @param string $To   The key in IDictionary at which copying ends.
     *
     * @return array An array containing the elements of the IDictionary.
     */
    public function ToArray(string $From = null, string $To = null): array;
}