<?php
declare(strict_types=1);

namespace vDesk\MetaInformation\DataSet;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\MetaInformation\DataSet;
use vDesk\MetaInformation\Mask;
use vDesk\Struct\InvalidOperationException;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;
use vDesk\Struct\Extension;
use vDesk\Utils\Validate;

/**
 * Represents a row of data, according to a {@link \vDesk\MetaInformation\DataSet}.
 *
 * @property int                             $ID            (Write once) Gets or sets the ID of the Row.
 * @property \vDesk\MetaInformation\DataSet  $DataSet       Gets the associated DataSet of the Row.
 * @property \vDesk\MetaInformation\Mask\Row $Row           Gets or sets the associated Mask\Row of the DataSet\Row.
 * @property string                          $Content       Gets or sets the content of the Row.
 * @property null|mixed                      $Value         Gets or sets the value of the Row.
 * @property-read bool                       $Changed       Gets a value indicating whether the Row has been changed.
 * @package Archive/MetaInformation
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Row implements IManagedModel {

    use Properties;

    /**
     * The ID of the Row.
     *
     * @var null|int
     */
    protected ?int $ID;

    /**
     * The DataSet of the Row.
     *
     * @var null|\vDesk\MetaInformation\DataSet
     */
    protected ?DataSet $DataSet;

    /**
     * The Mask\Row of the Row.
     *
     * @var null|\vDesk\MetaInformation\Mask\Row
     */
    protected ?Mask\Row $Row;

    /**
     * The value of the Row.
     *
     * @var null|mixed
     */
    protected $Value;

    /**
     * Flag indicating whether the value of the Row has been changed.
     *
     * @var bool
     */
    protected bool $ValueChanged = false;

    /**
     * Initializes a new instance of the Row class.
     *
     * @param int                                  $ID      Initializes the Row with the specified ID.
     * @param \vDesk\MetaInformation\DataSet|null  $DataSet Initializes the Row with the specified DataSet.
     * @param \vDesk\MetaInformation\Mask\Row|null $Row     Initializes the Row with the specified Row.
     * @param null                                 $Value   Initializes the Row with the specified value.
     */
    public function __construct(
        ?int $ID = null,
        DataSet $DataSet = null,
        Mask\Row $Row = null,
        $Value = null
    ) {
        $this->ID      = $ID;
        $this->DataSet = $DataSet;
        $this->Row     = $Row;
        $this->Value   = $Value;
        $this->AddProperties([
            "ID"      => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "DataSet" => [
                \Get => MappedGetter::Create(
                    $this->DataSet,
                    DataSet::class,
                    true,
                    $this->ID,
                    Expression::Select("DataSet")
                              ->From("MetaInformation.DataSetRows")
                )
            ],
            "Row"     => [
                \Get => MappedGetter::Create(
                    $this->Row,
                    Mask\Row::class,
                    true,
                    $this->ID,
                    Expression::Select("Row")
                              ->From("MetaInformation.DataSetRows")
                ),
                \Set => fn(Mask\Row $Value) => $this->Row ??= $Value,
            ],
            "Value"   => [
                \Get => function() {

                    if($this->ID !== null && $this->Value === null && ($this->Properties["Row"]->Getter)()->Required) {
                        $Value = Expression::Select("Value")
                                           ->From("MetaInformation.DataSetRows")
                                           ->Where(["ID" => $this->ID])();

                        switch($this->Row->Type) {
                            case Type::Int:
                                $this->Value = (int)$Value;
                                break;
                            case Type::Float:
                                $this->Value = (float)$Value;
                                break;
                            case Type::Bool:
                                $this->Value = (bool)$Value;
                                break;
                            case Extension\Type::Date:
                            case Extension\Type::Time:
                            case Extension\Type::DateTime:
                                $this->Value = new \DateTime($Value);
                                break;
                            default:
                                $this->Value = $Value;
                                break;
                        }
                    }

                    return $this->Value;

                },
                \Set => function($Value): void {

                    if(($this->Properties["Row"]->Getter)() === null) {
                        throw new InvalidOperationException("Cannot set value without specified Mask\Row");
                    }
                    if($this->Row->Required && $Value === null) {
                        throw new \InvalidArgumentException("DataSet\Row '[{$this->Row->Name}]' requires a value!");
                    }
                    if($Value !== null && !Validate::As($Value, $this->Row->Type, $this->Row->Validator)) {
                        var_dump($this->Row->Validator);
                        throw new \TypeError("Value of DataSet\Row '{$this->Row->Name}' is invalid!");
                    }

                    if($this->ID !== null && $this->Value !== $Value) {
                        $this->ValueChanged = true;
                    }
    
                    $this->Value = $Value;

                }
            ],
            "Changed" => [
                \Get => fn(): bool => $this->ValueChanged
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->ID;
    }

    /**
     * Fills the Row with its values from the database.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Row is virtual.
     *
     * @return \vDesk\MetaInformation\DataSet\Row The filled Row.
     */
    public function Fill(): Row {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Row       = Expression::Select("Row", "Content")
                               ->From("MetaInformation.DataSetRows")
                               ->Where(["ID" => $this->ID])
                               ->Execute()
                               ->ToMap();
        $this->Row = new Mask\Row((int)$Row["Row"]);
        switch($this->Row->Type) {
            case Type::Int:
                $this->Value = (int)$Row["Value"];
                break;
            case Type::Float:
                $this->Value = (float)$Row["Value"];
                break;
            case Type::Bool:
                $this->Value = (bool)$Row["Value"];
                break;
            case Extension\Type::Date:
            case Extension\Type::Time:
            case Extension\Type::DateTime:
                $this->Value = new \DateTime($Row["Value"]);
                break;
            default:
                $this->Value = $Row["Value"];
                break;
        }
        return $this;
    }

    /**
     * Creates a Row from a specified data view.
     *
     * @param array $DataView The data to use to create a Row.
     *
     * @return \vDesk\MetaInformation\DataSet\Row A Row created from the specified data view.
     */
    public static function FromDataView($DataView): Row {
        return new static(
            $DataView["ID"] ?? null,
            null,
            new Mask\Row($DataView["Row"] ?? null),
            $DataView["Value"] ?? ""
        );
    }

    /**
     * Creates a data view of the Row.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Row.
     *
     * @return array The data view representing the current state of the Row.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"    => $this->ID,
                "Row"   => $this->Row->ID,
                "Value" => $this->Value instanceof \DateTime ? $this->Value->format(\DateTime::ATOM) : $this->Value
            ];
    }

}
