<?php
declare(strict_types=1);

namespace vDesk\PinBoard;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Security\User;
use vDesk\Struct\Type;

/**
 * Represents a Note on the PinBoard.
 *
 * @property int    $Width   Gets or sets the width of a Note.
 * @property int    $Height  Gets or sets the height of a Note.
 * @property string $Color   Gets or sets the color of the Note.
 * @property string $Content Gets or sets the text content of the Note.
 * @todo    Rename "Content" property to "Text".
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 * @version 1.0.0.
 */
class Note extends Element {
    
    /**
     * The table of the Note.
     */
    protected const Table = "PinBoard.Notes";
    
    /**
     * The width of the Note.
     *
     * @var int|null
     */
    private ?int $Width;
    
    /**
     * Flag indicating whether the width of the Note has been changed.
     *
     * @var bool
     */
    private bool $WidthChanged = false;
    
    /**
     * The height of the Note.
     *
     * @var int|null
     */
    private ?int $Height;
    
    /**
     * Flag indicating whether the height of the Note has been changed.
     *
     * @var bool
     */
    private bool $HeightChanged = false;
    
    /**
     * The color of the Note.
     *
     * @var null|string
     */
    private ?string $Color;
    
    /**
     * Flag indicating whether the color of the Note has been changed.
     *
     * @var bool
     */
    private bool $ColorChanged = false;
    
    /**
     * The content of the Note.
     *
     * @var null|string
     */
    private ?string $Content;
    
    /**
     * Flag indicating whether the content of the Note has been changed.
     *
     * @var bool
     */
    private bool $ContentChanged = false;
    
    /**
     * Initializes a new instance of the Note class.
     *
     * @param int|null             $ID      Initializes the Note with the specified ID.
     * @param \vDesk\Security\User $Owner   Initializes the Note with the specified owner.
     * @param int|null             $X       Initializes the Note with the specified x position.
     * @param int|null             $Y       Initializes the Note with the specified y position.
     * @param int|null             $Width   Initializes the Note with the specified width.
     * @param int|null             $Height  Initializes the Note with the specified height.
     * @param string|null          $Color   Initializes the Note with the specified color.
     * @param string|null          $Content Initializes the Note with the specified content.
     */
    public function __construct(
        ?int $ID = null,
        User $Owner = null,
        int $X = null,
        int $Y = null,
        int $Width = null,
        int $Height = null,
        string $Color = null,
        string $Content = null
    ) {
        parent::__construct($ID, $Owner, $X, $Y);
        $this->Width   = $Width;
        $this->Height  = $Height;
        $this->Color   = $Color;
        $this->Content = $Content;
        $this->AddProperties([
            "Width"   => [
                \Get => MappedGetter::Create(
                    $this->Width,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Width")
                              ->From(static::Table)
                ),
                \Set => MappedSetter::Create(
                    $this->Width,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->WidthChanged
                )
            ],
            "Height"  => [
                \Get => MappedGetter::Create(
                    $this->Height,
                    Type::Int,
                    true,
                    $this->ID,
                    Expression::Select("Height")
                              ->From(static::Table)
                ),
                \Set => MappedSetter::Create(
                    $this->Height,
                    Type::Int,
                    false,
                    $this->ID,
                    $this->HeightChanged
                )
            ],
            "Color"   => [
                \Get => MappedGetter::Create(
                    $this->Color,
                    Type::String,
                    false,
                    $this->ID,
                    Expression::Select("Color")
                              ->From(static::Table)
                ),
                \Set => MappedSetter::Create(
                    $this->Color,
                    Type::String,
                    false,
                    $this->ID,
                    $this->ColorChanged
                )
            ],
            "Content" => [
                \Get => MappedGetter::Create(
                    $this->Content,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Content")
                              ->From(static::Table)
                ),
                \Set => MappedSetter::Create(
                    $this->Content,
                    Type::String,
                    false,
                    $this->ID,
                    $this->ContentChanged
                )
            ]
        ]);
    }
    
    /**
     * Fills the Note with its values from the database.
     *
     * @return \vDesk\PinBoard\Note The filled Note.
     * @throws \vDesk\Data\IDNullException Thrown if the Note is virtual.
     *
     */
    public function Fill(): Note {
        if($this->ID === null) {
            throw new IDNullException("Cannot Fill Model without ID");
        }
        $Note          = Expression::Select("*")
                                   ->From(static::Table)
                                   ->Where(["ID" => $this->ID])
                                   ->Execute()
                                   ->ToMap();
        $this->Owner   = new User((int)$Note["Owner"]);
        $this->X       = (int)$Note["X"];
        $this->Y       = (int)$Note["Y"];
        $this->Width   = (int)$Note["Width"];
        $this->Height  = (int)$Note["Height"];
        $this->Color   = $Note["Color"];
        $this->Content = $Note["Content"];
        return $this;
    }
    
    /**
     * Deletes the Note.
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
        //Check if the note already exists, so update it.
        if($this->ID !== null) {
            Expression::Update(static::Table)
                      ->SetIf([
                          "X"       => [$this->XChanged => $this->X],
                          "Y"       => [$this->YChanged => $this->Y],
                          "Width"   => [$this->WidthChanged => $this->Width],
                          "Height"  => [$this->HeightChanged => $this->Height],
                          "Color"   => [$this->ColorChanged => $this->Color],
                          "Content" => [$this->ContentChanged => $this->Content]
                      ])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        } else {
            $this->ID = Expression::Insert()
                                  ->Into(static::Table)
                                  ->Values([
                                      "ID"      => null,
                                      "Owner"   => $this->Owner,
                                      "X"       => $this->X,
                                      "Y"       => $this->Y,
                                      "Width"   => $this->Width,
                                      "Height"  => $this->Height,
                                      "Color"   => $this->Color,
                                      "Content" => $this->Content
                                  ])
                                  ->ID();
        }
    }
    
    /**
     * Creates a Note from a specified data view.
     *
     * @param array $DataView The data to use to create a Note.
     *
     * @return \vDesk\PinBoard\Note A Note created from the specified data view.
     */
    public static function FromDataView($DataView): Note {
        return new static(
            $DataView["ID"] ?? null,
            null,
            $DataView["X"] ?? 0,
            $DataView["Y"] ?? 0,
            $DataView["Width"] ?? 0,
            $DataView["Height"] ?? 0,
            $DataView["Color"] ?? "",
            $DataView["Content"] ?? "",
        );
    }
    
    /**
     * Creates a data view of the Note.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Note.
     *
     * @return array The data view representing the current state of the Note.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"      => $this->ID,
                "X"       => $this->X,
                "Y"       => $this->Y,
                "Width"   => $this->Width,
                "Height"  => $this->Height,
                "Color"   => $this->Color,
                "Content" => $this->Content
            ];
    }
}
