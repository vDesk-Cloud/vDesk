<?php
declare(strict_types=1);

namespace vDesk\Contacts;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\IResult;
use vDesk\Data\IDataView;
use vDesk\Locale\Country;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a Collection of {@link \vDesk\Contacts\Company} objects.
 *
 * @package Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Companies extends Collection implements IDataView {

    /**
     * The type of the Companies.
     */
    public const Type = Company::class;

    /**
     * Initializes a new instance of the Companies class.
     *
     * @param iterable|null $Elements Initializes the Companies with the specified set of elements.
     * @param bool          $AutoFill Determines whether the Companies should be filled by creation.
     */
    public function __construct(?iterable $Elements = [], bool $AutoFill = false) {

        parent::__construct($Elements);

        if($AutoFill) {
            $this->Fill();
        }
    }

    /**
     * Fetches a collection containing all existing {@link \vDesk\Contacts\Company} companies.
     *
     * @return \vDesk\Contacts\Companies A collection containing all existing {@link \vDesk\Contacts\Company} companies.
     */
    public static function All(): self {
        return new static(static::FetchCompanies());
    }

    /**
     * Creates a new Companies containing all Companies whose name starts with a specified alphabetical letter.
     *
     * @param string $Char   The letter to search.
     * @param int    $Amount The amount of Companies to fetch.
     * @param int    $Offset The offset to start from.
     *
     * @return \vDesk\Contacts\Companies
     */
    public static function StartsWith(string $Char, int $Amount = 100, int $Offset = 0): Companies {
        $ASCIIValue = \ord(\strtoupper($Char));
        return new static(
            (static function() use ($ASCIIValue, $Char, $Amount, $Offset): \Generator {
                foreach(
                    Expression::Select("*")
                              ->From("Contacts.Companies")
                              ->Where([
                                  "Name" => $ASCIIValue > 64 && $ASCIIValue < 91
                                      ? ["LIKE" => "{$Char}%"]
                                      : ["NOT REGEXP" => "^[A-Za-z]"]

                              ])
                              ->Limit($Amount)
                              ->Offset($Offset)
                    as
                    $Company
                ) {
                    yield new Company(
                        (int)$Company["ID"],
                        $Company["Name"],
                        $Company["Street"],
                        $Company["HouseNumber"],
                        (int)$Company["ZipCode"],
                        $Company["City"],
                        new Country($Company["Country"]),
                        $Company["PhoneNumber"],
                        $Company["FaxNumber"],
                        $Company["Email"],
                        $Company["Website"]
                    );
                }
            })()
        );
    }

    /**
     * Fetches all existing {@link \vDesk\Contacts\Company} companies.
     *
     * @return \Generator A generator that yields all existing {@link \vDesk\Contacts\Company} companies.
     */
    protected static function FetchCompanies(): \Generator {
        foreach(
            Expression::Select("*")
                      ->From("Contacts.Companies")
            as
            $Company
        ) {
            yield new Company(
                (int)$Company["ID"],
                $Company["Name"],
                $Company["Street"],
                $Company["HouseNumber"],
                (int)$Company["ZipCode"],
                $Company["City"],
                new Country($Company["Country"]),
                $Company["PhoneNumber"],
                $Company["FaxNumber"],
                $Company["Email"],
                $Company["Website"]
            );
        }
    }

    /**
     * Fills the collection with all available companies.
     */
    public function Fill(): void {
        if($this->Count > 0) {
            $this->Clear();
        }
        foreach(static::FetchCompanies() as $Company) {
            $this->Add($Company);
        }
    }

    /**
     * Creates a data view of the Companies.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Company.
     *
     * @return array The data view representing the current state of the Companies.
     */
    public function ToDataView(): array {
        return $this->Reduce(static function(array $Companies, Company $Company): array {
            $Companies[] = $Company->ToDataView();
            return $Companies;
        }, []);
    }

    /**
     * Creates a Companies from a specified data view.
     *
     * @param array $DataView The data to use to create a Companies.
     *
     * @return \vDesk\Contacts\Companies A Companies created from the specified data view.
     */
    public static function FromDataView($DataView): Companies {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Company::FromDataView($Data);
                }
            })()
        );
    }
}
