<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections\Typed;

use vDesk\Struct\Collections\DuplicateKeyException;
use vDesk\Struct\Collections\KeyNotFoundException;
use vDesk\Struct\Collections\IEnumerable;
use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a statically typed iterable list of key-value pairs.
 *
 * @property-read int      $Count  Gets the amount of elements in the Dictionary.
 * @property-read string[] $Keys   Gets all keys of the Dictionary
 * @property-read mixed[]  $Values Gets all values of the Dictionary
 * @package vDesk\Struct
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
abstract class Dictionary implements IEnumerable, \ArrayAccess, \Iterator {

    use Properties;

    /**
     * The Type of the value of the Dictionary.
     */
    public const Type = Type::Mixed;

    /**
     * The elements of the Dictionary.
     *
     * @var mixed[]
     */
    protected array $Elements = [];

    /**
     * Initializes a new instance of the Dictionary class.
     *
     * @param iterable|null $Elements Initializes the Dictionary with the specified set of elements.
     */
    public function __construct(?iterable $Elements = []) {
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
        foreach($Elements ?? [] as $Key => $Value) {
            $this->Add($Key, $Value);
        }
    }

    /**
     * Determines whether a passed argument or returned value is of the specified value-type of the Dictionary.
     *
     * @param mixed $Argument The argument to validate against the specified value-type of the Dictionary.
     *
     * @return bool True if the passed argument matches the specified value-type of the Dictionary; otherwise, false.
     */
    public static function IsValid(&$Argument): bool {
        return static::Type === Type::Of($Argument);
    }

    /**
     * Creates a new TypeError indicating a wrong argument type.
     *
     * @param int    $ArgumentPosition The position of the argument which type doesn't match.
     * @param string $Method           The method which has been called within the type mismatch has occurred.
     * @param mixed  $Argument         The mismatching argument.
     *
     * @return \TypeError A pre-filled TypeError indicating a wrong argument type.
     */
    protected static function TypeError(int $ArgumentPosition, string $Method, &$Argument): \TypeError {
        return Type::IsScalar($Argument)
            ? new \TypeError("Argument {$ArgumentPosition} passed to {$Method} must be of the type " . static::Type . ", " . Type::Of($Argument) . " given")
            : new \TypeError("Argument {$ArgumentPosition} passed to {$Method} must be an instance of " . static::Type . ", instance of " . Type::Of($Argument) . " given");
    }

