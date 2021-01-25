<?php
declare(strict_types=1);

namespace vDesk\Security;

/**
 * Represents a view on a collection of {@link \vDesk\Security\Group} objects.
 *
 * @property-read int $Count Gets the amount of elements in the Collection<float>.
 *
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class GroupsView extends Groups {
    
    /**
     * Returns a JSON-encodable representation of the GroupsView.
     *
     * @return array An array of shallow copies of the Groups of the GroupsView.
     */
    public function ToDataView(): array {
        return $this->Reduce(static function(array $Groups, Group $Group): array {
            $Groups[] = ["ID" => $Group->ID, "Name" => $Group->Name];
            return $Groups;
        },
            []);
    }
    
}
