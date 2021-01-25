<?php
declare(strict_types=1);

namespace vDesk\Contacts;

/**
 * Represents a view on a collection of {@link \vDesk\Contacts\Company} objects.
 *
 * @package vDesk\Contacts
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class CompaniesView extends Companies {
    
    /**
     * Returns a JSON-encodable representation of the Companies.
     *
     * @return array Companies.
     */
    public function ToDataView(): array {
        return $this->Reduce(static function(array $Companies, Company $Company): array {
            $Companies[] = ["ID" => $Company->ID, "Name" => $Company->Name];
            return $Companies;
        },
            []);
    }
    
}
