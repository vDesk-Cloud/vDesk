<?php
declare(strict_types=1);

namespace vDesk\Archive;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\Model;
use vDesk\Security\AccessControlledModel;
use vDesk\Security\AccessControlList;
use vDesk\Security\User;
use vDesk\Struct\Guid;
use vDesk\Struct\Type;

/**
 * Represents a file or folder.
 *
 * @property int|null                    $ID                Gets or sets the ID of the Element.
 * @property \vDesk\Security\User|null   $Owner             Gets or sets the owner of the Element.
 * @property \vDesk\Archive\Element|null $Parent            Gets or sets the Parent Element of the Element.
 * @property string|null                 $Name              Gets or sets the name of the Element.
 * @property int|null                    $Type              Gets or sets the type of the Element.
 * @property \DateTime                   $CreationTime      Gets or sets the creation time of the Element.
 * @property string|null                 $Guid              Gets or sets the Guid of the Element.
 * @property string                      $Extension         Gets or sets the extension of the Element.
 * @property string|null                 $File              Gets or sets the file name of the Element.
 * @property int                         $Size              Gets or sets the file size of the Element.
 * @property string|null                 $Thumbnail         Gets or sets the Thumbnail of the Element.
 * @property-read string                 $Icon              Gets the icon of the Element.
 * @property-read bool                   $HasChildren       Gets a value indicating whether the Element has children.
 * @property-read \vDesk\Archive\Element $Children          Gets a Collection of child Elements of the Element.
 *
 * @package vDesk\Archive
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
final class Element extends AccessControlledModel {
    
    /**
     * The folder-type of an Element.
     */
    public const Folder = 0;
    
    /**
     * The file-type of an Element.
     */
    public const File = 1;
    
    /**
     * The ID of the Archive root Element.
     */
    public const Archive = 1;
    
    /**
     * The ID of the Archive System folder Element.
     */
    public const System = 2;
    
    /**
     * The ID of the Element.
     *
     * @var int|null
     */
    private ?int $ID;
    
    /**
     * The owner of the Element.
     *
     * @var null|\vDesk\Security\User
     */
    private ?User $Owner;
    
    /**
     * The parent Element of the Element.
     *
     * @var null|\vDesk\Archive\Element
     */
    private ?Element $Parent;
    
    /**
     * Flag indicating whether the parent Element of the Element has been changed.
     *
     * @var bool
     */
    private bool $ParentChanged = false;
    
    /**
     * The name of the Element.
     *
     * @var null|string
     */
    private ?string $Name;
    
    /**
     * Flag indicating whether the name of the Element has been changed.
     *
     * @var bool
     */
    private bool $NameChanged = false;
    
    /**
     * The type of the Element.
     *
     * @var int|null
     */
    private ?int $Type;
    
    /**
     * The creation time of the Element.
     *
     * @var \DateTime|null
     */
    private ?\DateTime $CreationTime;
    
    /**
     * The Guid of the Element.
     *
     * @var null|string
     */
    private ?string $Guid;
    
    /**
     * The extension of the Element.
     * If the Element is of the type folder, this property will always be null.
     *
     * @var null|string
     */
    private ?string $Extension;
    
    /**
     * The file name of the Element.
     * If the Element is of the type folder, this property will always be null.
     *
     * @var null|string
     */
    private ?string $File;
    
    /**
     * Flag indicating whether the file name of the Element has been changed.
     *
     * @var bool
     */
    private bool $FileChanged = false;
    
    /**
     * The size of the Element
     *
     * @var int|null
     */
    private ?int $Size;
    
    /**
     * Flag indicating whether the size of the Element has been changed.
     *
     * @var bool
     */
    private bool $SizeChanged = false;
    
    /**
     * The Thumbnail of the Element.
     *
     * @var null|string
     */
    private ?string $Thumbnail;
    
    /**
     * Flag indicating whether the Thumbnail of the Element has been changed.
     *
     * @var bool
     */
    private bool $ThumbnailChanged = false;
    
    /**
     * Initializes a new instance of the Element class.
     *
     * @param int                                    $ID                Initializes the Element with the specified ID.
     * @param \vDesk\Security\User|null              $Owner             Initializes the Element with the specified owner.
     * @param \vDesk\Archive\Element|null            $Parent            Initializes the Element with the specified parent Element.
     * @param string|null                            $Name              Initializes the Element with the specified name.
     * @param int|null                               $Type              Initializes the Element with the specified type.
     * @param \DateTime|null                         $CreationTime      Initializes the Element with the specified creation time.
     * @param string|null                            $Guid              Initializes the Element with the specified Guid.
     * @param string|null                            $Extension         Initializes the Element with the specified extension.
     * @param string|null                            $File              Initializes the Element with the specified file name.
     * @param int|null                               $Size              Initializes the Element with the specified size.
     * @param string|null                            $Thumbnail         Initializes the Element with the specified Thumbnail.
     * @param \vDesk\Security\AccessControlList|null $AccessControlList Initializes the Element with the specified AccessControlList.
     */
    public function __construct(
        int $ID = null,
        User $Owner = null,
        Element $Parent = null,
        string $Name = null,
        int $Type = null,
        \DateTime $CreationTime = null,
        string $Guid = null,
        string $Extension = null,
        string $File = null,
        int $Size = null,
        string $Thumbnail = null,
        AccessControlList $AccessControlList = null
    ) {
        parent::__construct($AccessControlList);
        $this->ID           = $ID;
        $this->Owner        = $Owner;
        $this->Parent       = $Parent;
        $this->Name         = $Name;
        $this->Type         = $Type;
        $this->CreationTime = $CreationTime;
        $this->Extension    = $Extension;
        $this->File         = $File;
        $this->Size         = $Size;
        $this->Thumbnail    = $Thumbnail;
        $this->Guid         = $Guid;
        $this->AddProperties([
            "ID"           => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value
            ],
            "Owner"        => [
                \Get => MappedGetter::Create(
                    $this->Owner,
                    User::class,
                    true,
                    $this->ID,
                    Expression::Select("Owner")
                              ->From("Archive.Elements")
                ),
                \Set => fn(User $Value) => $this->Owner ??= $Value
            ],
            "Parent"       => [
                \Get => MappedGetter::Create(
                    $this->Parent,
                    static::class,
                    true,
                    $this->ID,
                    Expression::Select("Parent")
                              ->From("Archive.Elements")
                ),
                \Set => MappedSetter::Create(
                    $this->Parent,
                    static::class,
                    false,
                    $this->ID,
                    $this->ParentChanged
                )
            ],
            "Name"         => [
                \Get => MappedGetter::Create(
                    $this->Name,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Name")
                              ->From("Archive.Elements")
                ),
                \Set => MappedSetter::Create(
                    $this->Name,
                    Type::String,
                    false,
                    $this->ID,
                    $this->NameChanged
                )
            ],
            "Type"         => [
                \Get => MappedGetter::Create(
                    $this->Type,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Type")
                              ->From("Archive.Elements")
                ),
                \Set => fn(int $Value) => $this->Type ??= $Value
            ],
            "CreationTime" => [
                \Get => MappedGetter::Create(
                    $this->CreationTime,
                    \DateTime::class,
                    true,
                    $this->ID,
                    Expression::Select("CreationTime")
                              ->From("Archive.Elements")
                ),
                \Set => fn(\DateTime $Value) => $this->CreationTime ??= $Value
            ],
            "Guid"         => [
                \Get => MappedGetter::Create(
                    $this->Guid,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Guid")
                              ->From("Archive.Elements")
                ),
                \Set => fn(string $Value) => $this->Guid ??= $Value
            ],
            "Extension"    => [
                \Get => MappedGetter::Create(
                    $this->Extension,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Extension")
                              ->From("Archive.Elements")
                ),
                \Set => fn(?string $Value) => $this->Extension ??= $Value
            ],
            "File"         => [
                \Get => MappedGetter::Create(
                    $this->File,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("File")
                              ->From("Archive.Elements")
                ),
                \Set => MappedSetter::Create(
                    $this->File,
                    Type::String,
                    false,
                    $this->ID,
                    $this->FileChanged
                )
            ],
            "Size"         => [
                \Get => MappedGetter::Create(
                    $this->Size,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Size")
                              ->From("Archive.Attributes")
                ),
                \Set => MappedSetter::Create(
                    $this->Size,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->SizeChanged
                )
            ],
            "Thumbnail"    => [
                \Get => MappedGetter::Create(
                    $this->Thumbnail,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Thumbnail")
                              ->From("Archive.Elements")
                ),
                \Set => MappedSetter::Create(
                    $this->Thumbnail,
                    Type::String,
                    true,
                    $this->ID,
                    $this->ThumbnailChanged
                )
            ],
            "HasChildren"  => [
                \Get => function(): bool {
                    if($this->ID !== null && $this->Type === "Folder") {
                        return (Expression::Select("ID")
                                          ->From("Archive.Elements")
                                          ->Where(["Parent" => $this])())
                                ->Count > 0;
                    }
                    return false;
                }
            ],
            "Icon"         => [
                \Get => fn(): string => $this->Type === Element::Folder ? "folder" : $this->Extension
            ],
            "Children"     => [
                \Get => fn(): Elements => Elements::FromElement($this)
            ]
        ]);
    }
    
    /**
     * Returns the ID of the {@link \vDesk\Security\AccessControlList} assigned to this Element.
     *
     * @return int The ID of the {@link \vDesk\Security\AccessControlList}.
     */
    protected function GetACLID(): ?int {
        return $this->ACLID ??= $this->ID !== null
            ? (int)Expression::Select("AccessControlList")
                             ->From("Archive.Elements")
                             ->Where(["ID" => $this->ID])()
            : null;
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?int {
        return $this->ID;
    }
    
    /**
     * Fills the Element with it's values from the database.
     *
     * @param \vDesk\Security\User|null $User The User to determine access on the Element.
     *
     * @return \vDesk\Archive\Element The current instance for further chaining.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Element is virtual.
     */
    public function Fill(User $User = null): Element {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Element                 = Expression::Select("*")
                                             ->From("Archive.Elements")
                                             ->Where(["ID" => $this->ID])
                                             ->Execute()
                                             ->ToMap();
        $this->AccessControlList = new AccessControlList([], (int)$Element["AccessControlList"]);
        parent::Fill($User);
        $this->Owner        = new User((int)$Element["Owner"]);
        $this->Parent       = new Element((int)$Element["Parent"]);
        $this->Name         = $Element["Name"];
        $this->Type         = (int)$Element["Type"];
        $this->CreationTime = new \DateTime($Element["CreationTime"]);
        $this->Guid         = $Element["Guid"];
        $this->Extension    = $Element["Extension"];
        $this->File         = $Element["File"];
        $this->Size         = (int)$Element["Size"];
        $this->Thumbnail    = $Element["Thumbnail"];
        return $this;
    }
    
    /**
     * Saves possible changes if a valid ID was supplied, or creates a new database-entry if none has been supplied.
     *
     * @param \vDesk\Security\User|null $User The User to optionally perform access control checks.
     */
    public function Save(User $User = null): void {
        parent::Save($User);
        if($this->ID !== null) {
            Expression::Update("Archive.Elements")
                      ->SetIf([
                          "Parent"    => [$this->ParentChanged => $this->Parent],
                          "Name"      => [$this->NameChanged => $this->Name],
                          "File"      => [$this->FileChanged => $this->File],
                          "Size"      => [$this->SizeChanged => $this->Size],
                          "Thumbnail" => [$this->ThumbnailChanged => $this->Thumbnail]
                      ])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        } else {
            $this->ID = Expression::Insert()
                                  ->Into("Archive.Elements")
                                  ->Values([
                                      "ID"                => null,
                                      "Owner"             => $this->Owner,
                                      "Parent"            => $this->Parent,
                                      "Name"              => $this->Name,
                                      "Extension"         => $this->Extension,
                                      "Type"              => $this->Type,
                                      "CreationTime"      => $this->CreationTime,
                                      "Guid"              => $this->Guid,
                                      "File"              => $this->File,
                                      "Size"              => $this->Size,
                                      "Thumbnail"         => $this->Thumbnail,
                                      "AccessControlList" => $this->AccessControlList
                                  ])
                                  ->ID();
        }
    }
    
    /**
     * Deletes the Element.
     *
     * @param \vDesk\Security\User|null $User The User to optionally perform access control checks.
     */
    public function Delete(User $User = null): void {
        if($this->ID !== null) {
            parent::Delete($User);
            Expression::Delete()
                      ->From("Archive.Elements")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }
    
    /**
     * Creates an Element from a specified data view.
     *
     * @param array $DataView The data to use to create an Element.
     *
     * @return \vDesk\Archive\Element An Element created from the specified data view.
     */
    public static function FromDataView($DataView): Element {
        return new static(
            $DataView["ID"] ?? null,
            User::FromDataView($DataView["Owner"] ?? []),
            new Element($DataView["Parent"] ?? null),
            $DataView["Name"] ?? "",
            $DataView["Type"] ?? static::Folder,
            new \DateTime($DataView["CreationTime"] ?? "now"),
            $DataView["Guid"] ?? Guid::Create(),
            $DataView["Extension"] ?? null,
            $DataView["File"] ?? null,
            $DataView["Size"] ?? 0,
            $DataView["Thumbnail"] ?? null,
            AccessControlList::FromDataView($DataView["AccessControlList"] ?? [])
        );
    }
    
    /**
     * Creates a data view of the Element.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Element.
     *
     * @return array The data view representing the current state of the Element.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"                => $this->ID,
                "Owner"             => ($this->Owner ?? new Model())->ToDataView(true),
                "Parent"            => $this->Parent->ID,//@todo Change to ($this->Parent ?? new Model())->ToDataView(true),
                "Name"              => $this->Name,
                "Type"              => $this->Type,
                "CreationTime"      => ($this->CreationTime ?? new \DateTime("now"))->format(\DateTime::ATOM),
                "Guid"              => $this->Guid,
                "Extension"         => $this->Extension,
                "File"              => $this->File,
                "Size"              => $this->Size,
                "Icon"              => $this->Icon,
                "Thumbnail"         => $this->Thumbnail,
                "AccessControlList" => $this->AccessControlList->ToDataView(true),
            ];
    }
}
