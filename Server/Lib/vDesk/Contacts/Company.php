<?php
declare(strict_types=1);

namespace vDesk\Contacts;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\Data\Model;
use vDesk\Locale\Country;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a Company and its contact data.
 *
 * @property int                   $ID          (set once) Gets or sets the ID of the Company.
 * @property string                $Name        Gets or sets the Name of the Company.
 * @property string                $Street      Gets or sets the street of the Company.
 * @property string                $HouseNumber Gets or sets the house number of the Company.
 * @property int                   $ZipCode     Gets or sets the zip code of the Company.
 * @property string                $City        Gets or sets the city of the Company.
 * @property \vDesk\Locale\Country $Country     Gets or sets the country of the Company.
 * @property string                $PhoneNumber Gets or sets the phone number of the Company.
 * @property string                $FaxNumber   Gets or sets the fax number of the Company.
 * @property string                $Email       Gets or sets the email of the Company.
 * @property string                $Website     Gets or sets  the website of the Company.
 * @package vDesk\Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Company implements IModel {
    
    use Properties;
    
    /**
     * The ID of a non existent company.
     */
    public const None = 0;
    
    /**
     * Flag indicating whether the name of the Company has been changed.
     *
     * @var bool
     */
    protected bool $NameChanged = false;
    
    /**
     * Flag indicating whether the street of the Company has been changed.
     *
     * @var bool
     */
    protected bool $StreetChanged = false;
    
    /**
     * Flag indicating whether the house number of the Company has been changed.
     *
     * @var bool
     */
    protected bool $HouseNumberChanged = false;
    
    /**
     * Flag indicating whether the zip code of the Company has been changed.
     *
     * @var bool
     */
    protected bool $ZipCodeChanged = false;
    
    /**
     * Flag indicating whether the city of the Company has been changed.
     *
     * @var bool
     */
    protected bool $CityChanged = false;
    
    /**
     * Flag indicating whether the Country of the Company has been changed.
     *
     * @var bool
     */
    protected bool $CountryChanged = false;
    
    /**
     * Flag indicating whether the phone number of the Company has been changed.
     *
     * @var bool
     */
    protected bool $PhoneNumberChanged = false;
    
    /**
     * Flag indicating whether the fax number of the Company has been changed.
     *
     * @var bool
     */
    protected bool $FaxNumberChanged = false;
    
    /**
     * Flag indicating whether the email of the Company has been changed.
     *
     * @var bool
     */
    protected bool $EmailChanged = false;
    
    /**
     * Flag indicating whether the website of the Company has been changed.
     *
     * @var bool
     */
    protected bool $WebsiteChanged = false;
    
    /**
     * Initializes a new instance of the Company class.
     *
     * @param int|null                   $ID          Initializes the Company with the specified ID.
     * @param string|null                $Name        Initializes the Company with the specified name.
     * @param string|null                $Street      Initializes the Company with the specified street.
     * @param string|null                $HouseNumber Initializes the Company with the specified house number.
     * @param int|null                   $ZipCode     Initializes the Company with the specified zip code.
     * @param string|null                $City        Initializes the Company with the specified city.
     * @param \vDesk\Locale\Country|null $Country     Initializes the Company with the specified Country.
     * @param string|null                $PhoneNumber Initializes the Company with the specified phone number.
     * @param string|null                $FaxNumber   Initializes the Company with the specified fax number.
     * @param string|null                $Email       Initializes the Company with the specified email.
     * @param string|null                $Website     Initializes the Company with the specified website.
     */
    public function __construct(
        protected ?int $ID = null,
        protected ?string $Name = null,
        protected ?string $Street = null,
        protected ?string $HouseNumber = null,
        protected ?int $ZipCode = null,
        protected ?string $City = null,
        protected ?Country $Country = null,
        protected ?string $PhoneNumber = null,
        protected ?string $FaxNumber = null,
        protected ?string $Email = null,
        protected ?string $Website = null
    ) {
        $this->AddProperties([
            "ID"          => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Name"        => [
                \Get => MappedGetter::Create(
                    $this->Name,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Name")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->Name,
                    Type::String,
                    false,
                    $this->ID,
                    $this->NameChanged
                )
            ],
            "Street"      => [
                \Get => MappedGetter::Create(
                    $this->Street,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Street")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->Street,
                    Type::String,
                    false,
                    $this->ID,
                    $this->StreetChanged
                )
            ],
            "HouseNumber" => [
                \Get => MappedGetter::Create(
                    $this->HouseNumber,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("HouseNumber")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->HouseNumber,
                    Type::String,
                    false,
                    $this->ID,
                    $this->HouseNumberChanged
                )
            ],
            "ZipCode"     => [
                \Get => MappedGetter::Create(
                    $this->ZipCode,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("ZipCode")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->ZipCode,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->ZipCodeChanged
                )
            ],
            "City"        => [
                \Get => MappedGetter::Create(
                    $this->City,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("City")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->City,
                    Type::String,
                    false,
                    $this->ID,
                    $this->CityChanged
                )
            ],
            "Country"     => [
                \Get => MappedGetter::Create(
                    $this->Country,
                    Country::class,
                    true,
                    $this->ID,
                    Expression::Select("Country")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->Country,
                    Country::class,
                    false,
                    $this->ID,
                    $this->CountryChanged
                )
            ],
            "PhoneNumber" => [
                \Get => MappedGetter::Create(
                    $this->PhoneNumber,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("PhoneNumber")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->PhoneNumber,
                    Type::String,
                    true,
                    $this->ID,
                    $this->PhoneNumberChanged
                )
            ],
            "FaxNumber"   => [
                \Get => MappedGetter::Create(
                    $this->FaxNumber,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("FaxNumber")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->FaxNumber,
                    Type::String,
                    true,
                    $this->ID,
                    $this->FaxNumberChanged
                )
            ],
            "Email"       => [
                \Get => MappedGetter::Create(
                    $this->Email,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Email")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->Email,
                    Type::String,
                    true,
                    $this->ID,
                    $this->EmailChanged
                )
            ],
            "Website"     => [
                \Get => MappedGetter::Create(
                    $this->Website,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Website")
                              ->From("Contacts.Companies")
                ),
                \Set => MappedSetter::Create(
                    $this->Website,
                    Type::String,
                    true,
                    $this->ID,
                    $this->WebsiteChanged
                )
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
     * Fills the Company with its values from the database.
     *
     * @return \vDesk\Contacts\Company The filled Company.
     * @throws \vDesk\Data\IDNullException Thrown if the Company is virtual.
     *
     */
    public function Fill(): Company {
        if($this->ID === null) {
            throw new IDNullException();
        }
        if($this->ID !== self::None) {
            $Company           = Expression::Select("*")
                                           ->From("Contacts.Companies")
                                           ->Where(["ID" => $this->ID])
                                           ->Execute()
                                           ->ToMap();
            $this->Name        = $Company["Name"];
            $this->Street      = $Company["Street"];
            $this->HouseNumber = $Company["HouseNumber"];
            $this->ZipCode     = (int)$Company["ZipCode"];
            $this->City        = $Company["City"];
            $this->Country     = new Country($Company["Country"]);
            $this->PhoneNumber = $Company["PhoneNumber"];
            $this->FaxNumber   = $Company["FaxNumber"];
            $this->Email       = $Company["Email"];
            $this->Website     = $Company["Website"];
        }
        return $this;
    }
    
    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none has been supplied.
     */
    public function Save(): void {
        if($this->ID !== null) {
            Expression::Update("Contacts.Companies")
                      ->SetIf([
                          "Name"        => [$this->NameChanged => $this->Name],
                          "Street"      => [$this->StreetChanged => $this->Street],
                          "HouseNumber" => [$this->HouseNumberChanged => $this->HouseNumber],
                          "ZipCode"     => [$this->ZipCodeChanged => $this->ZipCode],
                          "City"        => [$this->CityChanged => $this->City],
                          "Country"     => [$this->CountryChanged => $this->Country->Code],
                          "PhoneNumber" => [$this->PhoneNumberChanged => $this->PhoneNumber],
                          "FaxNumber"   => [$this->FaxNumberChanged => $this->FaxNumber],
                          "Email"       => [$this->EmailChanged => $this->Email],
                          "Website"     => [$this->WebsiteChanged => $this->Website]
                      ])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        } else {
            $this->ID = Expression::Insert()
                                  ->Into("Contacts.Companies")
                                  ->Values([
                                      "ID"          => null,
                                      "Name"        => $this->Name,
                                      "Street"      => $this->Street,
                                      "HouseNumber" => $this->HouseNumber,
                                      "ZipCode"     => $this->ZipCode,
                                      "City"        => $this->City,
                                      "Country"     => $this->Country,
                                      "PhoneNumber" => $this->PhoneNumber,
                                      "FaxNumber"   => $this->FaxNumber,
                                      "Email"       => $this->Email,
                                      "Website"     => $this->Website
                                  ])
                                  ->ID();
        }
    }
    
    /**
     * Deletes the Company.
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("Contacts.Companies")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
            Expression::Update("Contacts.Contacts")
                      ->Set(["Company" => null])
                      ->Where(["Company" => $this->ID])
                      ->Execute();
        }
    }
    
    /**
     * Creates a Company from a specified data view.
     *
     * @param array $DataView The data to use to create a Company.
     *
     * @return \vDesk\Contacts\Company A Company created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Company {
        return new static(
            $DataView["ID"] ?? null,
            $DataView["Name"] ?? "",
            $DataView["Street"] ?? "",
            $DataView["HouseNumber"] ?? "",
            $DataView["ZipCode"] ?? 0,
            $DataView["City"] ?? "",
            new Country($DataView["Country"] ?? null),
            $DataView["PhoneNumber"] ?? "",
            $DataView["FaxNumber"] ?? "",
            $DataView["Email"] ?? "",
            $DataView["Website"] ?? ""
        );
    }
    
    /**
     * Creates a data view of the Company.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Company.
     *
     * @return array The data view representing the current state of the Company.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"          => $this->ID,
                "Name"        => $this->Name,
                "Street"      => $this->Street,
                "HouseNumber" => $this->HouseNumber,
                "ZipCode"     => $this->ZipCode,
                "City"        => $this->City,
                "Country"     => ($this->Country ?? new Model())->ToDataView(true),
                "PhoneNumber" => $this->PhoneNumber,
                "FaxNumber"   => $this->FaxNumber,
                "Email"       => $this->Email,
                "Website"     => $this->Website
            ];
    }
    
}
