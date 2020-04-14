<?php
declare(strict_types=1);

namespace vDesk\Contacts;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Contacts\Contact\Options;
use vDesk\Data\IDNullException;
use vDesk\Data\Model;
use vDesk\Locale\Country;
use vDesk\Security\AccessControlledModel;
use vDesk\Security\AccessControlList;
use vDesk\Security\User;
use vDesk\Struct\Type;

/**
 * Represents a Contact.
 *
 * @property int                                        $ID             (write once) Gets or sets the ID of the Contact.
 * @property \vDesk\Security\User            $Owner          (write once) Gets or sets the Owner of the Contact.
 * @property int                             $Gender         Gets or sets the gender of the Contact.
 * @property string                          $Title          Gets or sets the title of the Contact.
 * @property string                          $Forename       Gets or sets the forename of the Contact.
 * @property string                          $Surname        Gets or sets the Surname of the Contact.
 * @property string                          $Street         Gets or sets the street of the Contact.
 * @property string                          $HouseNumber    Gets or sets the housenumber of the Contact.
 * @property int                             $ZipCode        Gets or sets the zipcode of the Contact.
 * @property string                          $City           Gets or sets the city of the Contact.
 * @property \vDesk\Locale\Country           $Country        Gets or sets the country of the Contact.
 * @property \vDesk\Contacts\Contact\Options $Options        Gets or sets the ContactOptions of the Contact.
 * @property \vDesk\Contacts\Company         $Company        Gets or sets the company of the Contact.
 * @property string                          $Annotations    Gets or sets the annotations of the Contact.
 * @package Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Contact extends AccessControlledModel {

    /**
     * Male gender-type.
     */
    public const Male = 0;

    /**
     * Female gender-type.
     */
    public const Female = 1;

    /**
     * The ID of the Contact.
     *
     * @var int|null
     */
    protected ?int $ID;

    /**
     * The owner of the Contact.
     *
     * @var null|\vDesk\Security\User
     */
    protected ?User $Owner;

    /**
     * The gender of the Contact.
     *
     * @var int|null
     */
    protected ?int $Gender;

    /**
     * Flag indicating whether the gender of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $GenderChanged = false;

    /**
     * The title of the Contact.
     *
     * @var null|string
     */
    protected ?string $Title;

    /**
     * Flag indicating whether the title of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $TitleChanged = false;

    /**
     * The forename of the Contact.
     *
     * @var null|string
     */
    protected ?string $Forename;

    /**
     * Flag indicating whether the forename of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $ForenameChanged = false;

    /**
     * The surname of the Contact.
     *
     * @var null|string
     */
    protected ?string $Surname;

    /**
     * Flag indicating whether the surname of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $SurnameChanged = false;

    /**
     * The street of the Contact.
     *
     * @var null|string
     */
    protected ?string $Street;

    /**
     * Flag indicating whether the street of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $StreetChanged = false;

    /**
     * The house number of the Contact.
     *
     * @var null|string
     */
    protected ?string $HouseNumber;

    /**
     * Flag indicating whether the house number of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $HouseNumberChanged = false;

    /**
     * The zip code of the Contact.
     *
     * @var int|null
     */
    protected ?int $ZipCode;

    /**
     * Flag indicating whether the zip code of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $ZipCodeChanged = false;

    /**
     * The city of the Contact.
     *
     * @var null|string
     */
    protected ?string $City;

    /**
     * Flag indicating whether the city of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $CityChanged = false;

    /**
     * The Country of the Contact.
     *
     * @var null|\vDesk\Locale\Country
     */
    protected ?Country $Country;

    /**
     * Flag indicating whether the Country of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $CountryChanged = false;

    /**
     * The contact options of the Contact.
     *
     * @var null|\vDesk\Contacts\Contact\Options
     */
    protected ?Options $Options;

    /**
     * The Company of the Contact.
     *
     * @var null|\vDesk\Contacts\Company
     */
    protected ?Company $Company;

    /**
     * Flag indicating whether the Company of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $CompanyChanged = false;

    /**
     * The annotations of the Contact.
     *
     * @var null|string
     */
    protected ?string $Annotations;

    /**
     * Flag indicating whether the annotations of a non virtual Contact has been changed.
     *
     * @var bool
     */
    public bool $AnnotationsChanged = false;

    /**
     * Initializes a new instance of the Contact class.
     *
     * @param int|null                                        $ID                Initializes the Contact with the specified ID.
     * @param \vDesk\Security\User|null              $Owner             Initializes the Contact with the specified.
     * @param int|null                               $Gender            Initializes the Contact with the specified gender.
     * @param string|null                            $Title             Initializes the Contact with the specified title.
     * @param string|null                            $Forename          Initializes the Contact with the specified forename.
     * @param string|null                            $Surname           Initializes the Contact with the specified surname.
     * @param string|null                            $Street            Initializes the Contact with the specified street.
     * @param string|null                            $HouseNumber       Initializes the Contact with the specified house number.
     * @param int|null                               $ZipCode           Initializes the Contact with the specified zip code.
     * @param string|null                            $City              Initializes the Contact with the specified city.
     * @param \vDesk\Locale\Country|null             $Country           Initializes the Contact with the specified Country.
     * @param \vDesk\Contacts\Contact\Options|null   $Options           Initializes the Contact with the specified Collection of Options.
     * @param \vDesk\Contacts\Company|null           $Company           Initializes the Contact with the specified Company.
     * @param string|null                            $Annotations       Initializes the Contact with the specified annotations.
     * @param \vDesk\Security\AccessControlList|null $AccessControlList Initializes the Contact with the specified AccessControlList.
     */
    public function __construct(
        ?int $ID = null,
        User $Owner = null,
        int $Gender = null,
        string $Title = null,
        string $Forename = null,
        string $Surname = null,
        string $Street = null,
        string $HouseNumber = null,
        int $ZipCode = null,
        string $City = null,
        Country $Country = null,
        Options $Options = null,
        Company $Company = null,
        string $Annotations = null,
        AccessControlList $AccessControlList = null
    ) {
        parent::__construct($AccessControlList);
        $this->ID          = $ID;
        $this->Owner       = $Owner;
        $this->Gender      = $Gender;
        $this->Title       = $Title;
        $this->Gender      = $Gender;
        $this->Forename    = $Forename;
        $this->Surname     = $Surname;
        $this->Street      = $Street;
        $this->HouseNumber = $HouseNumber;
        $this->ZipCode     = $ZipCode;
        $this->City        = $City;
        $this->Country     = $Country;
        $this->Options     = $Options;
        $this->Company     = $Company;
        $this->Annotations = $Annotations;
        $this->AddProperties([
            "ID"          => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Owner"       => [
                \Get => MappedGetter::Create(
                    $this->Owner,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("Owner")
                              ->From("Contacts.Contacts")
                ),
                \Set => fn(User $Value) => $this->Owner ??= $Value
            ],
            "Gender"      => [
                \Get => MappedGetter::Create(
                    $this->Gender,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Gender")
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->Gender,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->GenderChanged
                )
            ],
            "Title"       => [
                \Get => MappedGetter::Create(
                    $this->Title,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Title")
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->Title,
                    Type::String,
                    true,
                    $this->ID,
                    $this->TitleChanged
                )
            ],
            "Forename"    => [
                \Get => MappedGetter::Create(
                    $this->Forename,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Forename")
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->Forename,
                    Type::String,
                    true,
                    $this->ID,
                    $this->ForenameChanged
                )
            ],
            "Surname"     => [
                \Get => MappedGetter::Create(
                    $this->Surname,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Surname")
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->Surname,
                    Type::String,
                    false,
                    $this->ID,
                    $this->SurnameChanged
                )
            ],
            "Street"      => [
                \Get => MappedGetter::Create(
                    $this->Street,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Street")
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->Street,
                    Type::String,
                    true,
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
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->HouseNumber,
                    Type::String,
                    true,
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
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->ZipCode,
                    Type::Int,
                    true,
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
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->City,
                    Type::String,
                    true,
                    $this->ID,
                    $this->CityChanged
                )
            ],
            "Country"     => [
                \Get => MappedGetter::Create(
                    $this->HouseNumber,
                    Country::class,
                    true,
                    $this->ID,
                    Expression::Select("Country")
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->Country,
                    Country::class,
                    true,
                    $this->ID,
                    $this->CountryChanged
                )
            ],
            "Options"     => [
                \Get => fn(): ?Options => $this->Options ??= new Options([], $this),
                \Set => function(?Options $Value): void {
                    $Value->Contact = $this;
                    $this->Options  ??= $Value;
                }
            ],
            "Company"     => [
                \Get => MappedGetter::Create(
                    $this->Company,
                    Company::class,
                    true,
                    $this->ID,
                    Expression::Select("Company")
                              ->From("Contacts.Contacts"),
                    "Code"
                ),
                \Set => MappedSetter::Create(
                    $this->Company,
                    Company::class,
                    true,
                    $this->ID,
                    $this->CompanyChanged
                )
            ],
            "Annotations" => [
                \Get => MappedGetter::Create(
                    $this->Annotations,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Annotations")
                              ->From("Contacts.Contacts")
                ),
                \Set => MappedSetter::Create(
                    $this->Annotations,
                    Type::String,
                    true,
                    $this->ID,
                    $this->AnnotationsChanged
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
     * Fills the Contact with its values from the database.
     *
     * @param \vDesk\Security\User|null $User The User to determine access on the Contact.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Contact is virtual.
     *
     * @return \vDesk\Contacts\Contact The filled Contact.
     */
    public function Fill(User $User = null): Contact {
        if($this->ID === null) {
            throw new IDNullException("Cannot Fill Model without ID");
        }
        $Contact                 = Expression::Select("*")
                                             ->From("Contacts.Contacts")
                                             ->Where(["ID" => $this->ID])
                                             ->Execute()
                                             ->ToMap();
        $this->Owner             = new User((int)$Contact["Owner"]);
        $this->Gender            = (int)$Contact["Gender"];
        $this->Title             = $Contact["Title"];
        $this->Forename          = $Contact["Forename"];
        $this->Surname           = $Contact["Surname"];
        $this->Street            = $Contact["Street"];
        $this->HouseNumber       = $Contact["HouseNumber"];
        $this->ZipCode           = $Contact["ZipCode"] !== null ? (int)$Contact["ZipCode"] : null;
        $this->City              = $Contact["City"];
        $this->Country           = new Country($Contact["Country"]);
        $this->Options           = (new Options([], $this))->Fill();
        $this->Company           = new Company($Contact["Company"] !== null ? (int)$Contact["Company"] : null);
        $this->AccessControlList = new AccessControlList([], (int)$Contact["AccessControlList"]);
        parent::Fill($User);
        $this->Annotations = $Contact["Annotations"];
        return $this;
    }

    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none has been supplied.
     *
     * @param \vDesk\Security\User|null $User
     *
     */
    public function Save(User $User = null): void {
        parent::Save($User);
        if($this->ID !== null) {
            Expression::Update("Contacts.Contacts")
                      ->SetIf([
                          "Gender"      => [$this->GenderChanged => $this->Gender],
                          "Title"       => [$this->TitleChanged => $this->Title],
                          "Forename"    => [$this->ForenameChanged => $this->Forename],
                          "Surname"     => [$this->SurnameChanged => $this->Surname],
                          "Street"      => [$this->StreetChanged => $this->Street],
                          "HouseNumber" => [$this->HouseNumberChanged => $this->HouseNumber],
                          "ZipCode"     => [$this->ZipCodeChanged => $this->ZipCode],
                          "City"        => [$this->CityChanged => $this->City],
                          "Country"     => [$this->CountryChanged => $this->Country],
                          "Company"     => [$this->CompanyChanged => $this->Company],
                          "Annotations" => [$this->AnnotationsChanged => $this->Annotations]
                      ])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
            if($this->Options !== null) {
                $this->Options->Save();
            }
        } else {
            $this->ID = Expression::Insert()
                                  ->Into("Contacts.Contacts")
                                  ->Values([
                                      "ID"                => null,
                                      "Owner"             => $this->Owner,
                                      "Gender"            => $this->Gender,
                                      "Title"             => $this->Title,
                                      "Forename"          => $this->Forename,
                                      "Surname"           => $this->Surname,
                                      "Street"            => $this->Street,
                                      "HouseNumber"       => $this->HouseNumber,
                                      "ZipCode"           => $this->ZipCode,
                                      "City"              => $this->City,
                                      "Country"           => $this->Country,
                                      "Company"           => $this->Company,
                                      "Annotations"       => $this->Annotations,
                                      "AccessControlList" => $this->AccessControlList
                                  ])
                                  ->ID();
            if($this->Options !== null) {
                $this->Options->Contact = $this;
                $this->Options->Save();
            }
        }
    }

    /**
     * Deletes the Contact.
     *
     * @param \vDesk\Security\User|null $User
     */
    public function Delete(User $User = null): void {
        if($this->ID !== null) {
            parent::Delete($User);
            ($this->Properties["Options"]->Getter)()->Delete();
            Expression::Delete()
                      ->From("Contacts.Contacts")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }

    /**
     * Creates a Contact from a specified data view.
     *
     * @param array $DataView The data to use to create a Contact.
     *
     * @return \vDesk\Contacts\Contact A Contact created from the specified data view.
     */
    public static function FromDataView($DataView): Contact {
        return new static(
            $DataView["ID"] ?? null,
            new User($DataView["Owner"]),
            $DataView["Gender"] ?? 0,
            $DataView["Title"] ?? "",
            $DataView["Forename"] ?? "",
            $DataView["Surname"] ?? "",
            $DataView["Street"] ?? "",
            $DataView["HouseNumber"] ?? "",
            $DataView["ZipCode"] ?? 0,
            $DataView["City"] ?? "",
            Country::FromDataView($DataView["Country"] ?? []),
            Options::FromDataView($DataView["Options"] ?? []),
            Company::FromDataView($DataView["Company"] ?? []),
            $DataView["Annotations"] ?? "",
            AccessControlList::FromDataView($DataView["AccessControlList"] ?? [])
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
                "ID"                => $this->ID,
                "Owner"             => ($this->Owner ?? new Model())->ToDataView(true),
                "Gender"            => $this->Gender,
                "Title"             => $this->Title,
                "Forename"          => $this->Forename,
                "Surname"           => $this->Surname,
                "Street"            => $this->Street,
                "HouseNumber"       => $this->HouseNumber,
                "ZipCode"           => $this->ZipCode,
                "City"              => $this->City,
                "Country"           => ($this->Country ?? new Model())->ToDataView(true),
                "Options"           => ($this->Options ?? new Model())->ToDataView(),
                "Company"           => ($this->Company ?? new Model())->ToDataView(true),
                "Annotations"       => $this->Annotations,
                "AccessControlList" => $this->AccessControlList->ToDataView(true)
            ];
    }

    /**
     * Returns the ID of the {@link \vDesk\Security\AccessControlList} assigned to this Contact.
     *
     * @return int The ID of the {@link \vDesk\Security\AccessControlList}.
     */
    protected function GetACLID(): ?int {
        return $this->ACLID ??= $this->ID !== null
            ? (int)Expression::Select("AccessControlList")
                             ->From("Contacts.Contacts")
                             ->Where(["ID" => $this->ID])()
            : null;
    }
}
