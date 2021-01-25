<?php
declare(strict_types=1);

namespace vDesk\PinBoard;

use vDesk\Archive\Element as AttachmentElement;
use vDesk\DataProvider;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\Data\IDNullException;
use vDesk\Security\User;

/**
 * Represents an attachment file on the pinboard.
 *
 * @property \vDesk\Archive\Element $Element Gets or sets the attached Element of the Attachment.
 * @package vDesk\PinBoard
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
class Attachment extends Element {
    
    /**
     * The table of the Element.
     */
    protected const Table = "PinBoard.Attachments";
    
    /**
     * Initializes a new instance of the Attachment class.
     *
     * @param int|null                    $ID      Initializes the Attachment with the specified ID.
     * @param null|\vDesk\Security\User   $Owner   Initializes the Attachment with the specified owner.
     * @param int|null                    $X       Initializes the Attachment with the specified horizontal position.
     * @param int|null                    $Y       Initializes the Attachment with the specified vertical position.
     * @param \vDesk\Archive\Element|null $Element Initializes the Attachment with the specified Element.
     */
    public function __construct(
        ?int $ID = null,
        ?User $Owner = null,
        ?int $X = null,
        ?int $Y = null,
        protected ?AttachmentElement $Element = null
    ) {
        parent::__construct($ID, $Owner, $X, $Y);
        $this->AddProperty(
            "Element",
            [
                \Get => MappedGetter::Create(
                    $this->Element,
                    AttachmentElement::class,
                    false,
                    $this->ID,
                    Expression::Select("Element")
                              ->From(static::Table)
                ),
                \Set => fn(AttachmentElement $Value) => $this->Element ??= $Value
            ]
        );
    }
    
    /**
     * Fills the Attachment with its values from the database.
     *
     * @return \vDesk\PinBoard\Attachment The filled Attachment.
     * @throws \vDesk\Data\IDNullException Thrown if the Attachment is virtual.
     *
     */
    public function Fill(): Attachment {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Attachment    = Expression::Select(
            "Attachments.Owner",
            "Attachments.X",
            "Attachments.Y",
            "Attachments.Element",
            "Elements.Name",
            "Elements.Type",
            "Elements.Extension"
        )
                                   ->From(static::Table)
                                   ->InnerJoin("Archive.Elements")
                                   ->On(["Elements.ID" => "Attachments.Element"])
                                   ->Where(["Attachments.ID" => $this->ID])
                                   ->Execute()
                                   ->ToMap();
        $this->Owner   = new User((int)$Attachment["Owner"]);
        $this->X       = (int)$Attachment["X"];
        $this->Y       = (int)$Attachment["Y"];
        $this->Element = new AttachmentElement(
            (int)$Attachment["Element"],
            null,
            new AttachmentElement(),
            $Attachment["Name"],
            (int)$Attachment["Type"],
            null,
            null,
            $Attachment["Extension"]
        );
        return $this;
    }
    
    /**
     * Deletes the Attachment.
     */
    public function Delete(): void {
        Expression::Delete()
                  ->From(static::Table)
                  ->Where(["ID" => $this->ID])
                  ->Execute();
    }
    
    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none was supplied.
     */
    public function Save(): void {
        if($this->ID !== null) {
            Expression::Update(static::Table)
                      ->SetIf([
                          "X" => [$this->XChanged => $this->X],
                          "Y" => [$this->YChanged => $this->Y]
                      ])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        } else {
            $this->ID = Expression::Insert()
                                  ->Into(static::Table)
                                  ->Values([
                                      "ID"      => null,
                                      "Owner"   => $this->Owner,
                                      "Element" => $this->Element,
                                      "X"       => $this->X,
                                      "Y"       => $this->Y
                                  ])
                                  ->ID();
        }
    }
    
    /**
     * Creates an Attachment from a specified data view.
     *
     * @param array $DataView The data to use to create an Attachment.
     *
     * @return \vDesk\PinBoard\Attachment An Attachment created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Attachment {
        return new static(
            $DataView["ID"] ?? null,
            $DataView["X"] ?? 0,
            $DataView["Y"] ?? 0,
            null,
            new AttachmentElement(
                (int)$DataView["Element"],
                null,
                $DataView["Name"],
                (int)$DataView["Type"],
                $DataView["Extension"]
            )
        );
    }
    
    /**
     * Creates a data view of the Attachment.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Attachment.
     *
     * @return array The data view representing the current state of the Attachment.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"      => $this->ID,
                "X"       => $this->X,
                "Y"       => $this->Y,
                "Element" => [
                    "ID"        => $this->Element?->ID,
                    "Name"      => $this->Element?->Name,
                    "Type"      => $this->Element?->Type,
                    "Thumbnail" => $this->Element?->Thumbnail ?? null,
                    "Extension" => $this->Element?->Extension,
                    "type1" => gettype($this->Element?->Thumbnail),
                    "type2" => gettype($this->Element?->Extension),
                ]
            ];
    }
}
