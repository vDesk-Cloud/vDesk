<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections\Typed\Observable;

use vDesk\Struct\Collections\IObservable;
use vDesk\Struct\Collections\Typed\CallableCollection;

/**
 * Represents an observable typed Collection.
 *
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnAdd    $Gets the "OnAdd"-Eventlisteners of the Collection.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnDelete $Gets the "OnDelete"-Eventlisteners of the Collection.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnChange $Gets the "OnChange"-Eventlisteners of the Collection.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnClear  $Gets the "OnClear"-Eventlisteners of the Collection.
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Collection extends \vDesk\Struct\Collections\Typed\Collection implements IObservable {

    /**
     * The 'OnAdd' callbacks of the Collection.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    public CallableCollection $OnAdd;

    /**
     * The 'OnDelete' callbacks of the Collection.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    public CallableCollection $OnRemove;

    /**
     * The 'OnChange' callbacks of the Collection.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    public CallableCollection $OnReplace;

    /**
     * The 'OnClear' callbacks of the Collection.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    public CallableCollection $OnClear;

    /**
     * Flag indicating whether the Collection is currently dispatching events.
     *
     * @var bool
     */
    private bool $Dispatching = false;

    /** @inheritdoc */
    public function __construct(iterable $Elements = []) {
        parent::__construct($Elements);
        $this->OnAdd       = new CallableCollection();
        $this->OnRemove    = new CallableCollection();
        $this->OnReplace   = new CallableCollection();
        $this->OnClear     = new CallableCollection();
        $this->Dispatching = true;
    }

    /** @inheritdoc */
    public function Add(mixed $Element): void {
        if($this->Dispatching) {
            foreach($this->OnAdd as $OnAdd) {
                if(!($OnAdd($Element, $this) ?? true)) {
                    return;
                }
            }
        }
        parent::Add($Element);
    }

    /** @inheritdoc */
    public function Insert(int $Index, mixed $Element): void {
        if($this->Dispatching) {
            foreach($this->OnAdd as $OnAdd) {
                if(!($OnAdd($Element, $this) ?? true)) {
                    return;
                }
            }
        }
        parent::Insert($Index, $Element);
    }

    /** @inheritdoc */
    public function Remove(mixed $Element): mixed {
        if($this->Dispatching) {
            foreach($this->OnRemove as $OnRemove) {
                if(!($OnRemove($Element, $this) ?? true)) {
                    return null;
                }
            }
        }
        return parent::Remove($Element);
    }

    /** @inheritdoc */
    public function RemoveAt(int $Index): mixed {
        if($this->Dispatching && isset($this->Elements[$Index])) {
            foreach($this->OnRemove as $OnRemove) {
                if(!($OnRemove($this->Elements[$Index], $this) ?? true)) {
                    return null;
                }
            }
        }
        return parent::RemoveAt($Index);
    }

    /** @inheritdoc */
    public function Replace(mixed $Element, mixed $Replacement): void {
        if($this->Dispatching) {
            foreach($this->OnReplace as $OnReplace) {
                if(!($OnReplace($Element, $Replacement, $this) ?? true)) {
                    return;
                }
            }
        }
        parent::Replace($Element, $Replacement);
    }

    /** @inheritdoc */
    public function ReplaceAt(int $Index, mixed $Element): mixed {
        if($this->Dispatching && isset($this->Elements[$Index])) {
            foreach($this->OnReplace as $OnReplace) {
                if(!($OnReplace($this->Elements[$Index], $Element, $this) ?? true)) {
                    return null;
                }
            }
        }
        return parent::ReplaceAt($Index, $Element);
    }

    /** @inheritdoc */
    public function Clear(): void {
        if($this->Dispatching) {
            foreach($this->OnClear as $OnClear) {
                if(!($OnClear($this) ?? true)) {
                    return;
                }
            }
        }
        parent::Clear();
    }

    /** @inheritDoc */
    public function Dispatching(?bool $Dispatching): bool {
        return $Dispatching === null ? $this->Dispatching : $this->Dispatching = $Dispatching;
    }

    /** @inheritDoc */
    public function AddEventListener(string $Event, callable $Listener): void {
        match ($Event) {
            IObservable::Add => $this->OnAdd->Add($Listener),
            IObservable::Remove => $this->OnRemove->Add($Listener),
            IObservable::Replace => $this->OnReplace->Add($Listener),
            IObservable::Clear => $this->OnClear->Add($Listener)
        };
    }

    /** @inheritDoc */
    public function RemoveEventListener(string $Event, callable $Listener): void {
        match ($Event) {
            IObservable::Add => $this->OnAdd->Remove($Listener),
            IObservable::Remove => $this->OnRemove->Remove($Listener),
            IObservable::Replace => $this->OnReplace->Remove($Listener),
            IObservable::Clear => $this->OnClear->Remove($Listener)
        };
    }

    /**
     * Enables/starts dispatching of events.
     */
    public function StartDispatch(): void {
        $this->Dispatching = true;
    }

    /**
     * Disables/stops dispatching of events.
     */
    public function StopDispatch(): void {
        $this->Dispatching = false;
    }

}