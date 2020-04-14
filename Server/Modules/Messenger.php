<?php
declare(strict_types=1);

namespace Modules;

use vDesk\DataProvider\Expression;
use vDesk\Modules\Command;
use vDesk\Messenger\Users\Message;
use vDesk\Modules\Module;
use vDesk\Security\Group;
use vDesk\Security\UnauthorizedAccessException;
use vDesk\Security\User;
use vDesk\Messenger\Users;
use vDesk\Messenger\Groups;
use vDesk\Utils\Log;

/**
 * Messenger module.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
final class Messenger extends Module {
    
    /**
     * Fetches a specified amount of Messages of a User chat before a specified date.
     *
     * @param \vDesk\Security\User|null $Sender    The sender of the chat.
     * @param \vDesk\Security\User|null $Recipient The recipient of the chat.
     * @param \DateTime|null            $Date      The date to get the Messages before.
     * @param int|null                  $Amount
     *
     * @return \vDesk\Messenger\Users\Chat A Collection of a User chat before the specified date.
     */
    public static function GetUserMessages(User $Sender = null, User $Recipient = null, \DateTime $Date = null, int $Amount = null): Users\Chat {
        $Sender    ??= new User(Command::$Parameters["Sender"]);
        $Recipient ??= \vDesk::$User;
        $Messages  = new Users\Chat();
        
        foreach(
            Expression::Select("*")
                      ->From(
                          Expression::Select("*")
                                    ->From("Messenger.Messages")
                                    ->Where(
                                        [
                        
                                            "Date" => ["<" => $Date ?? Command::$Parameters["Date"]],
                                            [
                                                [
                                                    "Sender"    => $Sender,
                                                    "Recipient" => $Recipient
                                                ],
                                                [
                                                    "Sender"    => $Recipient,
                                                    "Recipient" => $Sender
                                                ]
                                            ]
                                        ]
                                    )
                                    ->OrderBy(["Date" => false])
                                    ->Limit($Amount ?? Command::$Parameters["Amount"]),
                          "Messages"
                      )
                      ->OrderBy(["Date" => true])
            as
            $Row
        ) {
            $Message = new Users\Message(
                (int)$Row["ID"],
                new User((int)$Row["Sender"]),
                new User((int)$Row["Recipient"]),
                (int)$Row["Status"],
                new \DateTime($Row["Date"]),
                $Row["Text"]
            );
            if($Message->Status <= Users\Message::Received && $Message->Recipient->ID === $Recipient->ID) {
                $Message->Status = Users\Message::Read;
                $Message->Save();
                (new Users\Message\Read($Message->Sender, $Message))->Dispatch();
            }
            $Messages->Add($Message);
        }
        
        return $Messages;
    }
    
    /**
     * Gets the amount of all unread private Messages of a specified recipient.
     *
     * @param \vDesk\Security\User|null $Recipient The recipient of the unread private Messages.
     *
     * @return array The amount of all unread Messages of the specified recipient.
     */
    public static function GetUnreadUserMessages(User $Recipient = null): array {
        $Recipient ??= \vDesk::$User;
        $Messages  = [];
        foreach(
            Expression::Select("*")
                      ->From("Messenger.Messages")
                      ->Where([
                          "Recipient" => $Recipient,
                          "Status"    => ["<" => Message::Read]
                      ])
                      ->OrderBy(["Sender"])
            as
            $Row
        ) {
            $Message = new Users\Message(
                (int)$Row["ID"],
                new User((int)$Row["Sender"]),
                $Recipient,
                (int)$Row["Status"],
                new \DateTime($Row["Date"]),
                $Row["Text"]
            );
            if($Message->Status < Users\Message::Received) {
                $Message->Status = Users\Message::Received;
                $Message->Save();
                (new Users\Message\Received($Message->Sender, $Message))->Dispatch();
            }
            $Messages[$Message->Sender->ID] ??= 0;
            $Messages[$Message->Sender->ID]++;
        }
        return $Messages;
    }
    
    /**
     * Sends a Message to an User.
     *
     * @param \vDesk\Security\User|null $Recipient The User that the Message will be send to.
     * @param string|null               $Text      The text of the Message to send.
     *
     * @return \vDesk\Messenger\Users\Message The sent Message.
     */
    public static function SendUserMessage(User $Recipient = null, string $Text = null): Users\Message {
        $Message = new Users\Message(
            null,
            \vDesk::$User,
            $Recipient ?? new User(Command::$Parameters["Recipient"]),
            Users\Message::Sent,
            new \DateTime("now"),
            $Text ?? Command::$Parameters["Text"]
        );
        $Message->Save();
        (new Users\Message\Sent($Message->Recipient, $Message))->Dispatch();
        return $Message;
    }
    
    /**
     * Fetches a specified amount of Messages of a Group chat before a specified date.
     *
     * @param \vDesk\Security\Group|null $Group The Group of the chat.
     * @param \DateTime|null             $Date  The date to get the Messages before.
     * @param int|null                   $Amount
     *
     * @return \vDesk\Messenger\Groups\Chat A Collection of a Group chat before the specified date.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to read the messages of the Group.
     */
    public static function GetGroupMessages(Group $Group = null, \DateTime $Date = null, int $Amount = null): Groups\Chat {
        $Group    ??= new Group(Command::$Parameters["Group"]);
        $Messages = new Groups\Chat();
        if(!(\vDesk::$User->Memberships->Fill())->Any(fn(Group $Membership) => $Membership->ID === $Group->ID)) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to get Group messages without having permissions.");
            throw new UnauthorizedAccessException();
        }
        foreach(
            Expression::Select("*")
                      ->From(
                          Expression::Select("*")
                                    ->From("Messenger.GroupMessages")
                                    ->Where(
                                        [
                                            "Date"  => ["<" => $Date ?? Command::$Parameters["Date"]],
                                            "Group" => $Group
                                        ]
                                    )
                                    ->OrderBy(["Date" => false])
                                    ->Limit($Amount ?? Command::$Parameters["Amount"]),
                          "Messages"
                      )
                      ->OrderBy(["Date" => true])
            as
            $Row
        ) {
            $Message = new Groups\Message(
                (int)$Row["ID"],
                new User((int)$Row["Sender"]),
                new Group((int)$Row["Group"]),
                new \DateTime($Row["Date"]),
                $Row["Text"]
            );
            $Messages->Add($Message);
        }
        
        return $Messages;
    }
    
    /**
     * Sends a Message to a Group.
     *
     * @param \vDesk\Security\Group|null $Group The Group that the Message will be send to.
     * @param string|null                $Text  The text of the Message to send.
     *
     * @return \vDesk\Messenger\Groups\Message The sent Message.
     * @throws \vDesk\Security\UnauthorizedAccessException Thrown if the current User doesn't have permissions to send messages to the Group.
     */
    public static function SendGroupMessage(Group $Group = null, string $Text = null): Groups\Message {
        $Group ??= new Group(Command::$Parameters["Group"]);
        if(!\vDesk::$User->Memberships->Fill()->Any(fn(Group $Membership) => $Membership->ID === $Group->ID)) {
            Log::Warn(__METHOD__, \vDesk::$User->Name . " tried to send Group message without having permissions.");
            throw new UnauthorizedAccessException();
        }
        $Message = new Groups\Message(
            null,
            \vDesk::$User,
            $Group ?? new Group(Command::$Parameters["Group"]),
            new \DateTime("now"),
            $Text ?? Command::$Parameters["Text"]
        );
        $Message->Save();
        foreach($Group->Users as $User) {
            if($User->ID === \vDesk::$User->ID) {
                continue;
            }
            (new Groups\Message\Sent($User, $Message))->Dispatch();
        }
        return $Message;
    }
    
}