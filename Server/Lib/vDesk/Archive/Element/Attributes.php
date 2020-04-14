<?php
declare(strict_types=1);

namespace vDesk\Archive\Element;

use vDesk\Archive\Element;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\Security\User;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents the Attributes of an {@link \vDesk\Archive\Element}.
 *
 * @todo    Remove Name, Type and Guid. Filename to?
 *
 * @property Element              $Element           Gets or sets the Element of the Attributes.
 * @property \vDesk\Security\User $Owner             Gets or sets the owner of the Element of the Attributes.
 * @property \DateTime            $CreationTime      (set once) Gets or sets the creation time of the Element of the Attributes.
 * @property string               $Name              Gets or sets the name of the Element of the Attributes.
 * @property string               $Type              Gets or sets the type of the Element of the Attributes.
 * @property string|null          $FileName          Gets or sets the filename of the Element of the Attributes.
 * @property string               $Extension         Gets or sets the extension of the Element of the Attributes.
 * @property int                  $FileSize          Gets or sets the file size of the Element of the Attributes.
 * @property string               $Guid              Gets or sets the Guid of the Element of the Attributes.
 * @package vDesk\Archive
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Attributes implements IModel {

    use Properties;

    /**
     * The Element of the Attributes
     *
     * @var Element|null
     */
    private ?Element $Element;

    /**
     * The owner of the Attributes
     *
     * @var null|\vDesk\Security\User
     */
    private ?User $Owner;

    /**
     * Flag indicating whether the owner of the Attributes has been changed.
     *
     * @var bool
     */
    private bool $OwnerChanged = false;

    /**
     * The creation time of the Attributes
     *
     * @var \DateTime|null
     */
    private ?\DateTime $CreationTime;

    /**
     * The name of the Attributes
     *
     * @var null|string
     */
    private ?string $Name;

    /**
     * The file size of the Attributes
     *
     * @var int|null
     */
    private ?int $FileSize;

    /**
     * Flag indicating whether the file size of the Attributes has been changed.
     *
     * @var bool
     */
    private bool $FileSizeChanged = false;

    /**
     * The file name of the Attributes
     *
     * @var null|string
     */
    private ?string $FileName;

    /**
     * The extension of the Attributes
     *
     * @var null|string
     */
    private ?string $Extension;

    /**
     * The type of the Attributes
     *
     * @var null|int
     */
    private ?int $Type;

    /**
     * The Guid of the Attributes
     *
     * @var null|string
     */
    private ?string $Guid;

    /**
     * Initializes a new instance of the Attributes class.
     *
     * @param Element|null              $Element      Initializes the Attributes with the specified Element.
     * @param int|null                  $FileSize     Initializes the Attributes with the specified file size.
     * @param \DateTime|null            $CreationTime Initializes the Attributes with the specified creation time.
     * @param \vDesk\Security\User|null $Owner        Initializes the Attributes with the specified owner.
     * @param string|null               $Name         Initializes the Attributes with the specified name.
     * @param string|null               $FileName     Initializes the Attributes with the specified file name.
     * @param string|null               $Extension    Initializes the Attributes with the specified extension.
     * @param int|null                  $Type         Initializes the Attributes with the specified type.
     * @param string|null               $Guid         Initializes the Attributes with the specified Guid.
     */
    public function __construct(
        ?Element $Element = null,
        User $Owner = null,
        int $FileSize = null,
        \DateTime $CreationTime = null,
        string $Name = null,
        string $FileName = null,
        string $Extension = null,
        int $Type = null,
        string $Guid = null
    ) {
        $this->Element      = $Element;
        $this->FileSize     = $FileSize;
        $this->CreationTime = $CreationTime;
        $this->Owner        = $Owner;
        $this->Name         = $Name;
        $this->FileName     = $FileName;
        $this->Extension    = $Extension;
        $this->Type         = $Type;
        $this->Guid         = $Guid;
        $this->AddProperties([
            "Element"      => [
                \Get => fn(): ?Element => $this->Element,
                \Set => fn(Element $Value) => $this->Element ??= $Value
            ],
            "FileSize"     => [
                \Get => MappedGetter::Create(
                    $this->FileSize,
                    Type::Int,
                    true,
                    $this->Element,
                    Expression::Select("FileSize")
                              ->From("Archive.Attributes")
                ),
                \Set => function(int $Value): void {
                    if($this->Element !== null && $this->FileSize !== null) {
                        $this->FileSizeChanged = true;
                    }
                    $this->FileSize ??= $Value;
                }
            ],
            "CreationTime" => [
                \Get => MappedGetter::Create(
                    $this->CreationTime,
                    \DateTime::class,
                    true,
                    $this->Element,
                    Expression::Select("CreationTime")
                              ->From("Archive.Attributes")
                ),
                \Set => fn(\DateTime $Value) => $this->CreationTime ??= $Value
            ],
            "Owner"        => [
                \Get => MappedGetter::Create(
                    $this->Owner,
                    User::class,
                    true,
                    $this->Element,
                    Expression::Select("Owner")
                              ->From("Archive.Attributes")
                ),
                \Set => MappedSetter::Create(
                    $this->Owner,
                    User::class,
                    false,
                    $this->Element,
                    $this->OwnerChanged
                )
            ],
            "Name"         => [
                \Get => MappedGetter::Create(
                    $this->Name,
                    Type::String,
                    true,
                    $this->Element,
                    Expression::Select("Name")
                              ->From("Archive.Elements")
                ),
                \Set => fn(string $Value) => $this->Name ??= $Value
            ],
            "FileName"     => [
                \Get => MappedGetter::Create(
                    $this->FileName,
                    Type::String,
                    true,
                    $this->Element,
                    Expression::Select("FileName")
                              ->From("Archive.Elements")
                ),
                \Set => fn(string $Value) => $this->FileName ??= $Value
            ],
            "Extension"    => [
                \Get => MappedGetter::Create(
                    $this->Extension,
                    Type::String,
                    true,
                    $this->Element,
                    Expression::Select("Extension")
                              ->From("Archive.Elements")
                ),
                \Set => fn(string $Value) => $this->Extension ??= $Value
            ],
            "Type"         => [
                \Get => MappedGetter::Create(
                    $this->Extension,
                    Type::Int,
                    true,
                    $this->Element,
                    Expression::Select("Type")
                              ->From("Archive.Elements")
                ),
                \Set => fn(string $Value) => $this->Type ??= $Value
            ],
            "Guid"         => [
                \Get => MappedGetter::Create(
                    $this->Guid,
                    Type::String,
                    true,
                    $this->Element,
                    Expression::Select("Guid")
                              ->From("Archive.Elements")
                ),
                \Set => fn(string $Value) => $this->Guid ??= $Value
            ]
        ]);
    }

    /**
     * @inheritDoc
     */
    public function ID(): ?Element {
        return $this->Element;
    }

    /**
     * Fills the Attributes with its values stored in the database.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Attributes is virtual.
     *
     * @return \vDesk\Archive\Element\Attributes The filled Attributes.
     */
    public function Fill(): Attributes {
        if($this->Element === null || $this->Element->ID === null) {
            throw new IDNullException();
        }
        $Attributes         = Expression::Select(
            "Elements.Name",
            "Elements.FileName",
            "Elements.Type",
            "Attributes.FileSize",
            "Owner",
            "Attributes.CreationTime",
            "Elements.Extension",
            "Elements.Guid"
        )
                                        ->From("Archive.Attributes")
                                        ->InnerJoin("Archive.Elements")
                                        ->On(["Elements.ID" => "Attributes.ID"])
                                        ->Where(["Elements.ID" => $this->Element])
                                        ->Execute()
                                        ->ToMap();
        $this->CreationTime = new \DateTime($Attributes["CreationTime"]);
        $this->FileSize     = (int)$Attributes["FileSize"];
        $this->Owner        = new User((int)$Attributes["Owner"]);
        $this->Extension    = $Attributes["Extension"];
        $this->FileName     = $Attributes["FileName"];
        $this->Guid         = $Attributes["Guid"];
        $this->Name         = $Attributes["Name"];
        $this->Type         = (int)$Attributes["Type"];
        return $this;
    }

    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none was supplied.
     */
    public function Save(): void {
        if($this->Element !== null) {
            //TODO: Check these values.
            if($this->FileSizeChanged || $this->OwnerChanged) {
                Expression::Update("Archive.Attributes")
                          ->Set([
                              "Owner"    => $this->Owner,
                              "FileSize" => $this->FileSize
                          ])
                          ->Where(["ID" => $this->Element])
                          ->Execute();
            } else {
                Expression::Insert()
                          ->Into("Archive.Attributes")
                          ->Values([
                              "ID"           => $this->Element,
                              "Owner"        => $this->Owner,
                              "FileSize"     => $this->FileSize,
                              "CreationTime" => $this->CreationTime ??= new \DateTime("now")
                          ])
                          ->Execute();
            }
        }
    }

    /**
     * Deletes the Attributes.
     */
    public function Delete(): void {
        if($this->Element !== null) {
            Expression::Delete()
                      ->From("Archive.Attributes")
                      ->Where(["ID" => $this->Element])
                      ->Execute();
        }
    }

    /**
     * Returns a JSON-encodable representation of the Attributes.
     *
     * @return array A JSON-encodable representation of the Attributes.
     */

    /**
     * Creates a data view of the IManagedModel.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference to the IManagedModel.
     *
     * @return mixed The data view representing the current state of the IManagedModel.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["Element" => $this->Element]
            : [
                "Element"      => $this->Element,
                "Owner"        => $this->Owner->ToDataView(true),
                "CreationTime" => $this->CreationTime->format(\DateTime::ATOM),
                "Extension"    => $this->Extension,
                "FileSize"     => $this->FileSize,
                "FileName"     => $this->FileName,
                "Name"         => $this->Name,
                "Type"         => $this->Type,
                "Guid"         => $this->Guid
            ];
    }

    /**
     * Creates an IDataView from a JSON-encodable representation.
     *
     * @param mixed $DataView The Data to use to create an instance of the IDataView.
     *                        The type and format should match the output of
     *
     * @return \vDesk\Archive\Element\Attributes An instance of the implementing
     *                        class filled with the provided data.
     *
     * @see \vDesk\Data\IDataView::FromDataView().
     */
    public static function FromDataView($DataView): Attributes {
        return new static(
            Element::FromDataView($DataView["Element"] ?? []),
            User::FromDataView($DataView["Owner"] ?? []),
            $DataView["FileSize"] ?? 0,
            new \DateTime($DataView["CreationTime"] ?? "now"),
            $DataView["Name"] ?? "",
            $DataView["FileName"] ?? null,
            $DataView["Extension"] ?? "folder",
            $DataView["Type"] ?? 0
        );
    }

}
