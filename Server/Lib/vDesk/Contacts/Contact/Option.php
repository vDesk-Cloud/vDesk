<?php
declare(strict_types=1);

namespace vDesk\Contacts\Contact;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents an entry either a telephone number, email address or website.
 *
 * @property int       $ID      (set once) Gets or sets the ID of the Option.
 * @property int       $Type    Gets or sets the Type of the Option.
 * @property string    $Value   Gets or sets the value of the Option.
 * @property-read bool $Changed Gets a value indicating whether the Option has been changed.
 * @package vDesk\Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Option implements IManagedModel {

    use Properties;

    /**
     * Represents a telephone-number.
     */
    public const PhoneNumber = 0;

    /**
     * Represents a fax-number.
     */
    public const FaxNumber = 1;

    /**
     * Represents an email-address.
     */
    public const Email = 2;

    /**
     * Represents a website-url.
     */
    public const Website = 3;

    /**
     * The ID of the Option.
     *
     * @var int|null
     */
    private ?int $ID;

    /**
     * The type of the Option.
     *
     * @var int|null
     */
    private ?int $Type;

    /**
     * Flag indicating whether the type of the Option has been changed.
     *
     * @var bool
     */
    private bool $TypeChanged = false;

    /**
     * The value of the Option.
     *
     * @var null|string
     */
    private ?string $Value;

    /**
     * Flag indicating whether the value of the Option has been changed.
     *
     * @var bool
     */
    private bool $ValueChanged = false;

    /**
     * Initializes a new instance of the Option class.
     *
     * @param int         $ID    Initializes the Option with the specified ID.
     * @param int|null    $Type  Initializes the Option with the specified type.
     * @param string|null $Value Initializes the Option with the specified value.
     */
    public function __construct(?int $ID = null, int $Type = null, string $Value = null) {
        $this->ID    = $ID;
        $this->Type  = $Type;
        $this->Value = $Value;
        $this->AddProperties([
            "ID"      => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Type"    => [
                \Get => MappedGetter::Create(
                    $this->Type,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Type")
                              ->From("Contacts.Options")
                ),
                \Set => MappedSetter::Create(
                    $this->Type,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->TypeChanged
                )
            ],
            "Value"   => [
                \Get => MappedGetter::Create(
                    $this->Value,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Value")
                              ->From("Contacts.Options")
                ),
                \Set => MappedSetter::Create(
                    $this->Value,
                    Type::String,
                    false,
                    $this->ID,
                    $this->ValueChanged
                )
            ],
            "Changed" => [
                \Get => function(): bool {
                    return $this->ValueChanged || $this->TypeChanged;
                }
            ]
        ]);
    }

    /**
     * Creates an Option from a specified data view.
     *
     * @param array $DataView The data to use to create an Option.
     *
     * @return \vDesk\Contacts\Contact\Option An Option created from the specified data view.
     */
    public static function FromDataView($DataView): Option {
        return new static(
            $DataView["ID"] ?? null,
            $DataView["Type"] ?? self::PhoneNumber,
            $DataView["Value"] ?? ""
        );
    }

    /**
     * Creates a data view of the Option.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Option.
     *
     * @return array The data view representing the current state of the Option.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"    => $this->ID,
                "Type"  => $this->Type,
                "Value" => $this->Value
            ];
    }

    /**
     * Fills the Option with its values from the database.
     *
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Option is virtual.
     *
     * @return \vDesk\Contacts\Contact\Option The filled Option.
     */
    public function Fill(): Option {

        if($this->ID === null) {
            throw new IDNullException();
        }

        $Option = Expression::Select("Type", "Value")
                            ->From("Contacts.Options")
                            ->Where(["ID" => $this->ID])
                            ->Execute()
                            ->ToMap();

        $this->Type  = $Option["Type"];
        $this->Value = $Option["Value"];
        return $this;
    }

    /**
     * Gets the ID of the Option.
     *
     * @return int|null The ID of the Option; otherwise, null.
     */
    public function ID(): ?int {
        return $this->ID;
    }
}
