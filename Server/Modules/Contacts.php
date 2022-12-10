<?php
declare(strict_types=1);

namespace Modules;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\Expression\Functions;
use vDesk\Modules\Command;
use vDesk\Contacts\Companies;
use vDesk\Contacts\CompaniesView;
use vDesk\Contacts\Company;
use vDesk\Contacts\Contact;
use vDesk\Contacts\Contact\Options;
use vDesk\Contacts\Contact\Deleted;
use vDesk\Contacts\Contact\Updated;
use vDesk\Locale\Country;
use vDesk\Modules\Module;
use vDesk\Search\Results;
use vDesk\Search\ISearch;
use vDesk\Search\Result;
use vDesk\Security\AccessControlList;
use vDesk\Security\AccessControlList\Entry;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Utils\Log;

/**
 * Contacts Module.
 *
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @package vDesk\Contacts
 */
final class Contacts extends Module implements ISearch {

    /**
     * Filter for searching contacts.
     */
    public const FilterContact = "Contact";

    /**
     * Filter for searching companies.
     */
    public const FilterCompany = "Company";

    /**
     * Gets a filled Contact.
     *
     * @param null|int $ID The ID of the Contact.
     *
     * @return Contact A new Contact filled with database values.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have read-permissions on the Contact to get.
     */
    public static function GetContact(int $ID = null): Contact {
        $Contact = (new Contact($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!$Contact->AccessControlList->Read) {
            throw new UnauthorizedAccessException();
        }
        return $Contact;
    }

    /**
     * Gets a subset of all existing Contacts.
     *
     * @param null|string $Char   The starting character of the surname of the Contact to get.
     * @param null|int    $Offset The offset to start to get the Contacts from.
     * @param null|int    $Amount The amount of Contacts to get.
     *
     * @return \vDesk\Contacts\Contacts A Collection of Contacts whose surnames staring with the specified character.
     */
    public static function GetContacts(string $Char = null, int $Offset = null, int $Amount = null): \vDesk\Contacts\Contacts {
        return \vDesk\Contacts\Contacts::StartsWith(
            $Char ?? Command::$Parameters["Char"],
            $Amount ?? Command::$Parameters["Amount"],
            $Offset ?? Command::$Parameters["Offset"]
        );
    }

    /**
     * Creates a new Contact.
     * Triggers the {@link \vDesk\Contacts\ContactAdded}-Event for the created Contact.
     *
     * @param \vDesk\Security\User|null    $Owner       The owner of the Contact to create.
     * @param int|null                     $Gender      The gender of the Contact to create.
     * @param string|null                  $Title       The title of the Contact to create.
     * @param string|null                  $Forename    The forename of the Contact to create.
     * @param string|null                  $Surname     The surname of the Contact to create.
     * @param string|null                  $Street      The street of the Contact to create.
     * @param string|null                  $HouseNumber The house number of the Contact to create.
     * @param int|null                     $ZipCode     The zip code of the Contact to create.
     * @param string|null                  $City        The city of the Contact to create.
     * @param \vDesk\Locale\Country|null   $Country     The Country of the Contact to create.
     * @param \vDesk\Contacts\Company|null $Company     The Company of the Contact to create.
     * @param string|null                  $Annotations The annotations of the Contact to create.
     *
     * @return \vDesk\Contacts\Contact The new created Contact.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to create Contacts.
     */
    public static function CreateContact(
        User    $Owner = null,
        int     $Gender = null,
        string  $Title = null,
        string  $Forename = null,
        string  $Surname = null,
        string  $Street = null,
        string  $HouseNumber = null,
        int     $ZipCode = null,
        string  $City = null,
        Country $Country = null,
        Company $Company = null,
        string  $Annotations = null
    ): Contact {
        if(!User::$Current->Permissions["CreateContact"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to create a new Contact without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Contact = new Contact(
            null,
            $Owner ?? User::$Current,
            $Gender ?? Command::$Parameters["Gender"],
            $Title ?? Command::$Parameters["Title"],
            $Forename ?? Command::$Parameters["Forename"],
            $Surname ?? Command::$Parameters["Surname"],
            $Street ?? Command::$Parameters["Street"],
            $HouseNumber ?? Command::$Parameters["HouseNumber"],
            $ZipCode ?? Command::$Parameters["ZipCode"],
            $City ?? Command::$Parameters["City"],
            $Country ?? new Country(Command::$Parameters["Country"]),
            null,
            $Company ?? new Company(Command::$Parameters["Company"]),
            $Annotations ?? Command::$Parameters["Annotations"],
            new AccessControlList([
                Entry::FromUser(),
                Entry::FromGroup(null, false, false, false),
                Entry::FromUser($Owner ?? User::$Current)
            ])
        );
        $Contact->Save();
        (new Contact\Created($Contact))->Dispatch();
        return $Contact;
    }

    /**
     * Updates a Contact.
     * Triggers the {@link \vDesk\Contacts\Contact\Updated}-Event for the updated Contact.
     *
     * @param int|null                     $ID          The ID of the Contact to update.
     * @param int|null                     $Gender      The new gender of the Contact to update.
     * @param string|null                  $Title       The new title of the Contact to update.
     * @param string|null                  $Forename    The new forename of the Contact to update.
     * @param string|null                  $Surname     The new surname of the Contact to update.
     * @param string|null                  $Street      The new street of the Contact to update.
     * @param string|null                  $HouseNumber The new house number of the Contact to update.
     * @param int|null                     $ZipCode     The new zip code of the Contact to update.
     * @param string|null                  $City        The new city of the Contact to update.
     * @param \vDesk\Locale\Country|null   $Country     The new Country of the Contact to update.
     * @param \vDesk\Contacts\Company|null $Company     The new Company of the Contact to update.
     * @param string|null                  $Annotations The new annotations of the Contact to update.
     *
     * @return \vDesk\Contacts\Contact The updated Contact.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Contacts.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write-permissions on the Contact to update.
     */
    public static function UpdateContact(
        int     $ID = null,
        int     $Gender = null,
        string  $Title = null,
        string  $Forename = null,
        string  $Surname = null,
        string  $Street = null,
        string  $HouseNumber = null,
        int     $ZipCode = null,
        string  $City = null,
        Country $Country = null,
        Company $Company = null,
        string  $Annotations = null
    ): Contact {
        $Contact = (new Contact($ID ?? Command::$Parameters["ID"]))->Fill();
        if(!User::$Current->Permissions["UpdateContact"] || !$Contact->AccessControlList->Write) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to update a Contact without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Contact->Gender      = $Gender ?? Command::$Parameters["Gender"];
        $Contact->Title       = $Title ?? Command::$Parameters["Title"];
        $Contact->Forename    = $Forename ?? Command::$Parameters["Forename"];
        $Contact->Surname     = $Surname ?? Command::$Parameters["Surname"];
        $Contact->Street      = $Street ?? Command::$Parameters["Street"];
        $Contact->HouseNumber = $HouseNumber ?? Command::$Parameters["HouseNumber"];
        $Contact->ZipCode     = $ZipCode ?? Command::$Parameters["ZipCode"];
        $Contact->City        = $City ?? Command::$Parameters["City"];
        $Contact->Country     = $Country ?? new Country(Command::$Parameters["Country"]);
        $Contact->Company     = $Company ?? new Company(Command::$Parameters["Company"]);
        $Contact->Annotations = $Annotations ?? Command::$Parameters["Annotations"];
        $Contact->Save();
        (new Updated($Contact))->Dispatch();
        return $Contact;
    }

    /**
     * Updates the Options of a Contact.
     * Triggers the {@link \vDesk\Contacts\Contact\Updated} Event for the updated Contact.
     *
     * @param int|null   $ID     The ID of the Contact to update the Options of.
     * @param array|null $Add    The Options to add.
     * @param array|null $Update The Options to update.
     * @param array|null $Delete The IDs of the Options to delete.
     *
     * @return \vDesk\Contacts\Contact\Options A Collection containing the updated Options of the Contact.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Contacts.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have write-permissions on the Contact to update.
     */
    public static function SetContactOptions(int $ID = null, array $Add = null, array $Update = null, array $Delete = null): Options {
        $Contact = new Contact($ID ?? Command::$Parameters["ID"]);
        $Contact->Options->Fill();
        if(!User::$Current->Permissions["UpdateContact"] || !$Contact->AccessControlList->Write) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to update the Options of a Contact without having permissions.");
            throw new UnauthorizedAccessException();
        }

        //Add new options.
        foreach($Add ?? Command::$Parameters["Add"] as $Added) {
            $Contact->Options->Add(new Contact\Option(null, $Added["Type"], $Added["Value"]));
        }

        //Update changed options.
        foreach($Update ?? Command::$Parameters["Update"] as $Updated) {
            $Option        = $Contact->Options->Find(static fn(Contact\Option $Option): bool => $Option->ID === $Updated->ID);
            $Option->Value = $Updated->Value;
        }

        //Delete removed options.
        foreach($Delete ?? Command::$Parameters["Delete"] as $Deleted) {
            $Contact->Options->Remove($Contact->Options->Find(static fn(Contact\Option $Option): bool => $Option->ID === $Deleted));
        }
        $Contact->Options->Save();
        (new Updated($Contact))->Dispatch();
        return $Contact->Options;
    }

    /**
     * Deletes a Contact.
     * Triggers the {@link \vDesk\Contacts\Contact\Deleted}-Event for the deleted Contact.
     *
     * @param null|int $ID The ID of the Contact to delete.
     *
     * @return boolean True if the Contact has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to delete Contacts.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have delete-permissions on the Contact to delete.
     */
    public static function DeleteContact(int $ID = null): bool {
        $Contact = new Contact($ID ?? Command::$Parameters["ID"]);
        if(!User::$Current->Permissions["DeleteContact"] || !$Contact->AccessControlList->Delete) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to delete a Contact without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Contact->Delete();
        (new Deleted($Contact))->Dispatch();
        return true;
    }

    /**
     * Gets a filled Company Contact.
     *
     * @param null|int $ID The ID of the Company Contact.
     *
     * @return \vDesk\Contacts\Company A new Company Contact filled with database values.
     */
    public static function GetCompany(int $ID = null): Company {
        return (new Company($ID ?? Command::$Parameters["ID"]))->Fill();
    }

    /**
     * Gets a Collection of views off all existing {@link \vDesk\Contacts\Company} objects.
     *
     * @return CompaniesView A Collection of views on all existing {@link \vDesk\Contacts\Company} objects.
     */
    public static function GetCompanyViews(): Companies {
        return CompaniesView::All();
    }

    /**
     * Gets a subset of all existing Companies.
     *
     * @param null|string $Char   The starting character of the name of the Companies to get.
     * @param null|int    $Offset The offset to start to get the Companies from.
     * @param null|int    $Amount The amount of Companies to get.
     *
     * @return \vDesk\Contacts\Companies A Collection of Companies whose names staring with the specified character.
     */
    public static function GetCompanies(string $Char = null, int $Offset = null, int $Amount = null): Companies {
        return Companies::StartsWith(
            $Char ?? Command::$Parameters["Char"],
            $Amount ?? Command::$Parameters["Amount"],
            $Offset ?? Command::$Parameters["Offset"]
        );
    }

    /**
     * Creates a new Company Contact.
     * Triggers the {@link \vDesk\Contacts\Company\Created}-Event for the created Company Contact.
     *
     * @param string|null                $Name        The name of the Company Contact to create.
     * @param string|null                $Street      The street of the Company Contact to create.
     * @param string|null                $HouseNumber The house number of the Company Contact to create.
     * @param int|null                   $ZipCode     The zip code of the Company Contact to create.
     * @param string|null                $City        The city of the Company Contact to create.
     * @param \vDesk\Locale\Country|null $Country     The code of the Country of the Company Contact to create.
     * @param string|null                $PhoneNumber The phone number of the Company Contact to create.
     * @param string|null                $FaxNumber   The fax number of the Company Contact to create.
     * @param string|null                $Email       The email-address of the Company Contact to create.
     * @param string|null                $Website     The website of the Company Contact to create.
     *
     * @return \vDesk\Contacts\Company The new created Company Contact.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to create Company Contacts.
     */
    public static function CreateCompany(
        string  $Name = null,
        string  $Street = null,
        string  $HouseNumber = null,
        int     $ZipCode = null,
        string  $City = null,
        Country $Country = null,
        string  $PhoneNumber = null,
        string  $FaxNumber = null,
        string  $Email = null,
        string  $Website = null
    ): Company {
        if(!User::$Current->Permissions["CreateCompany"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to create a new Company without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Company = new Company(
            null,
            $Name ?? Command::$Parameters["Name"],
            $Street ?? Command::$Parameters["Street"],
            $HouseNumber ?? Command::$Parameters["HouseNumber"],
            $ZipCode ?? Command::$Parameters["ZipCode"],
            $City ?? Command::$Parameters["City"],
            $Country ?? new Country(Command::$Parameters["Country"]),
            $PhoneNumber ?? Command::$Parameters["PhoneNumber"],
            $FaxNumber ?? Command::$Parameters["FaxNumber"],
            $Email ?? Command::$Parameters["Email"],
            $Website ?? Command::$Parameters["Website"]
        );
        $Company->Save();
        (new Company\Created($Company))->Dispatch();
        return $Company;
    }

    /**
     * Updates a Company Contact.
     * Triggers the {@link \vDesk\Contacts\Company\Updated}-Event for the updated Company Contact.
     *
     * @param int|null                   $ID          The ID of the Company Contact to update.
     * @param string|null                $Name        The new name of the Company Contact to update.
     * @param string|null                $Street      The new street of the Company Contact to update.
     * @param string|null                $HouseNumber The new house number of the Company Contact to update.
     * @param int|null                   $ZipCode     The new zip code of the Company Contact to update.
     * @param string|null                $City        The new city of the Company Contact to update.
     * @param \vDesk\Locale\Country|null $Country     The new code of the Country of the Company Contact to update.
     * @param string|null                $PhoneNumber The new phone number of the Company Contact to update.
     * @param string|null                $FaxNumber   The new fax number of the Company Contact to update.
     * @param string|null                $Email       The new email  of the Company Contact to update.
     * @param string|null                $Website     The new website of the Company Contact to update.
     *
     * @return \vDesk\Contacts\Company The updated Company Contact.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to update Company Contacts.
     */
    public static function UpdateCompany(
        int     $ID = null,
        string  $Name = null,
        string  $Street = null,
        string  $HouseNumber = null,
        int     $ZipCode = null,
        string  $City = null,
        Country $Country = null,
        string  $PhoneNumber = null,
        string  $FaxNumber = null,
        string  $Email = null,
        string  $Website = null
    ): Company {
        if(!User::$Current->Permissions["UpdateCompany"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to update a Company without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Company              = (new Company($ID ?? Command::$Parameters["ID"]))->Fill();
        $Company->Name        = $Name ?? Command::$Parameters["Name"];
        $Company->Street      = $Street ?? Command::$Parameters["Street"];
        $Company->HouseNumber = $HouseNumber ?? Command::$Parameters["HouseNumber"];
        $Company->ZipCode     = $ZipCode ?? Command::$Parameters["ZipCode"];
        $Company->City        = $City ?? Command::$Parameters["City"];
        $Company->Country     = new Country($Country ?? Command::$Parameters["Country"]);
        $Company->PhoneNumber = $PhoneNumber ?? Command::$Parameters["PhoneNumber"];
        $Company->FaxNumber   = $FaxNumber ?? Command::$Parameters["FaxNumber"];
        $Company->Email       = $Email ?? Command::$Parameters["Email"];
        $Company->Website     = $Website ?? Command::$Parameters["Website"];
        $Company->Save();
        (new Company\Updated($Company))->Dispatch();
        return $Company;
    }

    /**
     * Deletes a Company Contact.
     * Triggers the {@link \vDesk\Contacts\CompanyDeleted}-Event for the deleted Company Contact.
     *
     * @param null|int $ID The ID of the Company Contact to delete.
     *
     * @return boolean True if the Company Contact has been successfully deleted.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to delete Company Contacts.
     */
    public static function DeleteCompany(int $ID = null): bool {
        if(!User::$Current->Permissions["DeleteCompany"]) {
            Log::Warn(__METHOD__, User::$Current->Name . " tried to delete a Company without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Company = new Company($ID ?? Command::$Parameters["ID"]);
        $Company->Delete();
        (new Company\Deleted($Company))->Dispatch();
        return true;
    }

    /**
     * Searches the database for Contacts or Company Contacts matching a specified name.
     *
     * @param string      $Value  The name to search for.
     * @param null|string $Filter A filter to apply on the search result.
     *
     * @return \vDesk\Search\Results The found results.
     */
    public static function Search(string $Value, string $Filter = null): Results {
        $Results = new Results();
        switch($Filter) {
            case self::FilterContact:
                foreach(
                    Expression::Select("ID", "Forename", "Surname", "AccessControlList")
                              ->From("Contacts.Contacts")
                              ->Where(["Surname" => ["LIKE" => "%{$Value}%"]])
                    as
                    $Contact
                ) {
                    if((new AccessControlList([], (int)$Contact["AccessControlList"]))->Read) {
                        $Results->Add(
                            new Result(
                                $Contact["Surname"],
                                "Contact",
                                [
                                    "ID"       => (int)$Contact["ID"],
                                    "Forename" => $Contact["Forename"],
                                    "Surname"  => $Contact["Surname"]
                                ]
                            )
                        );
                    }
                }
                break;
            case self::FilterCompany:
                foreach(
                    Expression::Select("ID", "Name")
                              ->From("Contacts.Companies")
                              ->Where(["Name" => ["LIKE" => "%{$Value}%"]])
                    as
                    $Company
                ) {
                    $Results->Add(
                        new Result(
                            $Company["Name"],
                            "Company",
                            [
                                "ID"   => (int)$Company["ID"],
                                "Name" => $Company["Name"]
                            ]
                        )
                    );
                }
                break;
        }
        return $Results;
    }

    /** @inheritDoc */
    public static function Status(): ?array {
        return [
            "ContactCount" => Expression::Select(Functions::Count("*"))
                                        ->From("Contacts.Contacts")(),
            "CompanyCount" => Expression::Select(Functions::Count("*"))
                                        ->From("Contacts.Companies")()
        ];
    }

}