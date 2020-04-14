<?php
declare(strict_types=1);

namespace vDesk\PinBoard;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a Collection of Notes.
 *
 * @package vDesk\PinBoard
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Notes extends Collection implements IDataView {

    /**
     * The Type of the Elements.
     */
    public const Type = Note::class;

    /**
     * Fetches a Attachments containing every Attachment of a specified User.
     *
     * @param \vDesk\Security\User $Owner The owner of whose Attachments get fetched.
     *
     * @return \vDesk\PinBoard\Notes A Collection containing every Attachment of the specified owner.
     */
    public static function FromOwner(User $Owner): Notes {
        return new static(
            (static function() use ($Owner): \Generator {
                foreach(
                    Expression::Select("*")
                              ->From("PinBoard.Notes")
                              ->Where(["Owner" => $Owner->ID])
                    as
                    $Note
                ) {
                    yield new Note(
                        (int)$Note["ID"],
                        $Owner,
                        (int)$Note["X"],
                        (int)$Note["Y"],
                        (int)$Note["Width"],
                        (int)$Note["Height"],
                        $Note["Color"],
                        $Note["Content"]
                    );
                }
            })()
        );
    }

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Note {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): Note {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Note {
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Note {
        return parent::offsetGet($Index);
    }

    /**
     * Creates a Notes from a specified data view.
     *
     * @param array $DataView The data to use to create a Notes.
     *
     * @return \vDesk\PinBoard\Notes A Notes created from the specified data view.
     */
    public static function FromDataView($DataView): Notes {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Note::FromDataView($Data);
                }
            })()
        );
    }

    /**
     * Creates a data view of the Notes.
     *
     * @return array The data view representing the current state of the Notes.
     */
    public function ToDataView(): array {
        return $this->Reduce(
            static function(array $Notes, Note $Note): array {
                $Notes[] = $Note->ToDataView();
                return $Notes;
            },
            []
        );
    }

}