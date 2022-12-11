<?php
declare(strict_types=1);

namespace vDesk\Security\Group;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\Group;
use vDesk\Struct\Collections\Typed;

/**
 * Class that represents a typed Collection of Groups containing utility functions for loading Groups from the database.
 *
 * @package vDesk\Security
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Collection extends Typed\Collection implements IDataView {

    /**
     * The Type of the Group Collection.
     */
    public const Type = Group::class;

    /**
     * Creates a new Group Collection containing every existing Group.
     *
     * @return \vDesk\Security\Group\Collection A Collection containing every existing Group.
     */
    public static function All(): static {
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
                    $Permissions = new Permissions();
                    foreach($Row as $Permission => $Value) {
                        if($Permission === "ID" || $Permission === "Name") {
                            continue;
                        }
                        $Permissions->Add($Permission, (bool)$Value);
                    }
                    yield new Group((int)$Row["ID"], $Row["Name"], $Permissions);
                }
            })()
        );
    }

    /** @inheritdoc */
    public static function FromDataView($DataView = []): static {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Group) {
                    yield Group::FromDataView($Group);
                }
            })()
        );
    }

    /** @inheritdoc */
    public function ToDataView(bool $Reference = false): array {
        return $this->Reduce(
            static function(array $Groups, Group $Group) use ($Reference): array {
                $Groups[] = $Group->ToDataView($Reference);
                return $Groups;
            },
            []
        );
    }

    /** @inheritdoc */
    public function Find(callable $Predicate): ?Group {
        return parent::Find($Predicate);
    }

    /** @inheritdoc */
    public function Remove($Element): Group {
        return parent::Remove($Element);
    }

    /** @inheritdoc */
    public function RemoveAt(int $Index): Group {
        return parent::RemoveAt($Index);
    }

    /** @inheritdoc */
    public function offsetGet($Index): Group {
        return parent::offsetGet($Index);
    }
}
