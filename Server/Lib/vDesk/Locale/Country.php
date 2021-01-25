<?php
declare(strict_types=1);

namespace vDesk\Locale;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\Data\IDataView;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents the data of an existing country.
 *
 * @property int    $ID   (write once) Gets or sets the ID of the Country.
 * @property string $Code (write once) Gets or sets the code of the Country.
 * @property string $Name (write once) Gets or sets the name of the Country.
 * @package vDesk\Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Country implements IManagedModel {
    
    use Properties;
    
    /**
     * Initializes a new instance of the Country class.
     *
     * @param string|null $Code Initializes the Country with the specified code.
     * @param string|null $Name Initializes the Country with the specified name.
     */
    public function __construct(protected ?string $Code = null, protected ?string $Name = null) {
        $this->AddProperties([
            "Code" => [
                \Get => MappedGetter::Create(
                    $this->Code,
                    Type::String,
                    true,
                    $this->Code,
                    Expression::Select("Code")
                              ->From("Locale.Countries")
                ),
                \Set => fn(string $Value) => $this->Code ??= $Value
            ],
            "Name" => [
                \Get => function(): ?string {
                    if($this->Code !== null && $this->Name === null) {
                        $this->Code = Expression::Select("Name")
                                                ->From("Locale.Countries")
                                                ->Where(["Code" => $this->Code])();
                    }
                    return $this->Name;
                },
                \Set => fn(string $Value) => $this->Name ??= $Value
            ]
        ]);
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?string {
        return $this->Code;
    }
    
    /**
     * Factory method that creates a new instance of the Country class by a specified country code.
     *
     * @param string $Code The code of the Country to create.
     *
     * @return \vDesk\Locale\Country A Country filled with the values stored in the database.
     */
    public static function FromCode(string $Code): Country {
        $Row           = Expression::Select("Name")
                                   ->From("Locale.Countries")
                                   ->Where(["Code" => $Code])
                                   ->Execute()
                                   ->ToMap();
        $Country       = new static($Code);
        $Country->Name = $Row["Name"];
        return $Country;
    }
    
    /**
     * Fills the Country with its values from the database.
     *
     * @return \vDesk\Locale\Country The filled Country.
     * @throws \vDesk\Data\IDNullException Thrown if the Country is virtual.
     *
     */
    public function Fill(): Country {
        if($this->Code === null) {
            throw new IDNullException();
            
        }
        $Country    = Expression::Select("Name")
                                ->From("Locale.Countries")
                                ->Where(["Code" => $this->Code])
                                ->Execute()
                                ->ToMap();
        $this->Name = $Country["Name"];
        return $this;
    }
    
    /**
     * Creates a Country from a specified data view.
     *
     * @param array $DataView The data to use to create a Country.
     *
     * @return \vDesk\Locale\Country A Country created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): IDataView {
        return new static($DataView["Code"] ?? null, $DataView["Name"] ?? "");
    }
    
    /**
     * Creates a data view of the Country.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Country.
     *
     * @return array The data view representing the current state of the Country.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["Code" => $this->Code]
            : [
                "Code" => $this->Code,
                "Name" => $this->Name
            ];
    }
}
