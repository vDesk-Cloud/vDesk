<?php
declare(strict_types=1);

namespace vDesk\MetaInformation;

use vDesk\Archive\Element;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\Data\IDNullException;
use vDesk\Data\ICollectionModel;
use vDesk\MetaInformation\DataSet\Row;
use vDesk\Struct\Collections\Typed\Collection;
use vDesk\Struct\Type;
use vDesk\Struct\Extension;

/**
 * Represents a Collection of DataSet\Rows, describing the metadata of an {@link \vDesk\Archive\Element}.
 *
 * @property int                         $ID      Gets or sets the ID of the DataSet.
 * @property \vDesk\Archive\Element      $Element Gets or sets the Element of the DataSet.
 * @property \vDesk\MetaInformation\Mask $Mask    Gets or sets the Mask of the DataSet.
 * @package vDesk/MetaInformation
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class DataSet extends Collection implements ICollectionModel {

    /**
     * The Type of the DataSet.
     */
    public const Type = Row::class;

    /**
     * Initializes a new instance of the DataSet class.
     *
     * @param \vDesk\MetaInformation\DataSet\Row[] $Rows    Initializes the DataSet with the specified Collection of Rows.
     * @param int|null                             $ID      Initializes the DataSet with the specified ID.
     * @param \vDesk\Archive\Element|null          $Element Initializes the DataSet with the specified Element.
     * @param \vDesk\MetaInformation\Mask|null     $Mask    Initializes the DataSet with the specified Mask.
     */
    public function __construct(iterable $Rows = [], protected ?int $ID = null, protected ?Element $Element = null, protected ?Mask $Mask = null) {
        parent::__construct($Rows);
        $this->AddProperties(
            [
                "ID"      => [
                    \Get => fn(): ?int => $this->ID,
                    \Set => fn(int $Value) => $this->ID ??= $Value
                ],
                "Element" => [
                    \Get => MappedGetter::Create(
                        $this->Element,
                        Element::class,
                        true,
                        $this->ID,
                        Expression::Select("Element")
                                  ->From("MetaInformation.DataSets")
                    ),
                    \Set => fn(Element $Value) => $this->Element ??= $Value
                ],
                "Mask"    => [
                    \Get => MappedGetter::Create(
                        $this->Mask,
                        Mask::class,
                        true,
                        $this->ID,
                        Expression::Select("Mask")
                                  ->From("MetaInformation.DataSets")
                    ),
                    \Set => function(Mask $Value): void {
                        $this->Mask ??= $Value;
                        $this->Clear();
                        foreach($Value as $MaskRow) {
                            $this->Add(
                                new Row(
                                    null,
                                    $this,
                                    $MaskRow
                                )
                            );
                        }
                    }
                ]
            ]
        );
    }

    /** @inheritdoc */
    public function ID(): ?int {
        return $this->ID;
    }

    /**
     * Fills the DataSet with its values from the database.
     *
     * @return \vDesk\MetaInformation\DataSet The filled DataSet.
     * @throws \vDesk\Data\IDNullException Thrown if the DataSet is virtual.
     *
     */
    public function Fill(): DataSet {
        if($this->ID === null) {
            throw new IDNullException();
        }

        //Refresh if filled before.
        if($this->Count > 0) {
            $this->Clear();
        }

        $DataSet       = Expression::Select("Element", "Mask")
                                   ->From("MetaInformation.DataSets")
                                   ->Where(["ID" => $this->ID])
                                   ->Execute()
                                   ->ToMap();
        $this->Element = new Element((int)$DataSet["Element"]);
        $this->Mask    = (new Mask([], (int)$DataSet["Mask"]))->Fill();

        //Get metadata.
        foreach(
            Expression::Select("ID", "Row", "Value")
                      ->From("MetaInformation.DataSetRows")
                      ->Where(["DataSet" => $this->ID])
            as
            $Row
        ) {

            $MaskRow = $this->Mask->Find(static fn(Mask\Row $MaskRow): bool => $MaskRow->ID === (int)$Row["Row"]);
            //Convert value to proper type.
            $Value = match ($Row["Value"]) {
                null => null,
                default => match ($MaskRow->Type) {
                    Type::Int => (int)$Row["Value"],
                    Type::Float => (float)$Row["Value"],
                    Type::Bool => (bool)$Row["Value"],
                    Extension\Type::Date, Extension\Type::Time, Extension\Type::DateTime => new \DateTime($Row["Value"]),
                    default => $Row["Value"]
                }
            };

            $this->Add(new Row((int)$Row["ID"], $this, $MaskRow, $Value));
        }
        return $this;
    }

    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none was supplied.
     */
    public function Save(): void {

        foreach($this->Elements as $DataSetRow) {
            if($DataSetRow->Row->Required && $DataSetRow->Value === null) {
                throw new \InvalidArgumentException("Missing value of DataSet\\Row for required Mask\\Row '{$DataSetRow->Row->Name}' of Mask '{$this->Mask->Name}'!");
            }
        }

        //Check if the DataSet is not virtual and a valid Mask has been set.
        if($this->ID !== null && $this->Mask !== null) {
            foreach($this->Elements as $DataSetRow) {
                if($DataSetRow->Changed) {
                    Expression::Update("MetaInformation.DataSetRows")
                              ->Set(["Value" => $DataSetRow->Value])
                              ->Where(["ID" => $DataSetRow->ID])
                              ->Execute();
                }
            }
        } else if($this->ID === null && $this->Element !== null && $this->Mask !== null) {
            $this->ID = Expression::Insert()
                                  ->Into("MetaInformation.DataSets")
                                  ->Values([
                                      "ID"      => null,
                                      "Element" => $this->Element->ID,
                                      "Mask"    => $this->Mask->ID
                                  ])
                                  ->ID();
            foreach($this->Elements as $DataSetRow) {
                //Retrieve ID.
                $DataSetRow->ID = Expression::Insert()
                                            ->Into("MetaInformation.DataSetRows")
                                            ->Values([
                                                "DataSet" => $this->ID,
                                                "Row"     => $DataSetRow->Row,
                                                "Value"   => $DataSetRow->Value
                                            ])
                                            ->ID();
            }
        }
    }

    /**
     * Deletes the DataSet and its DataRows.
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("MetaInformation.DataSetRows")
                      ->Where(["DataSet" => $this->ID])
                      ->Execute();
            Expression::Delete()
                      ->From("MetaInformation.DataSets")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }

    /**
     * Creates a DataSet from a specified data view.
     *
     * @param array $DataView The data to use to create a DataSet.
     *
     * @return \vDesk\MetaInformation\DataSet A DataSet created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): DataSet {
        $DataSet = new static(null, $DataView["ID"] ?? null);
        foreach($DataView["Rows"] ?? [] as $Data) {
            $DataSet->Add(Row::FromDataView($Data));
        }
        $DataSet->Mask = new Mask($DataView["Mask"] ?? null);
        return $DataSet;
    }

    /**
     * Creates a data view of the DataSet.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the DataSet.
     *
     * @return array The data view representing the current state of the DataSet.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"   => $this->ID,
                "Mask" => $this->Mask?->ToDataView(true),
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
     * Factory method that creates a new DataSet filled with the Rows according a specified Mask.
     *
     * @param \vDesk\MetaInformation\Mask $Mask    The Mask to create the DataSet from.
     * @param \vDesk\Archive\Element|null $Element Creates the DataSet with the specified Element.
     *
     * @return \vDesk\MetaInformation\DataSet A DataSet yielding the Rows according the specified Mask.
     */
    public static function FromMask(Mask $Mask, Element $Element = null): DataSet {
        return new static(\array_map(static fn(Mask\Row $Row): Row => new Row(Row: $Row), $Mask->ToArray()), null, $Element, $Mask);
    }

    /** @inheritdoc */
    public function offsetGet($Index): Row {
        return \is_string($Index)
            ? $this->Find(static fn(Row $DataSetRow): bool => $DataSetRow->Row->Name === $Index)
            : parent::offsetGet($Index);
    }

    /** @inheritdoc */
    public function Find(callable $Predicate): ?Row {
        return parent::Find($Predicate);
    }

    /** @inheritdoc */
    public function Remove($Element): Row {
        return parent::Remove($Element);
    }

    /** @inheritdoc */
    public function RemoveAt(int $Index): Row {
        return parent::RemoveAt($Index);
    }

}