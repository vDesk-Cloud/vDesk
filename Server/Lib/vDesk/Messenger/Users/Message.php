<?php
declare(strict_types=1);

namespace vDesk\Messenger\Users;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\Data\Model;
use vDesk\Security\User;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a conversation Message between two Users.
 *
 * @property int|null                  $ID        Gets or sets the ID of the Message.
 * @property \vDesk\Security\User|null $Sender    Gets or sets the sender of the Message.
 * @property \vDesk\Security\User|null $Recipient Gets or sets the recipient of the Message.
 * @property int                       $Status    Gets or sets the transmission status of the Message.
 * @property \DateTime|null            $Date      Gets or sets the date of the Message.
 * @property string|null               $Text      Gets or sets the text of the Message.
 * @package vDesk\Messenger
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
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
     * The ID of the Message.
     *
     * @var int|null
     */
    private ?int $ID;

    /**
     * The sender of the Message.
     *
     * @var  null|\vDesk\Security\User
     */
    private ?User $Sender;

    /**
     * The recipient of the Message.
     *
     * @var null|\vDesk\Security\User
     */
    private ?User $Recipient;

    /**
     * The transmission status of the Message.
     *
     * @var int
     */
    private int $Status;

    /**
     * The date of the Message.
     *
     * @var null|\DateTime
     */
    private ?\DateTime $Date;

    /**
     * The text of the Message.
     *
     * @var null|string
     */
    private ?string $Text;

    /**
     * Initializes a new instance of the Message class.
     *
     * @param int|null                  $ID        Initializes the Message with the specified ID.
     * @param \vDesk\Security\User|null $Sender    Initializes the Message with the specified sender.
     * @param \vDesk\Security\User|null $Recipient Initializes the Message with the specified recipient.
     * @param int                       $Status    Initializes the Message with the specified transmission status.
     * @param \DateTime|null            $Date      Initializes the Message with the specified date.
     * @param string|null               $Text      Initializes the Message with the specified text.
     */
    public function __construct(
        ?int $ID = null,
        User $Sender = null,
        User $Recipient = null,
        int $Status = self::Sent,
        \DateTime $Date = null,
        string $Text = null
    ) {
        $this->ID        = $ID;
        $this->Sender    = $Sender;
        $this->Recipient = $Recipient;
        $this->Status    = $Status;
        $this->Date      = $Date;
        $this->Text      = $Text;
        $this->AddProperties([
            "ID"        => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Sender"    => [
                \Get => MappedGetter::Create(
                    $this->Sender,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("Sender")
                              ->From("Messenger.Messages")
                ),
                \Set => fn(User $Value) => $this->Sender ??= $Value
            ],
            "Recipient" => [
                \Get => MappedGetter::Create(
                    $this->Recipient,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("Recipient")
                              ->From("Messenger.Messages")
                ),
                \Set => fn(User $Value) => $this->Recipient ??= $Value
            ],
            "Status"    => [
                \Get => fn(): int => $this->Status,
                \Set => fn(int $Value) => $this->Status = $Value
            ],
            "Date"      => [
                \Get => MappedGetter::Create(
                    $this->Date,
                    \DateTime::class,
                    true,
                    $this->ID,
                    Expression::Select("Date")
                              ->From("Messenger.Messages")
                ),
                \Set => fn(\DateTime $Value) => $this->Date ??= $Value
            ],
            "Text"      => [
                \Get => MappedGetter::Create(
                    $this->Text,
                    Type::String,
                    false,
                    $this->ID,
                    Expression::Select("Text")
                              ->From("Messenger.Messages")
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
     * @throws \vDesk\Data\IDNullException Thrown if the Message is virtual.
     * @return \vDesk\Messenger\Users\Message The filled Message.
     */
    public function Fill(): Message {
        if($this->ID === null) {
            throw new IDNullException("Cannot Fill Model without ID");
        }
        $Message         = Expression::Select("*")
                                     ->From("Messenger.Messages")
                                     ->Where(["ID" => $this->ID])
                                     ->Execute()
                                     ->ToMap();
        $this->Sender    = new User((int)$Message["Sender"]);
        $this->Recipient = new User((int)$Message["Recipient"]);
        $this->Status    = (int)$Message["Status"];
        $this->Date      = new \DateTime($Message["Date"]);
        $this->Text      = $Message["Text"];
        return $this;
    }

    /**
     * Saves possible changes of an existing IModel or creates a new one.
     */
    public function Save(): void {
        if($this->ID === null) {
            $this->ID = Expression::Insert()
                                  ->Into("Messenger.Messages")
                                  ->Values(
                                      [
                                          "ID"        => $this->ID,
                                          "Sender"    => $this->Sender,
                                          "Recipient" => $this->Recipient,
                                          "Status"    => $this->Status,
                                          "Date"      => $this->Date,
                                          "Text"      => $this->Text
                                      ]
                                  )
                                  ->ID();
        } else {
            //Only the transmission status of Messages are mutable after creation.
            Expression::Update("Messenger.Messages")
                      ->Set(["Status" => $this->Status])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }

    /**
     * Deletes the Message.
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("Messenger.Messages")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }

    /**
     * Creates a Message from a specified data view.
     *
     * @param array $DataView The data to use to create a Message.
     *
     * @return \vDesk\Messenger\Users\Message A Message created from the specified data view.
     */
    public static function FromDataView($DataView): Message {
        return new static(
            $DataView["ID"] ?? null,
            new User((int)$DataView["Sender"]),
            new User((int)$DataView["Recipient"]),
            (int)$DataView["Status"],
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
                "ID"        => $this->ID,
                "Sender"    => ($this->Sender ?? new Model())->ToDataView(true),
                "Recipient" => ($this->Recipient ?? new Model())->ToDataView(true),
                "Status"    => $this->Status,
                "Date"      => $this->Date->format(\DateTime::ATOM),
                "Text"      => $this->Text
            ];
    }

}