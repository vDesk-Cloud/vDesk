<?php
declare(strict_types=1);

namespace vDesk\Modules\Module\Command;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\Modules\Module\Command;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a parameter of a Command.
 *
 * @property int|null                    $ID          Gets or sets the ID of the Parameter.
 * @property \vDesk\Modules\Module\Command|null $Command     Gets or sets the Command of the Parameter.
 * @property string|null                 $Name        Gets or sets the name of the Parameter.
 * @property string|null                 $Type        Gets or sets the type of the Parameter.
 * @property bool|null                   $Nullable    Gets or sets a value indicating whether the Parameter is nullable.
 * @property bool|null                   $Optional    Gets or sets a value indicating whether the Parameter is optional.
 *
 * @package vDesk\Modules\Module\Command
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Parameter implements IModel {

    use Properties;

    /**
     * The ID of the Parameter.
     *
     * @var int|null
     */
    private ?int $ID;

    /**
     * The Command of the Parameter.
     *
     * @var \vDesk\Modules\Module\Command|null
     */
    private ?Command $Command;

    /**
     * The name of the Parameter.
     *
     * @var string
     */
    private ?string $Name;

    /**
     * Flag indicating whether the name of the Parameter has been changed.
     *
     * @var bool
     */
    private bool $NameChanged = false;

    /**
     * The type of the Parameter.
     *
     * @var string|null
     */
    private ?string $Type;

    /**
     * Flag indicating whether the type of the Parameter has been changed.
     *
     * @var bool
     */
    private bool $TypeChanged = false;

    /**
     * Flag indicating whether the Parameter is optional.
     *
     * @var bool|null
     */
    private ?bool $Optional;

    /**
     * Flag indicating whether the optional flag of the Parameter has been changed.
     *
     * @var bool
     */
    private bool $OptionalChanged = false;

    /**
     * Flag indicating whether the Parameter is nullable.
     *
     * @var bool|null
     */
    private ?bool $Nullable;

    /**
     * Flag indicating whether the nullable flag of the Parameter has been changed.
     *
     * @var bool
     */
    private bool $NullableChanged = false;

    /**
     * Initializes a new instance of the Parameter class.
     *
     * @param int|null                    $ID       Initializes the Parameter with the specified ID.
     * @param \vDesk\Modules\Module\Command|null $Command  Initializes the Parameter with the specified Command.
     * @param string|null                 $Name     Initializes the Parameter with the specified name.
     * @param string|null                 $Type     Initializes the Parameter with the specified type.
     * @param bool|null                   $Optional Initializes the Parameter with the specified flag indicating whether the Parameter is optional.
     * @param bool|null                   $Nullable Initializes the Parameter with the specified flag indicating whether the Parameter is nullable.
     */
    public function __construct(
        ?int $ID = null,
        Command $Command = null,
        string $Name = null,
        string $Type = null,
        bool $Optional = null,
        bool $Nullable = null
    ) {
        $this->ID       = $ID;
        $this->Command  = $Command;
        $this->Name     = $Name;
        $this->Type     = $Type;
        $this->Optional = $Optional;
        $this->Nullable = $Nullable;
        $this->AddProperties([
            "ID"       => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value,
            ],
            "Command"  => [
                \Get => MappedGetter::Create(
                    $this->Command,
                    Command::class,
                    true,
                    $this->ID,
                    Expression::Select("Command")
                              ->From("Modules.Parameters")
                ),
                \Set => fn(Command $Value) => $this->Command ??= $Value,
            ],
            "Name"     => [
                \Get => MappedGetter::Create(
                    $this->Name,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Name")
                              ->From("Modules.Parameters")
                ),
                \Set => MappedSetter::Create(
                    $this->Name,
                    Type::String,
                    false,
                    $this->ID,
                    $this->NameChanged
                )
            ],
            "Type"     => [
                \Get => MappedGetter::Create(
                    $this->Type,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Type")
                              ->From("Modules.Parameters")
                ),
                \Set => MappedSetter::Create(
                    $this->Type,
                    Type::String,
                    false,
                    $this->ID,
                    $this->TypeChanged
                )
            ],
            "Optional" => [
                \Get => MappedGetter::Create(
                    $this->Optional,
                    Type::Bool,
                    true,
                    $this->ID,
                    Expression::Select("Optional")
                              ->From("Modules.Parameters")
                ),
                \Set => MappedSetter::Create(
                    $this->Optional,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->OptionalChanged
                )
            ],
            "Nullable" => [
                \Get => MappedGetter::Create(
                    $this->Nullable,
                    Type::Bool,
                    true,
                    $this->ID,
                    Expression::Select("Nullable")
                              ->From("Modules.Parameters")
                ),
                \Set => MappedSetter::Create(
                    $this->Nullable,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->NullableChanged
                )
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
     * @inheritDoc
     * @throws \vDesk\Data\IDNullException
     */
    public function Fill(): Parameter {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Parameter      = Expression::Select("*")
                                    ->From("Modules.Parameters")
                                    ->Where(["ID" => $this->ID])
                                    ->Execute()
                                    ->ToMap();
        $this->Command  = new Command((int)$Parameter["Command"]);
        $this->Name     = $Parameter["Name"];
        $this->Type     = $Parameter["Type"];
        $this->Optional = (bool)$Parameter["Optional"];
        $this->Nullable = (bool)$Parameter["Nullable"];
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function Save(): void {
        if($this->ID === null) {
            $this->ID = Expression::Insert()
                                  ->Into("Modules.Parameters")
                                  ->Values([
                                      "ID"       => null,
                                      "Command"  => $this->Command,
                                      "Name"     => $this->Name,
                                      "Type"     => $this->Type,
                                      "Optional" => $this->Optional,
                                      "Nullable" => $this->Nullable
                                  ])
                                  ->ID();
        } else {
            Expression::Update("Modules.Parameters")
                      ->SetIf([
                          "Name"     => [$this->NameChanged => $this->Name],
                          "Type"     => [$this->TypeChanged => $this->Type],
                          "Optional" => [$this->OptionalChanged => $this->Optional],
                          "Nullable" => [$this->NullableChanged => $this->Nullable]
                      ])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }

    /**
     * @inheritDoc
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("Modules.Parameters")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
        }
    }

    /**
     * @inheritDoc
     */
    public static function FromDataView($DataView): Parameter {
        return new static(
            $DataView["ID"] ?? null,
            null,
            $DataView["Name"] ?? null,
            $DataView["Type"] ?? null,
            $DataView["Optional"] ?? null,
            $DataView["Nullable"] ?? null
        );
    }

    /**
     * @inheritDoc
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "Name"     => $this->Name,
                "Type"     => $this->Type,
                "Optional" => $this->Optional,
                "Nullable" => $this->Nullable
            ];
    }
}