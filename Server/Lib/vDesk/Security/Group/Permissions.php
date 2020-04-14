<?php
declare(strict_types=1);

namespace vDesk\Security\Group;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\Group;
use vDesk\Struct\Collections\Typed\Collection;
use vDesk\Struct\Collections\Typed\Dictionary;
use vDesk\Struct\Type;

/**
 * Represents a collection of {@link \vDesk\Security\Permission} objects of a {@link \vDesk\Security\Group} object.
 *
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Permissions extends Dictionary implements IDataView {
    
    /**
     * The Type of the Collection.
     */
    public const Type = Type::Boolean;
    
    /**
     * Creates a Permissions from a specified Group.
     *
     * @param \vDesk\Security\Group $Group The Group to use to create a Permissions.
     *
     * @return \vDesk\Security\Group\Permissions A Permissions created from the specified Group.
     */
    public static function FromGroup(Group $Group): Permissions {
        return new static(
            (static function() use ($Group) {
                foreach(
                    Expression::Select("*")
                              ->From("Security.Groups")
                              ->Where(["ID" => $Group->ID ?? Group::Everyone])
                              ->Execute()
                              ->ToMap()
                    as $Key => $Value
                ) {
                    if($Key === "ID" || $Key === "Name") {
                        continue;
                    }
                    yield new $Key() => (bool)$Value;
                }
            })()
        );
    }
    
    /**
     * Creates a Permissions from a specified data view.
     *
     * @param array $DataView The data to use to create a Permissions.
     *
     * @return \vDesk\Security\Group\Permissions A Permissions created from the specified data view.
     */
    public static function FromDataView($DataView = []): Permissions {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Name => $Value) {
                    yield new $Name() => $Value;
                }
            })()
        );
    }
    
    /**
     * Generates a JSON-encodable representation of the Permissions.
     *
     * @return mixed The JSON-encodable representation of the Permissions.
     */
    public function ToDataView(): array {
        return $this->Reduce(
            static function(array $Permissions, bool $Value, string $Permission): array {
                $Permissions[$Permission] = $Value;
                return $Permissions;
            },
            []
        );
    }
    
}
