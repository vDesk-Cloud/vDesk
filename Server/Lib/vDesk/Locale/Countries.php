<?php
declare(strict_types=1);

namespace vDesk\Locale;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of all existing countries.
 *
 * @package Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Countries extends Collection implements IDataView {

    /**
     * The Type of the Countries.
     */
    public const Type = Country::class;

    /**
     * Fetches a Collection containing all existing Countries yielding the translated name of a specified locale.
     *
     * @param string $Locale The locale of the Countries to fetch.
     *
     * @return \vDesk\Locale\Countries A Collection containing all existing Countries yielding the translated name according the specified locale.
     */
    public static function All(string $Locale = "EN"): Countries {
        return new static(
            (static function() use ($Locale): \Generator {
                foreach(
                    Expression::Select("*")
                              ->From("Locale.Countries")
                              ->Where(["Locale" => $Locale])
                    as
                    $Country
                ) {
                    yield new Country($Country["Code"], $Country["Name"]);
                }
            })()
        );
    }

    /**
     * Returns a JSON-encodable representation of the Countries.
     *
     * @return array
     */
    public function ToDataView(): array {
        return $this->Reduce(static function(array $Countries, Country $Country): array {
            $Countries[] = $Country->ToDataView();
            return $Countries;
        }, []);
    }

    /**
     * Creates an IDataView from a JSON-encodable representation.
     *
     * @param mixed $DataView The Data to use to create an instance of the IDataView. The type and format should match the output of @see
     *                        \vDesk\Data\IDataView::ToDataView().
     *
     * @return \vDesk\Data\IDataView An instance of the implementing class filled with the provided data.
     */
    public static function FromDataView($DataView): IDataView {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Country::FromDataView($Data);
                }
            })()
        );
    }

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Country {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): Country {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Country {
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Country {
        return parent::offsetGet($Index);
    }
}
