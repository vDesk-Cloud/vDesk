<?php
declare(strict_types=1);

namespace vDesk\Messenger\Users;

use vDesk\DataProvider\Expression;
use vDesk\Data\IDataView;
use vDesk\Security\User;
use vDesk\Struct\Collections\Typed\Collection;

/**
 * Represents a collection of {@link \vDesk\Messenger\Users\Message} objects.
 *
 * @package vDesk\Messenger
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Chat extends Collection implements IDataView {

    /**
     * The Type of the Chat.
     */
    public const Type = Message::class;

    /**
     * Creates a Chat from a specified data view.
     *
     * @param array $DataView The data to use to create a Chat.
     *
     * @return \vDesk\Messenger\Users\Chat A Chat created from the specified data view.
     */
    public static function FromDataView($DataView): Chat {
        return new static(
            (static function() use ($DataView) {
                foreach($DataView as $Data) {
                    yield Message::FromDataView($Data);
                }
            })()
        );
    }

    /**
     * Creates a data view of the Chat.
     *
     * @return array The data view representing the current state of the Chat.
     */
    public function ToDataView(): array {
        return $this->Reduce(static function(array $Messages, Message $Message): array {
            $Messages[] = $Message->ToDataView();
            return $Messages;
        }, []);
    }

    /**
     * @inheritdoc
     */
    public function Find(callable $Predicate): ?Message {
        return parent::Find($Predicate);
    }

    /**
     * @inheritdoc
     */
    public function Remove($Element): Message {
        return parent::Remove($Element);
    }

    /**
     * @inheritdoc
     */
    public function RemoveAt(int $Index): Message {
        return parent::RemoveAt($Index);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($Index): Message {
        return parent::offsetGet($Index);
    }
}