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
 * @property-read array    $Values Gets all values of the Dictionary
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
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
            "Count"  => [\Get => fn(): int => \count($this->Elements)],
            "Keys"   => [\Get => fn(): array => \array_keys($this->Elements)],
            "Values" => [\Get => fn(): array => \array_values($this->Elements)]
        ]);
        foreach($Elements as $Key => $Element) {
            $this->Add($Key, $Element);
        }
    }

    //Implementation of \vDesk\Struct\Collections\IDictionary.
    /**
     * @inheritDoc
     * @throws \vDesk\Struct\Collections\DuplicateKeyException Thrown if an element with an equal key already exists.
     */
    public function Add(string $Key, mixed $Element): void {
        if(isset($this->Elements[$Key])) {
            throw new DuplicateKeyException("An element with the same key '$Key' already exists.");
        }
        $this->Elements[$Key] = $Element;
    }

    /**
     * @inheritDoc
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
     * @inheritDoc
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

    /** @inheritDoc */
    public function Replace(mixed $Element, mixed $Replacement): void {
        if(($Key = $this->KeyOf($Element)) !== null) {
            $this->Elements[$Key] = $Replacement;
        }
    }

    /** @inheritDoc */
    public function ReplaceAt(string $Key, mixed $Element): mixed {
        if(isset($this->Elements[$Key])) {
            $Previous             = $this->Elements[$Key];
            $this->Elements[$Key] = $Element;
            return $Previous;
        }
        return null;
    }

    /** @inheritDoc */
    public function Remove(mixed $Element): mixed {
        return $this->RemoveAt($this->KeyOf($Element) ?? "");
    }

    /** @inheritDoc */
    public function RemoveAt(string $Key): mixed {
        if(isset($this->Elements[$Key])) {
            $Value = $this->Elements[$Key];
            unset($this->Elements[$Key]);
            return $Value;
        }
        return null;
    }

    /** @inheritDoc */
    public function KeyOf(mixed $Element): ?string {
        foreach($this->Elements as $Key => $mValue) {
            if($Element === $mValue) {
                return $Key;
            }
        }
        return null;
    }

    /** @inheritDoc */
    public function ChangeKey(mixed $Element, string $Key): ?string {
        if(($OldKey = $this->KeyOf($Element)) !== null) {
            $this->Elements[$Key] = $this->Elements[$OldKey];
            unset($this->Elements[$OldKey]);
            return $OldKey;
        }
        return null;
    }

    /** @inheritDoc */
    public function Contains(mixed $Element): bool {
        return \in_array($Element, $this->Elements);
    }

    /** @inheritDoc */
    public function ContainsKey(string $Key): bool {
        return isset($this->Elements[$Key]);
    }

    /** @inheritDoc */
    public function Clear(): void {
        $this->Elements = [];
    }

    /** @inheritDoc */
    public function Merge(IDictionary $Dictionary): void {
        foreach($Dictionary as $Key => $Value) {
            $this->Add($Key, $Value);
        }
    }

    /** @inheritDoc */
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

    //Implementation of \vDesk\Struct\Collections\IEnumerable.
    /** @inheritDoc */
    public function Count(): int {
        return \count($this->Elements);
    }

    /** @inheritDoc */
    public function Find(callable $Predicate): mixed {
        foreach($this as $Key => $Value) {
            if($Predicate($Value, $Key, $this)) {
                return $Value;
            }
        }
        return null;
    }

    /** @inheritDoc */
    public function Sort(callable $Predicate): static {
        $Sorted = $this->ToArray();
        \usort($Sorted, $Predicate);
        return new static($Sorted);
    }

    /** @inheritDoc */
    public function Filter(callable $Predicate): static {
        $Dictionary = new static();
        foreach($this as $Key => $Value) {
            if($Predicate($Value, $Key, $this)) {
                $Dictionary->Add($Key, $Value);
            }
        }
        return $Dictionary;
    }

    /** @inheritDoc */
    public function Map(callable $Predicate): static {
        $Dictionary = new static();
        foreach($this as $Key => $Value) {
            $Dictionary->Add($Key, $Predicate($Value, $Key, $this));
        }
        return $Dictionary;
    }

    /** @inheritDoc */
    public function Reduce(callable $Predicate, $InitialValue = null): mixed {
        $Accumulator = $InitialValue ?? \reset($this->Elements);
        foreach($this as $Key => $Value) {
            $Accumulator = $Predicate($Accumulator, $Value, $Key, $this);
        }
        return $Accumulator;
    }

    /** @inheritDoc */
    public function Any(callable $Predicate): bool {
        foreach($this as $Key => $Value) {
            if($Predicate($Value, $Key, $this)) {
                return true;
            }
        }
        return false;
    }

    /** @inheritDoc */
    public function Every(callable $Predicate): bool {
        foreach($this as $Key => $Value) {
            if(!$Predicate($Value, $Key, $this)) {
                return false;
            }
        }
        return true;
    }

    //Implementation of \ArrayAccess.
    /** @see \ArrayAccess::offsetSet() */
    public function offsetSet($Key, $Value): void {
        if($this->offsetExists($Key)) {
            $this->ReplaceAt($Key, $Value);
        } else {
            $this->Add($Key, $Value);
        }
    }

    /** @see \ArrayAccess::offsetExists() */
    public function offsetExists($Key): bool {
        return isset($this->Elements[$Key]);
    }

    /**
     * @throws \vDesk\Struct\InvalidOperationException Thrown if an element is being deleted using unset($Key).
     * @see \ArrayAccess::offsetUnset()
     */
    public function offsetUnset($Key): void {
        throw new InvalidOperationException("Cannot unset element at index " . __CLASS__ . "[$Key]. Use " . __CLASS__ . "::RemoveAt($Key) instead.");
    }

    /**
     * @throws \vDesk\Struct\Collections\KeyNotFoundException Thrown if the specified key doesn't exist.
     * @see \ArrayAccess::offsetGet()
     */
    public function offsetGet($Key) {
        if(!$this->offsetExists($Key)) {
            throw new KeyNotFoundException("Undefined index at " . __CLASS__ . "[$Key].");
        }
        return $this->Elements[$Key];
    }

    //Implementation of \Iterator.
    /** @see \Iterator::rewind() */
    public function rewind(): void {
        \reset($this->Elements);
    }

    /** @see \Iterator::current() */
    public function current() {
        return \current($this->Elements);
    }

    /** @see \Iterator::key() */
    public function key(): string {
        return \key($this->Elements);
    }

    /** @see \Iterator::next() */
    public function next(): void {
        \next($this->Elements);
    }

    /** @see \Iterator::valid() */
    public function valid(): bool {
        return \key($this->Elements) !== null;
    }


}