    /**
     * Adds an element to the Dictionary.
     *
     * @param string $Key     The key of the element it should be accessible.
     * @param mixed  $Element The element to add.
     *
     * @throws \vDesk\Struct\Collections\DuplicateKeyException Thrown if an element with an equal key already exists.
     */
    public function Add(string $Key, $Element): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(2, __METHOD__, $Element);
        }
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
    public function ChangeKey($Element, string $Key): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        if(($OldKey = $this->KeyOf($Element)) !== null) {
            $this->Elements[$Key] = $this->Elements[$OldKey];
            unset($this->Elements[$OldKey]);
        }
    }

    /**
     * Searches for the specified element and returns the key of the first occurrence within the entire Dictionary.
     *
     * @param mixed $Element The element to locate in the Dictionary.
     *
     * @return string|null The key of the first occurrence of the element within the entire Dictionary, if found; otherwise, null.
     */
    public function KeyOf($Element): ?string {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        foreach($this->Elements as $mKey => $mValue) {
            if($Element === $mValue) {
                return $mKey;
            }
        }
        return null;
    }

    /**
     * Copies the elements of the Dictionary into an array.
     *
     * @param string $From The key in the Dictionary at which copying begins.
     * @param string $To   The key in the Dictionary at which copying ends.
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
    public function Contains($Element): bool {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        return \in_array($Element, $this->Elements);
    }

    /**
     * Determines whether an element with the specified key is in the Dictionary.
     *
     * @param mixed $Key The key to check.
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
     * Merges the elements of a different {@link \vDesk\Struct\Collections\Typed\Dictionary} into the Dictionary.
     *
     * @param \vDesk\Struct\Collections\Typed\Dictionary $Dictionary The Dictionary to merge.
     */
    public function Merge(Dictionary $Dictionary): void {
        foreach($Dictionary as $mKey => $mValue) {
            $this->Add($mKey, $mValue);
        }
    }

    /**
     * Replaces an element of the Dictionary with a different element.
     * If the element to replace doesn't exist in the Dictionary, nothing is replaced nor added.
     *
     * @param mixed $Element     The old value of the element to replace.
     * @param mixed $Replacement The element to set.
     */
    public function Replace($Element, $Replacement): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        if(!self::IsValid($Replacement)) {
            throw self::TypeError(2, __METHOD__, $Replacement);
        }
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
    public function ReplaceAt(string $Key, $Element): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(2, __METHOD__, $Element);
        }
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
    public function Remove($Element) {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        return $this->RemoveAt($this->KeyOf($Element) ?? "");
    }

    /**
     * Removes an element with the specified key.
     *
     * @param string $Key The key at which the element should be removed.
     *
     * @return mixed The removed element.
     */
    public function RemoveAt(string $Key) {
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
     * @param string $Before The key of the element of which the new element will be inserted before.
     * @param string $Key    The key of the element to insert.
     * @param mixed  $Value  The value to insert.
     *
     * @throws \vDesk\Struct\Collections\DuplicateKeyException Thrown if an element with an equal key already exists.
     */
    public function Insert(string $Before, string $Key, $Value): void {

        if(!self::IsValid($Value)) {
            throw self::TypeError(3, __METHOD__, $Value);
        }

        if(isset($this->Elements[$Key])) {
            throw new DuplicateKeyException("An element with the same key '$Key' already exists.");
        }

        $Elements = [];

        foreach($this->Elements as $ExistingKey => $ExistingValue) {
            if($ExistingKey === $Before) {
                $Elements[$Key] = $Value;
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
    public function InsertAfter(string $After, string $Key, $Value): void {
        if(!self::IsValid($Value)) {
            throw self::TypeError(3, __METHOD__, $Value);
        }
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
     *
     * @param mixed $Key   The key of the value to set.
     * @param mixed $Value The value to set.
     *
     * @see \ArrayAccess::offsetSet()
     * @ignore
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
     * use {@see \vDesk\Struct\Collections\Typed\Dictionary::RemoveAt()} instead.
     *
     * @param mixed $Key The key of the element to unset.
     *
     * @throws \vDesk\Struct\InvalidOperationException Thrown if an element is being deleted using unset($Key).
     * @ignore
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($Key): void {
        throw new InvalidOperationException("Cannot unset element at index " . static::class . "[$Key]. Use " . static::class . "::RemoveAt($Key) instead.");
    }

    /**
     * Returns the element of the specified key.
     *
     * @param mixed $Key The string-based key of the element to get.
     *
     * @throws \vDesk\Struct\Collections\KeyNotFoundException Thrown if the specified key doesn't exist.
     *
     * @return mixed The element with the specified key.
     * @ignore
     *
     * @see \ArrayAccess::offsetGet()
     */
    public function offsetGet($Key) {
        if(!$this->offsetExists($Key)) {
            throw new KeyNotFoundException("Undefined key at " . static::class . "[$Key].");
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
     */
    public function Count(): int {
        return \count($this->Elements);
    }

    /**
     * Searches for an element inside the Dictionary and returns the first element which satisfies a test provided by the
     * specified predicate function.
     *
     * @param callable $Predicate A predicate function to execute on each element inside the Dictionary.
     *
     * @return mixed|null Returns the first matching element in the Dictionary, null if no element satisfies a test provided by the
     *                    specified predicate function.
     *  <code>
     *  //Example of using a predicate function for searching the Dictionary.
     *  $Dictionary->Find(fn(T $Value, string $Key, Dictionary $Dictionary) => $Value->ID === 12 && $Value->Name === "Foo");
     *  //Returns the first element with $Value->ID = 12 and $Value->Name = "Foo" if exists.
     *  </code>
     */
    public function Find(callable $Predicate) {
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
     * <code>
     * //Example of using callback functions for sorting the Dictionary.
     * $Dictionary->Sort(function(T $A, T $B): int => $A->ID - $B->ID);
     * //Sorts the Dictionary ascending by the value of the elements ID-property.
     * </code>
     */
    public function Sort(callable $Predicate): bool {
        return \usort($this->Elements, $Predicate);
    }

    /**
     * Returns a new Dictionary containing all elements that satisfy a test provided by the specified predicate function.
     *
     * @param callable $Predicate A predicate function to execute on each element inside the Dictionary.
     *
     * @return \vDesk\Struct\Collections\Typed\Dictionary The elements inside the Dictionary which are matching
     *                                                                 the search criteria.
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
     * Creates creates a new Dictionary with the results of calling a function for every element in the
     * Dictionary.
     *
     * @param callable $Predicate The callback function to apply on each element.
     *
     * @return \vDesk\Struct\Collections\Typed\Dictionary The Dictionary holding the result of each function
     *                                                                 call.
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
     */
    public function Reduce(callable $Predicate, $InitialValue = null) {
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