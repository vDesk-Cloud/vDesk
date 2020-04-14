<?php
declare(strict_types=1);

namespace vDesk\Security;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\Group\Permissions;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of {@link \vDesk\Security\Group} objects.
 *
 * @property-read int $Count Gets the amount of elements in the Collection<Group>.
 * @package vDesk\Security
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Groups extends Collection implements IDataView {
    
    /**
     * The Type of the Groups.
     */
    public const Type = Group::class;
    
    /**
     * Fetches a collection containing every existing {@link \vDesk\Security\Group} group.
     *
     * @return \vDesk\Security\Groups A collection containing every existing Group.
     */
    public static function FetchAll(): Groups {
        return new static(
            (static function() {
                //Create groups.
                foreach(
                    Expression::Select("*")
                              ->From("Security.Groups")
                              ->OrderBy(["ID" => true])
                    as
                    $Row
                ) {
                    $Group       = new Group((int)$Row["ID"]);
                    $Group->Name = $Row["Name"];
                    
                    $Permissions = new Permissions();
                    foreach($Row as $Permission => $Value) {
                        if($Permission === "ID" || $Permission === "Name") {
                            continue;
                        }
                        $Permissions->Add($Permission, (bool)$Row[$Permission]);
                    }
                    $Group->Permissions = $Permissions;
                    yield $Group;
                }
            })()
        );
    }
    
    /**
     * Creates an Groups from a JSON-encodable representation.
     *
     * @param mixed $DataView The Data to use to create an instance of the Groups. The type and format should match the output
     *                        of @return \vDesk\Security\Groups An instance of the implementing class filled with the provided
     *                        data.
     *
     * @see \vDesk\Security\Groups::ToDataView().
     *
     */
    public static function FromDataView($DataView = []): IDataView {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Group::FromDataView($Data);
                }
            })()
        );
    }
    
    /**
     * Generates a JSON-encodable representation of the Groups.
     *
     * @return mixed The JSON-encodable representation of the Groups.
     */
    public function ToDataView(): array {
        return $this->Reduce(
            static function(array $Groups, Group $Group): array {
                $Groups[] = $Group->ToDataView();
                return $Groups;
            },
            []
        );
    }
    
    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Group {
        return parent::Find($Predicate);
    }
    
    /**
     * @inheritdoc
     */
    public function Remove($Element): Group {
        return parent::Remove($Element);
    }
    
    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Group {
        return parent::RemoveAt($Index);
    }
    
    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Group {
        return parent::offsetGet($Index);
    }
}
