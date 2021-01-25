<?php
declare(strict_types=1);

namespace vDesk\Messenger\Groups;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\Security\Group;
use vDesk\Security\User;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a conversation Message between two Groups.
 *
 * @property int|null                   $ID        Gets or sets the ID of the Message.
 * @property \vDesk\Security\User|null  $Sender    Gets or sets the sender of the Message.
 * @property \vDesk\Security\Group|null $Group     Gets or sets the Group of the Message.
 * @property \DateTime|null             $Date      Gets or sets the date of the Message.
 * @property string|null                $Text      Gets or sets the text of the Message.
 * @package vDesk\Messenger
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Message implements IModel {
    
    use Properties;
    
    /**
     * Indicates that the Message has been transmitted.
     */
    public const Sent = 0;
    
    /**
     * Indicates that the Message has been received by the associated recipient.
     */
    public const Received = 1;
    
    /**
     * Indicates that the Message has been received and read by the associated recipient.
     */
    public const Read = 2;
    
    /**
     * Initializes a new instance of the Message class.
     *
     * @param int|null                   $ID     Initializes the Message with the specified ID.
     * @param \vDesk\Security\User|null  $Sender Initializes the Message with the specified sender.
     * @param \vDesk\Security\Group|null $Group  Initializes the Message with the specified recipient.
     * @param \DateTime|null             $Date   Initializes the Message with the specified date.
     * @param string|null                $Text   Initializes the Message with the specified text.
     */
    public function __construct(
        protected ?int $ID = null,
        protected ?User $Sender = null,
        protected ?Group $Group = null,
        protected ?\DateTime $Date = null,
        protected ?string $Text = null
    ) {
        $this->AddProperties([
            "ID"     => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Sender" => [
                \Get => MappedGetter::Create(
                    $this->Sender,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("Sender")
                              ->From("Messenger.GroupMessages")
                ),
                \Set => fn(User $Value) => $this->Sender ??= $Value
            ],
            "Group"  => [
                \Get => MappedGetter::Create(
                    $this->Group,
                    Group::class,
                    true,
                    $this->ID,
                    Expression::Select("Group")
                              ->From("Messenger.GroupMessages")
                ),
                \Set => fn(Group $Value) => $this->Group ??= $Value
            ],
            "Date"   => [
                \Get => MappedGetter::Create(
                    $this->Date,
                    \DateTime::class,
                    true,
                    $this->ID,
                    Expression::Select("Date")
                              ->From("Messenger.GroupMessages")
                ),
                \Set => fn(\DateTime $Value) => $this->Date ??= $Value
            ],
            "Text"   => [
                \Get => MappedGetter::Create(
                    $this->Text,
                    Type::String,
                    false,
                    $this->ID,
                    Expression::Select("Text")
                              ->From("Messenger.GroupMessages")
                ),
                \Set => fn(string $Value) => $this->Text ??= $Value
            ]
        ]);
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->ID;
    }
    
    /**
     * Fills the Message with its values from the database.
     *
     * @return \vDesk\Messenger\Groups\Message The filled Message.
     * @throws \vDesk\Data\IDNullException Thrown if the Message is virtual.
     */
    public function Fill(): Message {
        if($this->ID === null) {
            throw new IDNullException("Cannot Fill Model without ID");
        }
        $Message      = Expression::Select("*")
                                  ->From("Messenger.GroupMessages")
                                  ->Where(["ID" => $this->ID])
                                  ->Execute()
                                  ->ToMap();
        $this->Sender = new User((int)$Message["Sender"]);
        $this->Group  = new Group((int)$Message["Group"]);
        $this->Date   = new \DateTime($Message["Date"]);
        $this->Text   = $Message["Text"];
        return $this;
    }
    
    /**
     * Saves possible changes of an existing IModel or creates a new one.
     */
    public function Save(): void {
        if($this->ID === null) {
            $this->ID = Expression::Insert()
                                  ->Into("Messenger.GroupMessages")
                                  ->Values(
                                      [
                                          "ID"     => $this->ID,
                                          "Sender" => $this->Sender,
                                          "Group"  => $this->Group,
                                          "Date"   => $this->Date,
                                          "Text"   => $this->Text
                                      ]
                                  )
                                  ->ID();
        }
    }
    
    /**
     * Deletes the Message.
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("Messenger.GroupMessages")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }
    
    /**
     * Creates a Message from a specified data view.
     *
     * @param array $DataView The data to use to create a Message.
     *
     * @return \vDesk\Messenger\Groups\Message A Message created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Message {
        return new static(
            $DataView["ID"] ?? null,
            new User((int)$DataView["Sender"]),
            new Group((int)$DataView["Group"]),
            new \DateTime($DataView["Date"] ?? "now"),
            $DataView["Text"] ?? null
        );
    }
    
    /**
     * Creates a data view of the Message.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Message.
     *
     * @return array The data view representing the current state of the Message.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"     => $this->ID,
                "Sender" => $this->Sender?->ToDataView(true),
                "Group"  => $this->Group?->ToDataView(true),
                "Date"   => $this->Date->format(\DateTime::ATOM),
                "Text"   => $this->Text
            ];
    }
    
}