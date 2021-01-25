<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Properties;

/**
 * Represents a generic iterable list of key-value pairs.
 *
 * @property-read int      $Count  Gets the amount of elements in the Dictionary.
 * @property-read string[] $Keys   Gets all keys of the Dictionary
 * @property-read mixed[]  $Values Gets all values of the Dictionary
 * @package vDesk\Struct\Collections
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Dictionary implements IDictionary {
    
    use Properties;
    
    /**
     * The elements of the Dictionary.
     *
     * @var array
     */
    protected array $Elements = [];
    
    /**
     * Initializes a new instance of the Dictionary class.
     *
     * @param iterable $Elements Initializes the Dictionary with the specified set of elements.
     */
    public function __construct(iterable $Elements = []) {
        $this->AddProperties([
            "Count"  => [
                \Get => fn(): int => \count($this->Elements)
            ],
            "Keys"   => [
                \Get => fn(): array => \array_keys($this->Elements)
            ],
            "Values" => [
                \Get => fn(): array => \array_values($this->Elements)
            ]
        ]);
        foreach($Elements as $Key => $Element) {
            $this->Add($Key, $Element);
        }
    }
    
    /**
     * Adds an element to the Dictionary.
     *
     * @param string $Key     The key of the element it should be accessible.
     * @param mixed  $Element The element to add.
     *
     * @throws \vDesk\Struct\Collections\DuplicateKeyException Thrown if an element with an equal key already exists.
     */
    public function Add(string $Key, mixed $Element): void {
        if(isset($this->Elements[$Key])) {
            throw new DuplicateKeyException("An element with the same key '$Key' already exists.");
        }
        $this->Elements[$Key] = $Element;
    }
    
    /**
     * Changes the key associated with the specified element in the IKeyedCollection.
     *
     * @param mixed  $Element The element to change the key of.
     * @param string $Key     The new key for the element.
     */
    public function ChangeKey(mixed $Element, string $Key): void {
        if(($OldKey = $this->KeyOf($Element)) !== null) {
            $this->Elements[$Key] = $this->Elements[$OldKey];
            unset($this->Elements[$OldKey]);
        }
    }
    
    /**
     * Searches for the specified element and returns the key of the first occurrence within the entire Dictionary.
     *
     * @param mixed $Element The element to locate in the IMap.
     *
     * @return mixed|null The key of the first occurrence of the element within the entire IMap, if found; otherwise, null.
     */
    public function KeyOf(mixed $Element): ?string {
        foreach($this->Elements as $Key => $mValue) {
            if($Element === $mValue) {
                return $Key;
            }
        }
        return null;
    }
    
    /**
     * Copies the elements of the Dictionary into an array.
     *
     * @param null|string $From The key in the Dictionary at which copying begins.
     * @param null|string $To   The key in the Dictionary at which copying ends.
     *
     * @return array An array containing the elements of the Dictionary.
     */
    public function ToArray(string $From = null, string $To = null): array {
        $FromIndex = 0;
        $ToIndex   = null;
        foreach(\array_keys($this->Elements) as $Index => $Key) {
            if($From === $Key) {
                $FromIndex = $Index;
            }
            if($To === $Key) {
                $ToIndex = $Index - $FromIndex;
            }
        }
        return \array_slice($this->Elements, $FromIndex ?? 0, $ToIndex ?? $this->Count, true);
    }
    
    /**
     * Determines whether an element is in the Dictionary.
     *
     * @param mixed $Element The element to check.
     *
     * @return bool True if the element is in the Dictionary, otherwise, false.
     */
    public function Contains(mixed $Element): bool {
        return \in_array($Element, $this->Elements);
    }
    
    /**
     * Determines whether an element with the specified key is in the Dictionary.
     *
     * @param string $Key The key to check.
     *
     * @return bool True if an element with the specified key is in the Dictionary, otherwise, false.
     */
    public function ContainsKey(string $Key): bool {
        return isset($this->Elements[$Key]);
    }
    
    /**
     * Removes all elements from the Dictionary.
     */
    public function Clear(): void {
        $this->Elements = [];
    }
    
    /**
     * Merges the elements of a different {@link \vDesk\Struct\Collections\Dictionary} into the Dictionary.
     *
     * @param \vDesk\Struct\Collections\Dictionary $Dictionary The Dictionary to merge from.
     *
     * @throws \InvalidArgumentException Thrown if the IMap to merge is not of the same type as the instance
     */
    public function Merge(IDictionary $Dictionary): void {
        foreach($Dictionary as $Key => $Value) {
            $this->Add($Key, $Value);
        }
    }
    
    /**
     * Replaces an element of the Dictionary with a different element.
     *
     * @param mixed $Element     The old value of the element to replace.
     * @param mixed $Replacement The element to set.
     */
    public function Replace(mixed $Element, mixed $Replacement): void {
        if(($Key = $this->KeyOf($Element)) !== null) {
            $this->Elements[$Key] = $Replacement;
        }
    }
    
    /**
     * Replaces the element with the specified key of the Dictionary with a specified element.
     *
     * @param string $Key     The key of the element to replace.
     * @param mixed  $Element The element to set.
     */
    public function ReplaceAt(string $Key, mixed $Element): void {
        if(isset($this->Elements[$Key])) {
            $this->Elements[$Key] = $Element;
        }
    }
    
    /**
     * Removes the specified element from the Dictionary.
     *
     * @param mixed $Element The element to remove.
     *
     * @return mixed The removed element.
     */
    public function Remove(mixed $Element): mixed {
        return $this->RemoveAt($this->KeyOf($Element) ?? "");
    }
    
    /**
     * Removes an element with the specified key.
     *
     * @param string $Key The key at which the element should be removed.
     *
     * @return mixed The removed element.
     */
    public function RemoveAt(string $Key): mixed {
        if(isset($this->Elements[$Key])) {
            $Value = $this->Elements[$Key];
            unset($this->Elements[$Key]);
            return $Value;
        }
        return null;
    }
    
    /**
     * Inserts an element into the Dictionary at the position of the element with the specified key.
     *
     * @param string $Before  The key of the element of which the new element will be inserted before.
     * @param string $Key     The key of the element to insert.
     * @param mixed  $Element The element to insert.
     *
     * @throws \vDesk\Struct\Collections\DuplicateKeyException Thrown if an element with an equal key already exists.
     */
    public function Insert(string $Before, string $Key, mixed $Element): void {
        
        if(isset($this->Elements[$Key])) {
            throw new DuplicateKeyException("An element with the same key '$Key' already exists.");
        }
        
        $Elements = [];
        
        foreach($this->Elements as $ExistingKey => $ExistingValue) {
            if($ExistingKey === $Before) {
                $Elements[$Key] = $Element;
            }
            $Elements[$ExistingKey] = $ExistingValue;
        }
        
        $this->Elements = $Elements;
        
    }
    
    /**
     * Inserts an element into the Dictionary after the position of the element with the specified key.
     *
     * @param string $After The key of the element of which the new element will be inserted after.
     * @param string $Key   The key of the element to insert.
     * @param mixed  $Value The element to insert.
     *
     * @throws \vDesk\Struct\Collections\DuplicateKeyException Thrown if an element with an equal key already exists.
     */
    public function InsertAfter(string $After, string $Key, mixed $Value): void {
        
        if(isset($this->Elements[$Key])) {
            throw new DuplicateKeyException("An element with the same key '$Key' already exists.");
        }
        
        $Elements = [];
        
        foreach($this->Elements as $ExistingKey => $ExistingValue) {
            $Elements[$ExistingKey] = $ExistingValue;
            if($ExistingKey === $After) {
                $Elements[$Key] = $Value;
            }
        }
        
        $this->Elements = $Elements;
        
    }
    
    /**
     * Sets the value of an existing key within the Dictionary.
     * Note: Adding new key/values to the Dictionary isn't supported, use {@see \vDesk\Struct\Collections\Dictionary::Add()} instead.
     *
     * @param mixed $Key   The key of the value to set.
     * @param mixed $Value The value to set.
     *
     * @ignore
     *
     * @see \ArrayAccess::offsetSet()
     */
    public function offsetSet($Key, $Value): void {
        if($this->offsetExists($Key)) {
            $this->ReplaceAt($Key, $Value);
        } else {
            $this->Add($Key, $Value);
        }
    }
    
    /**
     *
     * Determines whether an element with the specified key exists.
     *
     * @param string $Key The key to check for existence.
     *
     * @return bool True if the specified key exists; otherwise, false.
     * @see \ArrayAccess::offsetExists()
     * @ignore
     */
    public function offsetExists($Key): bool {
        return isset($this->Elements[$Key]);
    }
    
    /**
     * Unsets an element and its key from the Dictionary.
     * Note: Using 'unset()' to delete an element within the Dictionary isn't supported,
     * use {@see \vDesk\Struct\Collections\Dictionary::RemoveAt()} instead.
     *
     * @param mixed $Key The key of the element to unset.
     *
     * @throws \vDesk\Struct\InvalidOperationException Thrown if an element is being deleted using unset($Key).
     * @ignore
     *
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($Key): void {
        throw new InvalidOperationException("Cannot unset element at index " . __CLASS__ . "[$Key]. Use " . __CLASS__ . "::RemoveAt($Key) instead.");
    }
    
    /**
     * Returns the element of the specified key.
     *
     * @param mixed $Key The string-based key of the element to get.
     *
     * @return mixed The element with the specified key.
     * @throws \vDesk\Struct\Collections\KeyNotFoundException Thrown if the specified key doesn't exist.
     *
     * @ignore
     *
     * @see \ArrayAccess::offsetGet()
     */
    public function offsetGet($Key) {
        if(!$this->offsetExists($Key)) {
            throw new KeyNotFoundException("Undefined index at " . __CLASS__ . "[$Key].");
        }
        return $this->Elements[$Key];
    }
    
    /**
     * Rewinds the internal pointer of the Dictionary to the start.
     *
     * @see \Iterator::rewind()
     * @ignore
     */
    public function rewind(): void {
        \reset($this->Elements);
    }
    
    /**
     *
     * Returns the element at the current position of the internal pointer of the Dictionary.
     *
     * @return mixed The element at the current position.
     * @ignore
     *
     * @see \Iterator::current()
     */
    public function current() {
        return \current($this->Elements);
    }
    
    /**
     * @inheritdoc
     *
     * @see \Iterator::key()
     * @ignore
     */
    public function key(): string {
        return \key($this->Elements);
    }
    
    /**
     * @inheritdoc
     *
     * @see \Iterator::next()
     * @ignore
     */
    public function next(): void {
        \next($this->Elements);
    }
    
    /**
     * @inheritdoc
     *
     * @see \Iterator::valid()
     * @ignore
     */
    public function valid(): bool {
        return \key($this->Elements) !== null;
    }
    
    /**
     * Returns the amount of elements inside the Dictionary.
     *
     * @return int The amount of elements in the Dictionary.
     * @see \vDesk\Struct\Collections\IEnumerable::Count()
     */
    public function Count(): int {
        return \count($this->Elements);
    }
    
    /**
     * Searches for an element inside the Dictionary and returns the first element which satisfies a test provided by the specified
     * predicate function.
     *
     * @param callable $Predicate A predicate function to execute on each element inside the Dictionary.
     *
     * @return mixed Returns the first matching element in the Dictionary, null if no element satisfies a test provided by the
     *                    specified predicate function.
     * @see   \vDesk\Struct\Collections\IEnumerable::Find()
     * <code>
     * //Example of using a predicate function for searching the Dictionary.
     * $Dictionary->Find(fn(T $Value, string $Key, Dictionary $Dictionary) => $Key === "Foo" && $Value->ID === 12);
     * //Returns the first element with with key "Foo" and $Value->ID = 12 if exists.
     * </code>
     */
    public function Find(callable $Predicate): mixed {
        foreach($this as $Key => $Value) {
            if($Predicate($Value, $Key, $this)) {
                return $Value;
            }
        }
        return null;
    }
    
    /**
     * Sorts the Dictionary by value.
     *
     * @param callable $Predicate The function to determine the sortorder. It should return either a positive, negative or zero-value.
     *
     * @return bool True if the sorting was successful.
     * @see         \vDesk\Struct\Collections\IEnumerable::Sort()
     *              <code>
     *              //Example of using callbackfunctions for sorting the Dictionary.
     *              $Dictionary->Sort(function(T $a, T $b): int {
     *              return $a->ID - $b->ID;
     *              });
     *              //Sorts the Dictionary ascending by the value of the elements ID-property.
     *              </code>
     *
     */
    public function Sort(callable $Predicate): bool {
        return \usort($this->Elements, $Predicate);
    }
    
    /**
     * Returns a new Dictionary containing all elements that satisfy a test provided by the specified predicate function.
     *
     * @param callable $Predicate A predicate function to execute on each element inside the Dictionary.
     *
     * @return \vDesk\Struct\Collections\Dictionary The elements inside the Dictionary which are matching the searchcriteria.
     * @see   \vDesk\Struct\Collections\IEnumerable::Filter()
     * <code>
     * //Example of using callbackfunctions.
     * $Dictionary->Filter(fn(T $Value, string $Key, Dictionary $Dictionary): bool => $Value->ID > 1 && $Value->ID < 6);
     * //Returns all elements whose value of $Value-ID is in the range betweeen 1 and 6.
     * </code>
     */
    public function Filter(callable $Predicate): IEnumerable {
        $Dictionary = new static();
        foreach($this as $Key => $Value) {
            if($Predicate($Value, $Key, $this)) {
                $Dictionary->Add($Key, $Value);
            }
        }
        return $Dictionary;
    }
    
    /**
     * Creates creates a new Dictionary with the results of calling a function for every element in the Dictionary.
     *
     * @param callable $Predicate The callback function to apply on each element.
     *
     * @return \vDesk\Struct\Collections\Dictionary The Dictionary holding the result of each function call.
     * @see \vDesk\Struct\Collections\IEnumerable::Map()
     */
    public function Map(callable $Predicate): IEnumerable {
        $Dictionary = new static();
        foreach($this as $Key => $Value) {
            $Dictionary->Add($Key, $Predicate($Value, $Key, $this));
        }
        return $Dictionary;
    }
    
    /**
     * Reduces the values of the Dictionary to a single value.
     *
     * @param callable $Predicate    The callback function to apply on each element inside the Dictionary.
     * @param mixed    $InitialValue Value to use as the first argument to the first call of the $Predicate. If no initial value is
     *                               supplied, the first element in the Dictionary will be used.
     *
     * @return mixed The value that results from the reduction.
     * @see \vDesk\Struct\Collections\IEnumerable::Reduce()
     */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed {
        $Accumulator = $InitialValue ?? \reset($this->Elements);
        foreach($this as $Key => $Value) {
            $Accumulator = $Predicate($Accumulator, $Value, $Key, $this);
        }
        return $Accumulator;
    }
    
    /**
     * Determines whether any element of a sequence satisfies a condition.
     *
     * @param callable $Predicate The callback function to apply on each element inside the Dictionary.
     *
     * @return bool True if at least one element inside the Dictionary matches the predicate.
     */
    public function Any(callable $Predicate): bool {
        foreach($this as $Key => $Value) {
            if($Predicate($Value, $Key, $this)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Determines whether all elements of a sequence satisfy a condition.
     *
     * @param callable $Predicate A function to test each element for a condition.
     *
     * @return bool True if every element of the sequence passes the test in the specified predicate, or if the sequence is empty;
     *              otherwise, false.
     */
    public function Every(callable $Predicate): bool {
        foreach($this as $Key => $Value) {
            if(!$Predicate($Value, $Key, $this)) {
                return false;
            }
        }
        return true;
    }
}
