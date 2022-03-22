<?php
declare(strict_types=1);

namespace vDesk\Struct\Collections\Observable;

use vDesk\Struct\Collections\Typed\CallableCollection;

/**
 * Represents an observable generic Collection.
 *
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnAdd    $Gets the "OnAdd"-Eventlisteners of the Collection.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnDelete $Gets the "OnDelete"-Eventlisteners of the Collection.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnChange $Gets the "OnChange"-Eventlisteners of the Collection.
 * @property \vDesk\Struct\Collections\Typed\CallableCollection $OnClear  $Gets the "OnClear"-Eventlisteners of the Collection.
 *
 * @package vDesk
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Collection extends \vDesk\Struct\Collections\Collection {

    /**
     * The 'OnAdd' callbacks of the Collection.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    protected CallableCollection $OnAdd;

    /**
     * The 'OnDelete' callbacks of the Collection.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    protected CallableCollection $OnDelete;

    /**
     * The 'OnChange' callbacks of the Collection.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    protected CallableCollection $OnChange;

    /**
     * The 'OnClear' callbacks of the Collection.
     *
     * @var \vDesk\Struct\Collections\Typed\CallableCollection
     */
    protected CallableCollection $OnClear;

    /**
     * Flag indicating whether the Collection is currently dispatching events.
     *
     * @var bool
     */
    private bool $Dispatching = false;

    /**
     * Initializes a new instance of the Collection class.
     *
     * @param iterable $Elements
     */
    public function __construct(iterable $Elements = []) {
        $this->OnAdd    = new CallableCollection();
        $this->OnDelete = new CallableCollection();
        $this->OnChange = new CallableCollection();
        $this->OnClear  = new CallableCollection();
        parent::__construct($Elements);
        $this->Dispatching = true;
        $this->AddProperties([
            "OnAdd"    => [\Get => fn&(): CallableCollection => $this->OnAdd],
            "OnDelete" => [\Get => fn&(): CallableCollection => $this->OnDelete],
            "OnChange" => [\Get => fn&(): CallableCollection => $this->OnChange],
            "OnClear"  => [\Get => fn&(): CallableCollection => $this->OnClear]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function Add(mixed $Element): void {
        if($this->Dispatching) {
            foreach($this->OnAdd as $OnAdd) {
                $OnAdd($this, $Element);
            }
        }
        parent::Add($Element);
    }

    /**
     * @inheritdoc
     */
    public function Insert(int $Index, mixed $Element): void {
        if($this->Dispatching) {
            foreach($this->OnAdd as $OnAdd) {
                $OnAdd($this, $Element);
            }
        }
        parent::Insert($Index, $Element);
    }

    /**
     * @inheritdoc
     */
    public function Remove(mixed $Element): mixed {
        if($this->Dispatching) {
            foreach($this->OnDelete as $OnDelete) {
                $OnDelete($this, $Element);
            }
        }
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): mixed {
        if($this->Dispatching) {
            $Element = parent::RemoveAt($Index);
            foreach($this->OnDelete as $OnDelete) {
                $OnDelete($this, $Element);
            }
            return $Element;
        }
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function Replace(mixed $Element, mixed $Replacement): void {
        if($this->Dispatching) {
            foreach($this->OnChange as $OnChange) {
                $OnChange($this, $Replacement);
            }
        }
        parent::Replace($Element, $Element);
    }

    /**
     * @inheritdoc
     */
    public function ReplaceAt(int $Key, mixed $Element): void {
        if($this->Dispatching) {
            foreach($this->OnChange as $OnChange) {
                $OnChange($this, $Element);
            }
        }
        parent::Replace($Element, $Element);
    }

    /**
     * @inheritdoc
     */
    public function Clear(): void {
        if($this->Dispatching) {
            foreach($this->OnClear as $OnClear) {
                $OnClear($this);
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

}