<?php
declare(strict_types=1);

namespace vDesk\Contacts\Contact;

use vDesk\DataProvider\Expression;
use vDesk\Contacts\Contact;
use vDesk\Data\IDataView;
use vDesk\Data\IDNullException;
use vDesk\Data\ICollectionModel;
use vDesk\Struct\Collections\Typed\Observable\Collection;

/**
 * Represents a collection of {@link \vDesk\Contacts\Contact\Option} objects.
 *
 * @property \vDesk\Contacts\Contact|null $Contact (write once) Gets or sets the Contacts of the Options.
 * @package vDesk\Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Options extends Collection implements ICollectionModel {
    
    /**
     * The Type of the Options.
     */
    public const Type = Option::class;
    
    /**
     * Flag indicating whether the Options has been accessed.
     *
     * @var bool
     */
    private bool $Accessed = false;
    
    /**
     * The added Options of the Options.
     *
     * @var array
     */
    private array $Added = [];
    
    /**
     * The deleted Options of the Options.
     *
     * @var array
     */
    private array $Deleted = [];
    
    /**
     * Initializes a new instance of the Options class.
     *
     * @param iterable             $Elements Initializes the Options with the specified set of elements.
     * @param \vDesk\Contacts\Contact|null $Contact  Initializes the Options with the specified Contact.
     */
    public function __construct(iterable $Elements = [], private ?Contact $Contact = null) {
        parent::__construct($Elements);
        $this->AddProperty(
            "Contact",
            [
                \Get => fn(): ?Contact => $Contact,
                \Set => fn(Contact $Value) => $Contact ??= $Value
            ]
        );
    
        /**
         * Listens on the 'OnAdd'-event.
         *
         * @param \vDesk\Contacts\Contact\Options $Sender
         * @param \vDesk\Contacts\Contact\Option  $Option
         *
         * @return \vDesk\Contacts\Contact\Option
         */
        $this->OnAdd[] = fn(&$Sender, Option $Option): Option => $this->Added[] = $Option;
        
        /**
         * Listens on the 'OnDelete'-event.
         *
         * @param \vDesk\Contacts\Contact\Options $Sender
         * @param \vDesk\Contacts\Contact\Option  $Option
         *
         */
        $this->OnDelete[] = function(&$Sender, Option $Option): void {
            //Check if the associated contact is not virtual and if the option to remove is not virtual.
            if($Option->ID !== null) {
                $this->Deleted[] = $Option;
            }
        };
        
    }
    
    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Option {
        return parent::Find($Predicate);
    }
    
    /**
     * @inheritdoc
     */
    public function Remove($Element): Option {
        return parent::Remove($Element);
    }
    
    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Option {
        return parent::RemoveAt($Index);
    }
    
    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Option {
        if($this->ID !== null && !$this->Accessed) {
            $this->Fill();
        }
        return parent::offsetGet($Index);
    }
    
    /**
     * Fills the Options with its values from the database.
     *
     * @return \vDesk\Contacts\Contact\Options The filled Options.
     * @throws \vDesk\Data\IDNullException Thrown if the Event of the Options is virtual.
     *
     */
    public function Fill(): Options {
        
        if($this->Contact === null || $this->Contact->ID === null) {
            throw new IDNullException();
        }
        
        // Stop/disable event dispatching.
        $this->StopDispatch();
        
        if($this->Count > 0) {
            $this->Clear();
        }
        foreach(
            Expression::Select("*")
                      ->From("Contacts.Options")
                      ->Where(["Contact" => $this->Contact])
            as
            $Option
        ) {
            $this->Add(
                new Option(
                    (int)$Option["ID"],
                    (int)$Option["Type"],
                    $Option["Value"]
                )
            );
        }
        
        // Start/re-enable event dispatching.
        $this->StartDispatch();
        return $this;
    }
    
    /**
     * Saves possible changes if a valid ID has been supplied.
     */
    public function Save(): void {
        if($this->Contact !== null && $this->Contact->ID !== null) {
            //Save new options.
            foreach($this->Added as $Added) {
                //Retrieve ID.
                $Added->ID = Expression::Insert()
                                       ->Into("Contacts.Options")
                                       ->Values([
                                           "ID"      => null,
                                           "Contact" => $this->Contact,
                                           "Type"    => $Added->Type,
                                           "Value"   => $Added->Value
                                       ])
                                       ->ID();
            }
            
            //Update changed options.
            foreach($this->Elements as $Updated) {
                //if changed and validate..
                if($Updated->Changed) {
                    Expression::Update("Contacts.Options")
                              ->Set([
                                  "Type"  => $Updated->Type,
                                  "Value" => $Updated->Value
                              ])
                              ->Where(["ID" => $Updated])
                              ->Execute();
                }
            }
            //Delete removed options.
            foreach($this->Deleted as $Deleted) {
                Expression::Delete()
                          ->From("Contacts.Options")
                          ->Where(["ID" => $Deleted])
                          ->Execute();
            }
        }
    }
    
    /**
     * Deletes all ContactOptions of the Options.
     */
    public function Delete(): void {
        if($this->Contact !== null && $this->Contact->ID !== null) {
            Expression::Delete()
                      ->From("Contacts.Options")
                      ->Where(["Contact" => $this->Contact])
                      ->Execute();
        }
    }
    
    /**
     * Creates a Options from a specified data view.
     *
     * @param array $DataView The data to use to create a Options.
     *
     * @return \vDesk\Contacts\Contact\Options A Options created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): IDataView {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $aoData) {
                    yield Option::FromDataView($aoData);
                }
            })()
        );
    }
    
    /**
     * Creates a data view of the Options.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Options.
     *
     * @return array The data view representing the current state of the Options.
     */
    public function ToDataView(bool $Reference = false): array {
        return $this->Reduce(static function(array $Options, Option $Option) use ($Reference): array {
            $Options[] = $Option->ToDataView($Reference);
            return $Options;
        },
            []);
    }
    
    /**
     * Gets the ID of the Options.
     *
     * @return \vDesk\Contacts\Contact|null The ID of the Options; otherwise, null.
     */
    public function ID(): ?Contact {
        return $this->Contact;
    }
}
