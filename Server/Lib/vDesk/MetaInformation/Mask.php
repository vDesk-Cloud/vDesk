<?php
declare(strict_types=1);

namespace vDesk\MetaInformation;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\ICollectionModel;
use vDesk\MetaInformation\Mask\Row;
use vDesk\Struct\Collections\Typed\Observable\Collection;
use vDesk\Struct\Type;

/**
 * Represents collection of {@link \vDesk\MetaInformation\Mask\Row} objects, defining a schema for {@link \vDesk\MetaInformation\DataSet}
 * datasets. Provides functionality for updating existing and saving new masks.
 *
 * @property int    $ID     Gets or sets the ID of the Mask.
 * @property string $Name   Gets or sets the Name of the Mask.
 * @package vDesk/MetaInformation
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Mask extends Collection implements ICollectionModel {

    /**
     * The Type of the Mask.
     */
    public const Type = Row::class;

    /**
     * The ID of the Mask.
     *
     * @var int|null
     */
    protected ?int $ID;

    /**
     * The name of the Mask.
     *
     * @var null|string
     */
    protected ?string $Name;

    /**
     * Flag indicating whether the name of the Mask has been changed.
     *
     * @var bool
     */
    protected bool $NameChanged = false;

    /**
     * Flag indicating whether the indexer of the Mask has been invoked.
     *
     * @var bool
     */
    protected bool $Accessed = false;

    /**
     * The added Rows of the Mask.
     *
     * @var \vDesk\MetaInformation\Mask\Row[]
     */
    protected array $Added = [];

    /**
     * The deleted Rows of the Mask.
     *
     * @var \vDesk\MetaInformation\Mask\Row[]
     */
    protected array $Deleted = [];

    /**
     * Initializes a new instance of the Mask class.
     *
     * @param \vDesk\MetaInformation\Mask\Row[]|null $Rows Initializes the Mask with the specified Collection of Rows.
     * @param int|null                               $ID   Initializes the Mask with the specified ID.
     * @param string|null                            $Name Initializes the Mask with the specified name.
     */
    public function __construct(?iterable $Rows = [], ?int $ID = null, string $Name = null) {
        parent::__construct($Rows);
        $this->ID   = $ID;
        $this->Name = $Name;
        $this->AddProperties([
            "ID"   => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Name" => [
                \Get => MappedGetter::Create(
                    $this->Name,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Name")
                              ->From("MetaInformation.Masks")
                ),
                \Set => MappedSetter::Create(
                    $this->Name,
                    Type::String,
                    false,
                    $this->ID,
                    $this->NameChanged
                )
            ]
        ]);

        /**
         * Listens on the 'OnAdd'-event
         *
         * @param \vDesk\MetaInformation\Mask     $Sender
         * @param \vDesk\MetaInformation\Mask\Row $Row
         */
        $this->OnAdd[] = function(&$Sender, Row $Row): void {

            //Check if the row has a set index, else set it to the last one.
            if($Row->Index === null) {
                $Row->Index = ($this->Count > 0) ? $this->Elements[$this->Count - 1]->Index + 1 : 0;
            } else if(
                $this->Count > 0
                && ($TempRow = $this->Find(static fn(Row $Compare): bool => $Compare->Index === $Row->Index)) !== null
            ) {
                //$this->Insert($this->IndexOf($oTempRow), $Row);
                //If true, append it between and shift the index of the following rows.
                for($Index = $this->IndexOf($TempRow); $Index < $this->Count; $Index++) {
                    $this->Elements[$Index]->Index++;
                }
            }

            //Check if the mask is not virtual and if the row to add is virtual.
            if($this->ID !== null && $Row->ID === null) {
                $this->Added[] = $Row;
            }

            //Reorder the indices.
            $this->Reorder();
        };

        /**
         * Listens on the 'OnDelete'-event
         *
         * @param \vDesk\MetaInformation\Mask     $Sender
         * @param \vDesk\MetaInformation\Mask\Row $Row
         */
        $this->OnDelete[] = function(&$Sender, Row $Row): void {

            //Check if the mask and row are not virtual.
            if($this->ID !== null && $Row->ID !== null) {
                $this->Deleted[] = $Row;
            }

            //Reorder the indices.
            $this->Reorder();
        };
    }

    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->ID;
    }

    /**
     * Fills the Mask with its values from the database.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Mask is virtual.
     *
     * @return \vDesk\MetaInformation\Mask The filled Mask.
     */
    public function Fill(): Mask {

        if($this->ID === null) {
            throw new IDNullException();
        }

        //Disable events.
        $this->StopDispatch();

        //Refresh if filled before.
        if($this->Count > 0) {
            $this->Clear();
        }

        //Hydrate Mask.
        $Mask       = Expression::Select("Name")
                                ->From("MetaInformation.Masks")
                                ->Where(["ID" => $this->ID])
                                ->Execute()
                                ->ToMap();
        $this->Name = $Mask["Name"];

        //Hydrate Rows of the Mask.
        foreach(
            Expression::Select("*")
                      ->From("MetaInformation.MaskRows")
                      ->Where(["Mask" => $this->ID])
                      ->OrderBy(["Index" => true])
            as
            $Row
        ) {
            $this->Add(
                new Row(
                    (int)$Row["ID"],
                    $this,
                    (int)$Row["Index"],
                    $Row["Name"],
                    $Row["Type"],
                    (bool)$Row["Required"],
                    $Row["Validator"] !== null ? \json_decode($Row["Validator"]) : null
                )
            );
        }
        $this->Accessed = true;

        //Re-enable events.
        $this->StartDispatch();
        return $this;
    }
    
    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none was supplied.
     */
    public function Save(): void {

        $this->Sort(static fn(Row $First, Row $Second) => $First->Index <=> $Second->Index);

        if($this->ID !== null) {

            //Delete removed Rows.
            foreach($this->Deleted as $Deleted) {
                Expression::Delete()
                          ->From("MetaInformation.MaskRows")
                          ->Where(["ID" => $Deleted])
                          ->Execute();
                //Delete DataSetRows associated to the MaskRow.
                Expression::Delete()
                          ->From("MetaInformation.DataSetRows")
                          ->Where(["Row" => $Deleted])
                          ->Execute();
            }

            //Save new Rows.
            foreach($this->Added as $Added) {
                $Added->ID = Expression::Insert()
                                       ->Into("MetaInformation.MaskRows")
                                       ->Values([
                                           "ID"        => null,
                                           "Mask"      => $this->ID,
                                           "Index"     => $Added->Index,
                                           "Name"      => $Added->Name,
                                           "Type"      => $Added->Type,
                                           "Required"  => $Added->Required,
                                           "Validator" => $Added->Validator
                                       ])
                                       ->ID();
            }

            //Update changed Rows.
            /** @var Row $Updated */
            foreach($this->Elements as $Updated) {
                if($Updated->Changed) {
                    Expression::Update("MetaInformation.MaskRows")
                              ->Set([
                                  "Index"     => $Updated->Index,
                                  "Name"      => $Updated->Name,
                                  "Type"      => $Updated->Type,
                                  "Required"  => $Updated->Required,
                                  "Validator" => $Updated->Validator
                              ])
                              ->Where(["ID" => $Updated->ID])
                              ->Execute();
                }
            }

        } else {

            //Create new one.
            $this->ID = Expression::Insert()
                                  ->Into("MetaInformation.Masks")
                                  ->Values([
                                      "ID"   => null,
                                      "Name" => $this->Name
                                  ])
                                  ->ID();

            /** @var Row $Row */
            foreach($this as $Row) {
                $Row->ID = Expression::Insert()
                                     ->Into("MetaInformation.MaskRows")
                                     ->Values([
                                         "ID"        => null,
                                         "Mask"      => $this->ID,
                                         "Index"     => $Row->Index,
                                         "Name"      => $Row->Name,
                                         "Type"      => $Row->Type,
                                         "Required"  => $Row->Required,
                                         "Validator" => $Row->Validator
                                     ])
                                     ->ID();
            }
        }
    }

    /**
     * Deletes the Mask, its MaskRows and vDesk\Data\DataSets set under this Mask.
     */
    public function Delete(): void {
        if($this->ID !== null) {

            //Delete the Mask and its Rows.
            Expression::Delete()
                      ->From("MetaInformation.MaskRows")
                      ->Where(["Mask" => $this->ID])
                      ->Execute();
            Expression::Delete()
                      ->From("MetaInformation.Masks")
                      ->Where(["ID" => $this->ID])
                      ->Execute();

            //Delete DataSets created through this Mask.
            foreach(
                Expression::Select("ID")
                          ->From("MetaInformation.DataSets")
                          ->Where(["Mask" => $this->ID])
                as
                $Row
            ) {
                Expression::Delete()
                          ->From("MetaInformation.DataSetRows")
                          ->Where(["DataSet" => (int)$Row["ID"]])
                          ->Execute();
                Expression::Delete()
                          ->From("MetaInformation.DataSets")
                          ->Where(["ID" => (int)$Row["ID"]])
                          ->Execute();
            }
        }
    }

    /**
     * Creates a Mask from a specified data view.
     *
     * @param array $DataView The data to use to create a Mask.
     *
     * @return \vDesk\MetaInformation\Mask A Mask created from the specified data view.
     */
    public static function FromDataView($DataView): Mask {
        return new static(
            (static function() use ($DataView): \Generator {
                foreach($DataView["Rows"] ?? [] as $Row) {
                    yield Row::FromDataView($Row);
                }
            })(),
            $DataView["ID"] ?? null,
            $DataView["Name"] ?? ""
        );
    }

    /**
     * Creates a data view of the Mask.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Mask.
     *
     * @return array The data view representing the current state of the Mask.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"   => $this->ID,
                "Name" => $this->Name,
                "Rows" => $this->Reduce(
                    static function(array $Rows, Row $Row): array {
                        $Rows[] = $Row->ToDataView();
                        return $Rows;
                    },
                    []
                )
            ];
    }

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Row {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Row {
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Offset): Row {
        if(!$this->Accessed && $this->ID !== null) {
            $this->Fill();
        }
        return parent::offsetGet($Offset);
    }

    /**
     * Reorders the rows of the mask ascending by their indices.
     */
    private function Reorder(): void {

        //Sort the indices ascending.
        $this->Sort(static fn(Row $First, Row $Second): int => $First->Index <=> $Second->Index);

        //Loop through rows and set the indices.
        foreach($this->Elements as $Index => $Row) {
            $Row->Index = $Index;
        }
    }

}
