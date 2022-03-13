<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Collation;
use vDesk\DataProvider\Type;
use vDesk\Locale;
use vDesk\Events;
use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Security\AccessControlList;
use vDesk\Struct\Collections\Observable\Collection;

/**
 * Contacts Package manifest class.
 *
 * @package vDesk\Contacts
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Contacts extends Package implements Locale\IPackage, Events\IPackage {
    
    /**
     * The name of the Package.
     */
    public const Name = "Contacts";
    
    /**
     * The version of the Package.
     */
    public const Version = "1.0.1";
    
    /**
     * The name of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";
    
    /**
     * The name of the Package.
     */
    public const Description = "Package providing functionality for organizing personal and business contacts.";
    
    /**
     * The dependencies of the Package.
     */
    public const Dependencies = [
        "Events"   => "1.0.0",
        "Locale"   => "1.0.0",
        "Security" => "1.0.2",
        "Search"   => "1.0.0"
    ];
    
    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Client => [
            self::Design  => [
                "vDesk/Contacts.css",
                "vDesk/Contacts"
            ],
            self::Modules => [
                "Contacts.js"
            ],
            self::Lib     => [
                "vDesk/Contacts.js",
                "vDesk/Contacts"
            ]
        ],
        self::Server => [
            self::Modules => [
                "Contacts.php"
            ],
            self::Lib     => [
                "vDesk/Contacts"
            ]
        ]
    ];
    
    /**
     * The eventlisteners of the Package.
     */
    public const Events = [
        "vDesk.Security.User.Deleted" => "/vDesk/Contacts/vDesk.Security.User.Deleted.php"
    ];
    
    /**
     * The translations of the Package.
     */
    public const Locale = [
        "DE" => [
            "Contacts"    => [
                "AddContactOption"    => "Kontaktmöglichkeit hinzufügen.",
                "Address"             => "Adresse",
                "Annotations"         => "Anmerkungen",
                "City"                => "Stadt",
                "Companies"           => "Firmen",
                "Company"             => "Firma",
                "CompanyContact"      => "Firmenkontakt",
                "Contact"             => "Kontakt",
                "ContactOptions"      => "Kontaktmöglichkeiten",
                "Country"             => "Land",
                "DeleteContactOption" => "Kontaktmöglichkeit entfernen.",
                "EditCompany"         => "Firmenkontakt bearbeiten",
                "EditContact"         => "Kontakt bearbeiten",
                "Email"               => "E-Mail-Addresse",
                "FaxNumber"           => "Faxnummer",
                "Forename"            => "Vorname",
                "Gender"              => "Anrede",
                "GenderFemale"        => "Frau",
                "GenderMale"          => "Herr",
                "HouseNumber"         => "Hausnummer",
                "Module"              => "Kontakte",
                "Name"                => "Name",
                "NewCompany"          => "Neuer Firmenkontakt",
                "NewContact"          => "Neuer Kontakt",
                "PhoneNumber"         => "Telefonnummer",
                "Street"              => "Straße",
                "Surname"             => "Nachname",
                "Website"             => "Webseite",
                "ZipCode"             => "Postleitzahl",
                "ContactCount"        => "Anzahl persönlicher Kontakte",
                "CompanyCount"        => "Anzahl Unternehmens-Kontakte"
            ],
            "Permissions" => [
                "CreateCompany" => "Legt fest ob Mitglieder der Gruppe neue Unternehmenskontakte erstellen können",
                "CreateContact" => "Legt fest ob Mitglieder der Gruppe neue Kontakte erstellen können",
                "DeleteCompany" => "Legt fest ob Mitglieder der Gruppe Unternehmenskontakte löschen können",
                "DeleteContact" => "Legt fest ob Mitglieder der Gruppe Kontakte löschen können",
                "UpdateCompany" => "Legt fest ob Mitglieder der Gruppe Unternehmenskontakte bearbeiten können",
                "UpdateContact" => "Legt fest ob Mitglieder der Gruppe Kontakte bearbeiten können"
            ]
        ],
        "EN" => [
            "Contacts"    => [
                "AddContactOption"    => "Add contact option.",
                "Address"             => "Address",
                "Annotations"         => "Annotations",
                "City"                => "City",
                "Companies"           => "Companies",
                "Company"             => "Company",
                "CompanyContact"      => "Company contact",
                "Contact"             => "Contact",
                "ContactOptions"      => "Contact options",
                "Country"             => "Country",
                "DeleteContactOption" => "Delete contact option",
                "EditCompany"         => "Edit company contact",
                "EditContact"         => "Edit contact",
                "Email"               => "Email address",
                "FaxNumber"           => "Fax number",
                "Forename"            => "Forename",
                "Gender"              => "Salutation",
                "GenderFemale"        => "Mrs.",
                "GenderMale"          => "Mr.",
                "HouseNumber"         => "House number",
                "Module"              => "Contacts",
                "Name"                => "Name",
                "NewCompany"          => "New company contact",
                "NewContact"          => "New contact",
                "PhoneNumber"         => "Phone number",
                "Street"              => "Street",
                "Surname"             => "Surname",
                "Website"             => "Website",
                "ZipCode"             => "Zip code",
                "ContactCount"        => "Amount of personal contacts",
                "CompanyCount"        => "Amount of business contacts"
            ],
            "Permissions" => [
                "CreateCompany" => "Determines whether members of the group are allowed to create new company contacts",
                "CreateContact" => "Determines whether members of the group are allowed to create new contacts",
                "DeleteCompany" => "Determines whether members of the group are allowed to delete company contacts",
                "DeleteContact" => "Determines whether members of the group are allowed to delete contacts",
                "UpdateCompany" => "Determines whether members of the group are allowed to update company contacts",
                "UpdateContact" => "Determines whether members of the group are allowed to update contacts"
            
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        
        Expression::Create()
                  ->Schema("Contacts")
                  ->Execute();
        
        //Create tables.
        Expression::Create()
                  ->Table(
                      "Contacts.Contacts",
                      [
                          "ID"                => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Owner"             => ["Type" => Type::BigInt | Type::Unsigned],
                          "Gender"            => ["Type" => Type::Boolean],
                          "Title"             => ["Type" => Type::TinyText, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "Forename"          => ["Type" => Type::TinyText, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "Surname"           => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Street"            => ["Type" => Type::TinyText, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "HouseNumber"       => ["Type" => Type::TinyText, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "ZipCode"           => ["Type" => Type::BigInt | Type::Unsigned, "Size" => 10, "Nullable" => true, "Default" => null],
                          "City"              => ["Type" => Type::TinyText, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "Country"           => ["Type" => Type::Char, "Size" => 2, "Collation" => Collation::ASCII, "Nullable" => true, "Default" => null],
                          "Company"           => ["Type" => Type::BigInt | Type::Unsigned, "Nullable" => true, "Default" => null],
                          "AccessControlList" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Annotations"       => ["Type" => Type::Text, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID"]],
                          "Surname" => ["Fields" => ["Surname" => 4]]
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Contacts.Options",
                      [
                          "ID"      => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Contact" => ["Type" => Type::BigInt | Type::Unsigned],
                          "Type"    => ["Type" => Type::TinyInt | Type::Unsigned, "Size" => 2],
                          "Value"   => ["Type" => Type::Text, "Collation" => Collation::UTF8]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID"]],
                          "Contact" => ["Fields" => ["Contact"]],
                      ]
                  )
                  ->Execute();
        Expression::Create()
                  ->Table(
                      "Contacts.Companies",
                      [
                          "ID"          => ["Type" => Type::BigInt | Type::Unsigned, "Autoincrement" => true],
                          "Name"        => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Street"      => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "HouseNumber" => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "ZipCode"     => ["Type" => Type::BigInt | Type::Unsigned, "Size" => 10],
                          "City"        => ["Type" => Type::TinyText, "Collation" => Collation::UTF8],
                          "Country"     => ["Type" => Type::Char, "Size" => 2, "Collation" => Collation::ASCII],
                          "PhoneNumber" => ["Type" => Type::Text, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "FaxNumber"   => ["Type" => Type::Text, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "Email"       => ["Type" => Type::Text, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null],
                          "Website"     => ["Type" => Type::Text, "Collation" => Collation::UTF8, "Nullable" => true, "Default" => null]
                      ],
                      [
                          "Primary" => ["Fields" => ["ID"]],
                          "Name"    => ["Fields" => ["Name" => 4]]
                      ]
                  )
                  ->Execute();
        
        //Install Module.
        /** @var \Modules\Contacts $Contacts */
        $Contacts = \vDesk\Modules::Contacts();
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "GetContact",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "GetContacts",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Char", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Offset", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Amount", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "CreateContact",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Gender", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Title", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Forename", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Surname", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Street", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "HouseNumber", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "ZipCode", \vDesk\Struct\Type::Int, false, true),
                    new Parameter(null, null, "City", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Country", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Company", \vDesk\Struct\Type::Int, false, true),
                    new Parameter(null, null, "Annotations", \vDesk\Struct\Type::String, false, true)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "UpdateContact",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Gender", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Title", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Forename", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Surname", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Street", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "HouseNumber", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "ZipCode", \vDesk\Struct\Type::Int, false, true),
                    new Parameter(null, null, "City", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Country", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Company", \vDesk\Struct\Type::Int, false, true),
                    new Parameter(null, null, "Annotations", \vDesk\Struct\Type::String, false, true)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "SetContactOptions",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Add", \vDesk\Struct\Type::Array, false, false),
                    new Parameter(null, null, "Update", \vDesk\Struct\Type::Array, false, false),
                    new Parameter(null, null, "Delete", \vDesk\Struct\Type::Array, false, false)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "DeleteContact",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "GetCompany",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "GetCompanyViews",
                true,
                false
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "GetCompanies",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Char", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Offset", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Amount", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "CreateCompany",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Street", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "HouseNumber", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "ZipCode", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "City", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Country", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "PhoneNumber", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "FaxNumber", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Email", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Website", \vDesk\Struct\Type::String, false, true)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "UpdateCompany",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "Name", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Street", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "HouseNumber", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "ZipCode", \vDesk\Struct\Type::Int, false, false),
                    new Parameter(null, null, "City", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "Country", \vDesk\Struct\Type::String, false, false),
                    new Parameter(null, null, "PhoneNumber", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "FaxNumber", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Email", \vDesk\Struct\Type::String, false, true),
                    new Parameter(null, null, "Website", \vDesk\Struct\Type::String, false, true)
                ])
            )
        );
        $Contacts->Commands->Add(
            new Command(
                null,
                $Contacts,
                "DeleteCompany",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "ID", \vDesk\Struct\Type::Int, false, false)
                ])
            )
        );
        $Contacts->Save();
        
        //Create permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::CreatePermission("CreateContact", true);
        $Security::CreatePermission("UpdateContact", true);
        $Security::CreatePermission("DeleteContact", true);
        $Security::CreatePermission("CreateCompany", true);
        $Security::CreatePermission("UpdateCompany", true);
        $Security::CreatePermission("DeleteCompany", true);
        
        //Extract files.
        self::Deploy($Phar, $Path);
        
    }
    
    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {
        
        //Uninstall Module.
        /** @var \Modules\Contacts $Contacts */
        $Contacts = \vDesk\Modules::Contacts();
        $Contacts->Delete();
        
        //Delete ACLs
        foreach(
            Expression::Select("AccessControlList")
                      ->From("Contacts.Contacts")
            as
            $Contact
        ) {
            $AccessControlList = new AccessControlList([], (int)$Contact["AccessControlList"]);
            $AccessControlList->Delete();
        }
        
        //Delete permissions.
        /** @var \Modules\Security $Security */
        $Security = \vDesk\Modules::Security();
        $Security::DeletePermission("CreateContact");
        $Security::DeletePermission("UpdateContact");
        $Security::DeletePermission("DeleteContact");
        $Security::DeletePermission("CreateCompany");
        $Security::DeletePermission("UpdateCompany");
        $Security::DeletePermission("DeleteCompany");
        
        //Drop database.
        Expression::Drop()
                  ->Schema("Contacts")
                  ->Execute();
        
        //Delete files.
        self::Undeploy();
        
    }
}