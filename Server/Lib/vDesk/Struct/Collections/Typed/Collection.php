<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections\Typed;

use vDesk\Struct\Collections\ICollection;
use vDesk\Struct\Collections\IEnumerable;
use vDesk\Struct\Collections\IndexOutOfRangeException;
use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a statically typed iterable list of elements.
 *
 * @property-read int $Count Gets the amount of elements in the Collection.
 *
 * @package vDesk\Struct\Collections\Typed
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Collection implements ICollection {
    
    use Properties;
    
    /**
     * The Type of the Collection.
     */
    public const Type = Type::Mixed;
    
    /**
     * The elements of the Collection.
     * @var array
     */
    protected array $Elements = [];
    
    /**
     * Initializes a new instance of the Collection class.
     *
     * @param iterable $Elements Initializes the Collection with the specified set of elements.
     */
    public function __construct(iterable $Elements = []) {
        $this->AddProperty("Count", [\Get => fn(): int => \count($this->Elements)]);
        foreach($Elements as $Element){
            $this->Add($Element);
        }
    }
    
    /**
     * Determines whether a passed argument or returned value is of the specified type of the Collection.
     *
     * @param mixed $Argument The argument to validate against the specified type of the Collection.
     *
     * @return bool True if the passed argument matches the specified type of the Collection; otherwise, false.
     */
    public static function IsValid(mixed &$Argument): bool {
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
    protected static function TypeError(int $ArgumentPosition, string $Method, mixed &$Argument): \TypeError {
        return Type::IsScalar($Argument)
            ? new \TypeError("Argument {$ArgumentPosition} passed to {$Method} must be of the type " . static::Type . ", " . Type::Of($Argument) . " given")
            : new \TypeError("Argument {$ArgumentPosition} passed to {$Method} must be an instance of " . static::Type . ", instance of " . Type::Of($Argument) . " given");
    }
    
    /**
     * Adds an element to the Collection.
     *
     * @param mixed $Element The element to add.
     *
     * @see \vDesk\Struct\Collections\ICollection::Add()
     *
     */
    public function Add(mixed $Element): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        $this->Elements[] = $Element;
    }
    
    /**
     * Replaces an element of the Collection with a different element.
     * If the element to replace doesn't exist in the Collection, nothing is replaced nor added.
     *
     * @param mixed $Element     The element to replace.
     * @param mixed $Replacement The new element to set.
     *
     * @see \vDesk\Struct\Collections\ICollection::Replace()
     */
    public function Replace(mixed $Element, mixed $Replacement): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        if(!self::IsValid($Replacement)) {
            throw self::TypeError(2, __METHOD__, $Replacement);
        }
        if(~($iIndex = $this->IndexOf($Element))) {
            $this->Elements[$iIndex] = $Replacement;
        }
    }
    
    /**
     * Inserts an element into the Collection at the specified index.
     *
     * @param int   $Index   The zero-based index at which the element should be inserted.
     * @param mixed $Element The element to insert.
     *
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     * @see \vDesk\Struct\Collections\ICollection::Insert()
     */
    public function Insert(int $Index, mixed $Element): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        if(
            $Index < 0
            || $Index > ($Count = $this->Count())
            || !$this->offsetExists($Index)
        ) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        
        if($Index === $Count) {
            $this->Add($Element);
        } else {
            $Temp             = \array_splice($this->Elements, $Index);
            $this->Elements[] = $Element;
            \array_merge($this->Elements, $Temp);
            $this->Elements = \array_values($this->Elements);
        }
    }
    
    /**
     * Inserts an element into the Collection after the element at the specified index.
     *
     * @param int   $Index   The zero-based index after which the element should be inserted.
     * @param mixed $Element The element to insert.
     */
    public function InsertAfter(int $Index, mixed $Element): void {
        $this->Insert(++$Index, $Element);
    }
    
    /**
     * Replaces the element at the specified index.
     *
     * @param mixed $Index   The index of the element to replace.
     * @param mixed $Element The new value for the element at the specified index.
     *
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     */
    public function ReplaceAt(int $Index, mixed $Element): void {
        if(!self::IsValid($Element)) {
            throw self::TypeError(2, __METHOD__, $Element);
        }
        if(!$this->offsetExists($Index)) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        $this->Elements[$Index] = $Element;
    }
    
    /**
     * Returns the number of elements in the Collection.
     *
     * @return int The number of elements inside the Collection.
     * @see \vDesk\Struct\Collections\IEnumerable::Count()
     */
    public function Count(): int {
        return \count($this->Elements);
    }
    
    /**
     * Determines whether an element at the specified index exists.
     *
     * @param int $Index The index to check for existence.
     *
     * @return bool True if the specified index exists; otherwise, false.
     * @throws \TypeError Thrown if the specified index is not an integer.
     *
     * @see \ArrayAccess::offsetExists()
     */
    public function offsetExists($Index): bool {
        if(!\is_int($Index)) {
            throw new \TypeError("Argument 1 passed to " . __METHOD__ . " must be of the type int, " . Type::Of($Index) . " given");
        }
        return isset($this->Elements[$Index]);
    }
    
    /**
     * Searches for an element inside the Collection and returns the first element which satisfies a test provided by the specified
     * predicate function.
     *
     * @param callable $Predicate A predicate function to execute on each element inside the Collection.
     *
     * @return mixed Returns the first matching element in the Collection, null if no element satisfies a test provided by the
     *                    specified predicate function.
     * @see   \vDesk\Struct\Collections\IEnumerable::Find()
     * <code>
     * //Example of using a predicate function for searching the Collection.
     * $Collection->Find(fn(T $Value, int $Index, Collection $Collection): bool => $Value->ID === 12 && $Value->Name === "Foo");
     * //Returns the first element with $Element->ID = 12 and $Element->Name = "Foo" if exists.
     * </code>
     */
    public function Find(callable $Predicate): mixed {
        foreach($this as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return $Value;
            }
        }
        return null;
    }
    
    /**
     * Sorts the Collection by value.
     *
     * @param Callable $Predicate The function to determine the sortorder. It should return either a positive, negative or zero-value.
     *
     * @return Bool True if the sorting was successful.
     * @see \vDesk\Struct\Collections\IEnumerable::Sort()
     * <code>
     * //Example of using callbackfunctions for sorting the collection.
     * $Collection->Sort(fn($A, $B) => $A->ID <=> $B->ID);
     * //Sorts the Collection ascending by the value of the elements ID-property.
     * </code>
     */
    public function Sort(callable $Predicate): bool {
        return \usort($this->Elements, $Predicate);
    }
    
    /**
     * Returns a new Collection containing all elements that satisfy a test provided by the specified predicate function.
     *
     * @param callable $Predicate A predicate function to execute on each element inside the Collection.
     *
     * @return \vDesk\Struct\Collections\Typed\Collection The elements inside the Collection which are matching the searchcriteria.
     * @see   \vDesk\Struct\Collections\IEnumerable::Filter()
     * <code>
     * //Example of using callbackfunctions.
     * $Collection->Filter(fn(T $Value, int $Index, Collection $Collection): bool => $Value->ID > 1 && $Value->ID < 6);
     * //Returns all elements whose value of $Value->ID is in the range betweeen 1 and 6.
     * </code>
     */
    public function Filter(callable $Predicate): IEnumerable {
        $Collection = new static();
        foreach($this as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                $Collection->Add($Value);
            }
        }
        return $Collection;
    }
    
    /**
     * Creates a new Collection with the results of calling a function for every Collection element.
     *
     * @param callable $Predicate The function to apply on each element.
     *
     * @return \vDesk\Struct\Collections\Typed\Collection The Collection holding the result of each function call.
     * @see \vDesk\Struct\Collections\IEnumerable::Map()
     */
    public function Map(callable $Predicate): IEnumerable {
        $Collection = new static();
        foreach($this as $Index => $Value) {
            $Collection->Add($Predicate($Value, $Index, $this));
        }
        return $Collection;
    }
    
    /**
     * Reduces the values of the Collection to a single value.
     *
     * @param callable $Predicate    The callback function to apply on each element inside the Collection.
     * @param mixed    $InitialValue Value to use as the first argument to the first call of the $Predicate. If no initial value is
     *                               supplied, the first element in the Collection will be used.
     *
     * @return mixed The value that results from the reduction.
     * @see \vDesk\Struct\Collections\IEnumerable::Reduce()
     */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed {
        $Accumulator = $InitialValue ?? \reset($this->Elements);
        foreach($this as $Index => $Value) {
            $Accumulator = $Predicate($Accumulator, $Value, $Index, $this);
        }
        return $Accumulator;
    }
    
    /**
     * Unsets an element and its index from the Collection.
     * Note: Using 'unset()' to delete an element within the Collection isn't supported,
     * use {@see \vDesk\Struct\Collections\Typed\Collection::RemoveAt()} instead.
     *
     * @param int $Index The index of the element to unset.
     *
     * @throws \vDesk\Struct\InvalidOperationException Thrown if an element is being deleted using unset($Index).
     *
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($Index): void {
        throw new InvalidOperationException("Cannot unset element at index " . __CLASS__ . "[$Index]. Use " . __CLASS__ . "::RemoveAt($Index) instead.");
    }
    
    /**
     * Returns the element at the specified index.
     *
     * @param int $Index The zero-based index of the element to get.
     *
     * @return mixed The element at the specified index.
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     *
     * @see \ArrayAccess::offsetGet()
     */
    public function offsetGet($Index) {
        if(!$this->offsetExists($Index)) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        return $this->Elements[$Index];
    }
    
    /**
     * @see \Iterator::rewind()
     * @ignore
     */
    public function rewind(): void {
        \reset($this->Elements);
    }
    
    /**
     * Returns the index of an element.
     *
     * @param mixed $Element The element to seek.
     *
     * @return int The index of the element. -1 if the element is not in the collection.
     * @see \vDesk\Struct\Collections\ICollection::IndexOf()
     */
    public function IndexOf(mixed $Element): int {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        foreach($this->Elements as $Index => $InternalElement) {
            if($Element === $InternalElement) {
                return $Index;
            }
        }
        return -1;
    }
    
    /**
     * @return mixed
     * @ignore
     *
     * @see \Iterator::current()
     */
    public function current() {
        return \current($this->Elements);
    }
    
    /**
     * Determines whether any element of a sequence satisfies a condition.
     *
     * @param callable $Predicate The callback function to apply on each element inside the Collection.
     *
     * @return bool True if at least one element inside the Collection matches the predicate.
     * @see \vDesk\Struct\Collections\IEnumerable::Any()
     */
    public function Any(callable $Predicate): bool {
        foreach($this as $Index => $Value) {
            if($Predicate($Value, $Index, $this)) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @return int
     * @ignore
     *
     * @see \Iterator::key()
     */
    public function key(): int {
        return \key($this->Elements);
    }
    
    /**
     * Removes the specified element from the collection.
     *
     * @param mixed $Element The element to remove.
     *
     * @return mixed The removed element.
     * @see \vDesk\Struct\Collections\ICollection::Remove()
     */
    public function Remove(mixed $Element): mixed {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        $Value          = \array_splice($this->Elements, $this->IndexOf($Element), 1)[0];
        $this->Elements = \array_values($this->Elements);
        return $Value;
    }
    
    /**
     * @see \Iterator::next()
     * @ignore
     */
    public function next(): void {
        \next($this->Elements);
    }
    
    /**
     * Determines whether all elements of a sequence satisfy a condition.
     *
     * @param callable $Predicate A function to test each element for a condition.
     *
     * @return bool True if every element of the sequence passes the test in the specified predicate, or if the sequence is empty;
     *              otherwise, false.
     * @see \vDesk\Struct\Collections\IEnumerable::Every()
     */
    public function Every(callable $Predicate): bool {
        foreach($this as $Index => $Value) {
            if(!$Predicate($Value, $Index, $this)) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * @return bool
     * @ignore
     *
     * @see \Iterator::valid()
     */
    public function valid(): bool {
        return \key($this->Elements) !== null;
    }
    
    /**
     * Removes an element at the specified index.
     *
     * @param int $Index The index at which the element should be removed.
     *
     * @return mixed The removed element.
     * @see \vDesk\Struct\Collections\ICollection::RemoveAt()
     */
    public function RemoveAt(int $Index): mixed {
        if(isset($this->Elements[$Index])) {
            $Value          = \array_splice($this->Elements, $Index, 1)[0];
            $this->Elements = \array_values($this->Elements);
            return $Value;
        }
        return null;
    }
    
    /**
     * Copies the elements of the Collection into an array.
     *
     * @param null|int $From The index in ICollection at which copying begins.
     * @param null|int $To   The index in ICollection at which copying ends.
     *
     * @return array An array containing the elements of the Collection.
     * @see \vDesk\Struct\Collections\ICollection::ToArray()
     */
    public function ToArray(int $From = null, int $To = null): array {
        return \array_slice($this->Elements, $From ?? 0, ($To ?? $this->Count) - $From);
    }
    
    /**
     * Determines whether an element is in the Collection.
     *
     * @param mixed $Element The element to check.
     *
     * @return bool True if the element is in the collection, otherwise, false.
     * @see \vDesk\Struct\Collections\ICollection::Contains()
     *
     */
    public function Contains(mixed $Element): bool {
        if(!self::IsValid($Element)) {
            throw self::TypeError(1, __METHOD__, $Element);
        }
        return \in_array($Element, $this->Elements);
    }
    
    /**
     * Removes all elements from the Collection.
     *
     * @see \vDesk\Struct\Collections\ICollection::Clear()
     */
    public function Clear(): void {
        \array_splice($this->Elements, 0);
    }
    
    /**
     * Merges the objects of a different {@link \vDesk\Struct\Collections\Typed\Collection} into the Collection.
     *
     * @param \vDesk\Struct\Collections\ICollection $Collection The Collection to merge from.
     *
     * @see \vDesk\Struct\Collections\ICollection::Merge()
     */
    public function Merge(ICollection $Collection): void {
        foreach($Collection as $Item) {
            $this->Add($Item);
        }
    }
    
    /**
     * Sets the value at an existing index within the Collection.
     *
     * @param int   $Index The index of the value to set.
     * @param mixed $Value The value to set.
     *
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     * @ignore
     *
     * @see \ArrayAccess::offsetSet()
     */
    public function offsetSet($Index, $Value): void {
        // Check if an index has been specified.
        if($Index !== null) {
            // Check if the index is in range.
            if(!$this->offsetExists($Index)) {
                throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
            }
            $this->Replace($Index, $Value);
        } else {
            $this->Add($Value);
        }
    }
}