<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections;

use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a generic iterable list of objects.
 *
 * @property-read int $Count Gets the amount of elements in the Collection.
 *
 * @package vDesk\Struct\Collections
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Collection implements ICollection {
    
    use Properties;
    
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
     * Adds an element to the Collection.
     *
     * @param mixed $Element The element to add.
     *
     * @see \vDesk\Struct\Collections\ICollection::Add()
     *
     */
    public function Add(mixed $Element): void {
        $this->Elements[] = $Element;
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
        if($Index < 0 || $Index > ($iCount = $this->Count())) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        
        if($Index === $iCount) {
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
     * Replaces an element of the ICollection with a different element.
     * If the element to replace doesn't exist in the Collection, nothing is replaced nor added.
     *
     * @param mixed $Element     The element to replace.
     * @param mixed $Replacement The new element to set.
     *
     * @see \vDesk\Struct\Collections\ICollection::Replace()
     */
    public function Replace(mixed $Element, mixed $Replacement): void {
        if(~($iIndex = $this->IndexOf($Element))) {
            $this->Elements[$iIndex] = $Replacement;
        }
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
        if(!$this->offsetExists($Index)) {
            throw new IndexOutOfRangeException("Undefined index at " . __CLASS__ . "[$Index].");
        }
        $this->Elements[$Index] = $Element;
    }
    
    /**
     * Removes the specified element from the Collection.
     *
     * @param mixed $Element The element to remove.
     *
     * @return mixed The removed element.
     * @see \vDesk\Struct\Collections\ICollection::Remove()
     */
    public function Remove(mixed $Element): mixed {
        $Value          = \array_splice($this->Elements, $this->IndexOf($Element), 1)[0];
        $this->Elements = \array_values($this->Elements);
        return $Value;
    }
    
    /**
     * Determines whether any element of a sequence satisfies a condition.
     *
     * @param callable $Predicate The callback function to apply on each element inside the ICollection.
     *
     * @return bool True if at least one element inside the ICollection matches the predicate.
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
     * Removes all elements from the Collection.
     *
     * @see \vDesk\Struct\Collections\ICollection::Clear()
     */
    public function Clear(): void {
        $this->Elements = [];
    }
    
    /**
     * Determines whether an element is in the Collection.
     *
     * @param mixed $Element The element to check.
     *
     * @return bool True if the element is in the collection, otherwise, false.
     * @see \vDesk\Struct\Collections\ICollection::Contains()
     */
    public function Contains(mixed $Element): bool {
        return \in_array($Element, $this->Elements);
    }
    
    /**
     * Determines whether an element at the specified index exists.
     *
     * @param int $Index The index to check for existence.
     *
     * @return bool True if the specified index exists; otherwise, false.
     * @throws \TypeError Thrown if the specified index is not an integer.
     *
     * @ignore
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
     * Unsets an element and its index from the Collection.
     * Note: Using 'unset()' to delete an element within the Collection isn't supported, use {@see \vDesk\Struct\Collections\Collection::RemoveAt()}
     * instead.
     *
     * @param int $Index The index of the element to unset.
     *
     * @throws \vDesk\Struct\InvalidOperationException Thrown if an element is being deleted using unset($Index).
     * @ignore
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
     * $Collection->Find(fn(T $Value, int $Index, Collection $Collection) => $Value->ID === 12 && $Value->Name === "Foo");
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
     * @see \Iterator::rewind()
     * @ignore
     */
    public function rewind(): void {
        \reset($this->Elements);
    }
    
    /**
     * Sorts the Collection by value.
     *
     * @param Callable $Predicate The function to determine the sort order. It should return either a positive, negative or zero-value.
     *
     * @return Bool True if the sorting was successful.
     * @see         \vDesk\Struct\Collections\IEnumerable::Sort()
     * <code>
     * //Example of using callback functions for sorting the collection.
     * $Collection->Sort(fn($a, $b) => $a->ID <=> $b->ID);
     * //Sorts the collection ascending by the value of the elements ID-property.
     * </code>
     */
    public function Sort(callable $Predicate): bool {
        return \usort($this->Elements, $Predicate);
    }
    
    /**
     *
     * @return mixed
     * @ignore
     *
     * @see \Iterator::current()
     */
    public function current(): mixed {
        return \current($this->Elements);
    }
    
    /**
     * Returns a new Collection containing all elements that satisfy a test provided by the specified predicate function.
     *
     * @param callable $Predicate A predicate function to execute on each element inside the Collection.
     *
     * @return \vDesk\Struct\Collections\Collection The elements inside the Collection which are matching the searchcriteria.
     * @see   \vDesk\Struct\Collections\IEnumerable::Filter()
     * <code>
     * //Example of using callback functions.
     * $Collection->Filter(fn(T $Value, int $Index, Collection $Collection): bool => $Value->ID > 1 && $Value->ID < 6);
     * //Returns all elements whose value of $Value->ID is in the range between 1 and 6.
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
     * @return int
     * @ignore
     *
     * @see \Iterator::key()
     */
    public function key(): int {
        return \key($this->Elements);
    }
    
    /**
     * Creates a new Collection with the results of calling a function for every Collection element.
     *
     * @param callable $Predicate The function to apply on each element.
     *
     * @return \vDesk\Struct\Collections\Collection The Collection holding the result of each function call.
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
     * @see \Iterator::next()
     * @ignore
     */
    public function next(): void {
        \next($this->Elements);
    }
    
    /**
     * Reduces the values of the Collection to a single value.
     *
     * @param callable $Predicate    The callback function to apply on each element inside the Collection.
     * @param mixed    $InitialValue Value to use as the first argument to the first call of the $Predicate. If no initial value is
     *                               supplied, the first element in the collection will be used.
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
     * @return bool
     * @ignore
     *
     * @see \Iterator::valid()
     */
    public function valid(): bool {
        return \key($this->Elements) !== null;
    }
    
    /**
     * Tests if every element in the Collection passes the test implemented by the specified predicate.
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
     * Returns the index of an element.
     *
     * @param mixed $Element The element to seek.
     *
     * @return int The index of the element. -1 if the element is not in the collection.
     * @see \vDesk\Struct\Collections\ICollection::IndexOf()
     */
    public function IndexOf(mixed $Element): int {
        foreach($this->Elements as $Index => $InternalElement) {
            if($Element === $InternalElement) {
                return $Index;
            }
        }
        return -1;
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
     * @param int $From The index in ICollection at which copying begins.
     * @param int $To   The index in ICollection at which copying ends.
     *
     * @return array An array containing the elements of the Collection.
     * @see \vDesk\Struct\Collections\ICollection::ToArray()
     */
    public function ToArray(int $From = null, int $To = null): array {
        return \array_slice($this->Elements, $From ?? 0, ($To !== null) ? ($To - $From) : $this->Count);
    }
    
    /**
     * Merges the objects of a different {@link \vDesk\Struct\Collections\Collection} into the Collection.
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
     * Note: Adding new values to the Collection isn't supported,
     * use {@see \vDesk\Struct\Collections\Collection::Add()} instead.
     *
     * @param int   $Index The index of the value to set.
     * @param mixed $Value The value to set.
     *
     * @throws \vDesk\Struct\Collections\IndexOutOfRangeException Thrown if the specified index doesn't exist.
     *
     * @see \ArrayAccess::offsetSet()
     * @ignore
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
    
    /**
     * Returns the number of elements in the Collection.
     *
     * @return int The number of elements inside the Collection.
     * @see \vDesk\Struct\Collections\IEnumerable::Count()
     */
    public function Count(): int {
        return \count($this->Elements);
    }
}
