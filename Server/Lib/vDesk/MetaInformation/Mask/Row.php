<?php

declare(strict_types=1);

namespace vDesk\MetaInformation\Mask;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\MetaInformation\Mask;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;
use vDesk\Struct\Extension;

/**
 * Represents a row of a {@link \vDesk\MetaInformation\Mask}, defining the order, name and content type for data of
 * {@link \vDesk\MetaInformation\DataSet\Row} DataRows of {@link \vDesk\MetaInformation\DataSet} DataSets.
 *
 * @property int                         $ID           Gets or sets once the ID of the Row.
 * @property \vDesk\MetaInformation\Mask $Mask         Gets or sets the associated Mask of the Row.
 * @property int                         $Index        Gets or sets the index of the Row.
 * @property string                      $Name         Gets or sets the name of the Row.
 * @property string                      $Type         Gets or sets the type of the Row.
 * @property bool                        $Required     Gets or sets a value indicating whether the Row is required.
 * @property null|array|object           $Validator    Gets or sets the validator of the Row.
 * @property bool                        $Changed      Gets or sets a value indicating whether the Row has been changed.
 * @property bool                        $TypeChanged  Gets or sets a value indicating whether the type of the Row has been changed.
 * @package Archive/MetaInformation
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Row implements IManagedModel {
    
    use Properties;
    
    /**
     * The supported types of the Row.
     */
    public const Types
        = [
            Type::Int,
            Type::Float,
            Type::String,
            Type::Bool,
            Extension\Type::Date,
            Extension\Type::Time,
            Extension\Type::DateTime,
            Extension\Type::Enum,
            Extension\Type::Email,
            Extension\Type::Money,
            Extension\Type::URL
        ];
    
    /**
     * Flag indicating whether the index of the Row has been changed.
     *
     * @var bool
     */
    protected bool $IndexChanged = false;
    
    /**
     * Flag indicating whether the name of the Row has been changed.
     *
     * @var bool
     */
    protected bool $NameChanged = false;
    
    /**
     * Flag indicating whether the type of the Row has been changed.
     *
     * @var bool
     */
    protected bool $TypeChanged = false;
    
    /**
     * Flag indicating whether the required flag of the Row has been changed.
     *
     * @var bool
     */
    protected bool $RequiredChanged = false;
    
    /**
     * Flag indicating whether the validator of the Row has been changed.
     *
     * @var bool
     */
    protected bool $ValidatorChanged = false;
    
    /**
     * Initializes a new instance of the Row class.
     *
     * @param int|null                         $ID        Initializes the Row with the specified ID.
     * @param \vDesk\MetaInformation\Mask|null $Mask      Initializes the Row with the specified Mask.
     * @param int|null                         $Index     Initializes the Row with the specified index.
     * @param string|null                      $Name      Initializes the Row with the specified name.
     * @param string|null                      $Type      Initializes the Row with the specified type.
     * @param bool|null                        $Required  Flag indicating whether the Row requires a value.
     * @param null|array|object                $Validator Initializes the Row with the specified validator.
     */
    public function __construct(
        protected ?int $ID = null,
        protected ?Mask $Mask = null,
        protected ?int $Index = null,
        protected ?string $Name = null,
        protected ?string $Type = null,
        protected ?bool $Required = null,
        protected null|array|object $Validator = null
    ) {
        $this->AddProperties([
            "ID"          => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Mask"        => [
                \Get => MappedGetter::Create(
                    $this->Mask,
                    Mask::class,
                    true,
                    $this->ID,
                    Expression::Select("Mask")
                              ->From("MetaInformation.MaskRows")
                ),
                \Set => fn(Mask $Value) => $this->Mask ??= $Value
            ],
            "Index"       => [
                \Get => MappedGetter::Create(
                    $this->Index,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Index")
                              ->From("MetaInformation.MaskRows")
                ),
                \Set => MappedSetter::Create(
                    $this->Index,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->IndexChanged
                )
            ],
            "Name"        => [
                \Get => MappedGetter::Create(
                    $this->Name,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Name")
                              ->From("MetaInformation.MaskRows")
                ),
                \Set => MappedSetter::Create(
                    $this->Name,
                    Type::String,
                    false,
                    $this->ID,
                    $this->NameChanged
                )
            ],
            "Type"        => [
                \Get => MappedGetter::Create(
                    $this->Type,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Type")
                              ->From("MetaInformation.MaskRows")
                ),
                \Set => MappedSetter::Create(
                    $this->Type,
                    Type::String,
                    false,
                    $this->ID,
                    $this->TypeChanged
                )
            ],
            "Required"    => [
                \Get => MappedGetter::Create(
                    $this->Required,
                    Type::Bool,
                    true,
                    $this->ID,
                    Expression::Select("Required")
                              ->From("MetaInformation.MaskRows")
                ),
                \Set => MappedSetter::Create(
                    $this->Required,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->RequiredChanged
                )
            ],
            "Validator"   => [
                \Get => function() {
                    if($this->Validator === null && $this->ID !== null) {
                        $Validator       = Expression::Select("Validator")
                                                     ->From("MetaInformation.MaskRows")
                                                     ->Where(["ID" => $this->ID])();
                        $this->Validator = $Validator !== null ? \json_decode($Validator) : null;
                    }
                    return $this->Validator;
                },
                \Set => MappedSetter::Create(
                    $this->Validator,
                    Type::Mixed,
                    true,
                    $this->ID,
                    $this->ValidatorChanged
                )
            ],
            "Changed"     => [
                \Get => fn(): bool => $this->IndexChanged || $this->NameChanged || $this->TypeChanged || $this->RequiredChanged || $this->ValidatorChanged
            ],
            "TypeChanged" => [
                \Get => fn(): bool => $this->TypeChanged
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
     * @return \vDesk\MetaInformation\Mask\Row The filled Row.
     * @throws \vDesk\Data\IDNullException Thrown if the Row is virtual.
     *
     */
    public function Fill(): Row {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Row             = Expression::Select("*")
                                     ->From("MetaInformation.MaskRows")
                                     ->Where(["ID" => $this->ID])
                                     ->Execute()
                                     ->ToMap();
        $this->Mask      = new Mask([], (int)$Row["Mask"]);
        $this->Index     = (int)$Row["Index"];
        $this->Name      = $Row["Name"];
        $this->Type      = $Row["Type"];
        $this->Required  = (bool)$Row["Required"];
        $this->Validator = $Row["Validator"] !== null ? \json_decode($Row["Validator"]) : null;
        return $this;
    }
    
    /**
     * Creates a Row from a specified data view.
     *
     * @param array $DataView The data to use to create a Row.
     *
     * @return \vDesk\MetaInformation\Mask\Row A Row created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Row {
        return new static(
            $DataView["ID"] ?? null,
            null,
            $DataView["Index"] ?? 0,
            $DataView["Name"] ?? "",
            $DataView["Type"] ?? "",
            $DataView["Required"] ?? false,
            $DataView["Validator"] ?? null
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
                "ID"        => $this->ID,
                "Index"     => $this->Index,
                "Name"      => $this->Name,
                "Type"      => $this->Type,
                "Required"  => $this->Required,
                "Validator" => $this->Validator
            ];
    }
}
