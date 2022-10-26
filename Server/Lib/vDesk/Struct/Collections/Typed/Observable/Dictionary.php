<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections\Typed\Observable;

use vDesk\Struct\Collections\IObservable;
use vDesk\Struct\Collections\Typed\CallableCollection;

/**
 * Represents an observable typed Dictionary.
 *
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnAdd     $Gets the "Add"-Eventlisteners of the Dictionary.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnRemove  $Gets the "Remove"-Eventlisteners of the Dictionary.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnReplace $Gets the "Change"-Eventlisteners of the Dictionary.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnClear   $Gets the "Clear"-Eventlisteners of the Dictionary.
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Dictionary extends \vDesk\Struct\Collections\Typed\Dictionary implements IObservable {

    /**
     * The 'OnAdd' callbacks of the Dictionary.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    protected CallableCollection $OnAdd;

    /**
     * The 'OnRemove' callbacks of the Dictionary.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    protected CallableCollection $OnRemove;

    /**
     * The 'OnReplace' callbacks of the Dictionary.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    protected CallableCollection $OnReplace;

    /**
     * The 'OnClear' callbacks of the Dictionary.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    protected CallableCollection $OnClear;

    /**
     * Flag indicating whether the Dictionary is currently dispatching events.
     *
     * @var bool
     */
    private bool $Dispatching = false;

    /** @inheritdoc */
    public function __construct(iterable $Elements = [], iterable $Add = [], iterable $Remove = [], iterable $Replace = [], iterable $Clear = []) {
        $this->OnAdd     = new CallableCollection($Add);
        $this->OnRemove  = new CallableCollection($Remove);
        $this->OnReplace = new CallableCollection($Replace);
        $this->OnClear   = new CallableCollection($Clear);
        parent::__construct($Elements);
        $this->Dispatching = true;
        $this->AddProperties([
            "OnAdd"     => [\Get => fn&(): CallableCollection => $this->OnAdd],
            "OnRemove"  => [\Get => fn&(): CallableCollection => $this->OnRemove],
            "OnReplace" => [\Get => fn&(): CallableCollection => $this->OnReplace],
            "OnClear"   => [\Get => fn&(): CallableCollection => $this->OnClear]
        ]);
    }

    /** @inheritdoc */
    public function Add(string $Key, mixed $Element): void {
        if($this->Dispatching) {
            foreach($this->OnAdd as $OnAdd) {
                if(!($OnAdd($Element, $this) ?? true)) {
                    return;
                }
            }
        }
        parent::Add($Key, $Element);
    }

    /** @inheritdoc */
    public function Insert(string $Before, string $Key, mixed $Element): void {
        if($this->Dispatching) {
            foreach($this->OnAdd as $OnAdd) {
                if(!($OnAdd($Element, $this) ?? true)) {
                    return;
                }
            }
        }
        parent::Insert($Before, $Key, $Element);
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
    public function RemoveAt(string $Key): mixed {
        if($this->Dispatching && isset($this->Elements[$Key])) {
            foreach($this->OnRemove as $OnRemove) {
                if(!($OnRemove($this->Elements[$Key], $this) ?? true)) {
                    return null;
                }
            }
        }
        return parent::RemoveAt($Key);
    }

    /** @inheritdoc */
    public function Replace($Element, $Replacement): void {
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
    public function ReplaceAt(string $Key, $Element): mixed {
        if($this->Dispatching && isset($this->Elements[$Key])) {
            foreach($this->OnReplace as $OnReplace) {
                if(!($OnReplace($this->Elements[$Key], $Element, $this) ?? true)) {
                    return null;
                }
            }
        }
        return parent::ReplaceAt($Key, $Element);
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

}