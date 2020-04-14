<?php
declare(strict_types=1);

namespace vDesk\PinBoard;

use vDesk\Archive\Element;
use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of {@link \vDesk\PinBoard\Element} objects.
 *
 * @package vDesk\PinBoard
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Attachments extends Collection implements IDataView {

    /**
     * The Type of the Elements.
     */
    public const Type = Attachment::class;

    /**
     * Fetches a Attachments containing every Attachment of a specified User.
     *
     * @param \vDesk\Security\User $Owner The owner of whose Attachments get fetched.
     *
     * @return \vDesk\PinBoard\Attachments A Collection containing every Attachment of the specified owner.
     */
    public static function FromOwner(User $Owner): self {
        return new static(
            (static function() use ($Owner): \Generator {
                foreach(
                    Expression::Select(
                        "Attachments.ID",
                        "Attachments.Element",
                        "Attachments.X",
                        "Attachments.Y",
                        "Elements.Name",
                        "Elements.Type",
                        "Elements.Extension"
                    )
                              ->From("PinBoard.Attachments")
                              ->InnerJoin("Archive.Elements",)
                              ->On(["Elements.ID" => "Attachments.Element"])
                              ->Where(["Attachments.Owner" => $Owner])
                    as
                    $Attachment
                ) {
                    yield new Attachment(
                        (int)$Attachment["ID"],
                        $Owner,
                        (int)$Attachment["X"],
                        (int)$Attachment["Y"],
                        new Element(
                            (int)$Attachment["Element"],
                            $Owner,
                            null,
                            $Attachment["Name"],
                            (int)$Attachment["Type"],
                            null,
                            null,
                            $Attachment["Extension"]
                        )
                    );
                }
            })()
        );
    }

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Attachment {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): Attachment {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Attachment {
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Attachment {
        return parent::offsetGet($Index);
    }

    /**
     * Creates a Attachments from a specified data view.
     *
     * @param array $DataView The data to use to create a Attachments.
     *
     * @return \vDesk\PinBoard\Attachments A Attachments created from the specified data view.
     */
    public static function FromDataView($DataView): Attachments {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Attachment::FromDataView($Data);
                }
            })()
        );
    }

    /**
     * Creates a data view of the Attachments.
     *
     * @return array The data view representing the current state of the Attachments.
     */
    public function ToDataView(): array {
        return $this->Reduce(
            static function(array $Attachments, Attachment $Attachment): array {
                $Attachments[] = $Attachment->ToDataView();
                return $Attachments;
            },
            []
        );
    }

}
