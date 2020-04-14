<?php
declare(strict_types=1);

namespace vDesk\Modules\Module;

use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Data\IDataView;
use vDesk\Data\IDNullException;
use vDesk\Data\IManagedModel;
use vDesk\Data\IModel;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Modules\Module;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents a Command of a Module.
 *
 * @property int|null                   $ID                     Gets or sets the ID of the Command.
 * @property \vDesk\Modules\Module|null $Module                 Gets or sets the Module of the Command.
 * @property string|null                $Name                   Gets or sets the name of the Command.
 * @property bool|null                  $RequireTicket          Gets or sets a value indicating whether the Command requires a ticket.
 * @property bool|null                  $Binary                 Gets or sets a value indicating whether the command returns a binary response.
 * @property string|null                $Alias                  Gets or sets the alias of the Command.
 * @property Collection|null            $Parameters            Gets or sets the Parameters of the Command.
 *
 * @package vDesk\Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 * @version 1.0.0
 */
class Command implements IModel {
    
    use Properties;
    
    /**
     * Gets or sets the ID of the command.
     *
     * @var null|int
     */
    private ?int $ID;
    
    /**
     * The Module of the Command.
     *
     * @var \vDesk\Modules\Module|null
     */
    private ?Module $Module;
    
    /**
     * The name of the Command.
     *
     * @var string|null
     */
    private ?string $Name;
    
    /**
     * Flag indicating whether the name of the Command has been changed.
     *
     * @var bool
     */
    private bool $NameChanged = false;
    
    /**
     * Flag indicating whether the Command requires authentication via ticket.
     *
     * @var null|bool
     */
    public bool $RequireTicket;
    
    /**
     * @var bool
     */
    private bool $RequireTicketChanged = false;
    
    /**
     * Flag indicating whether to return the result of the command as binary data.
     *
     * @var bool|null
     */
    private ?bool $Binary;
    
    /**
     * Flag indicating whether the binary flag of the Command has been changed.
     *
     * @var bool
     */
    private bool $BinaryChanged = false;
    
    /**
     * The alias of the Command.
     *
     * @var string|null
     */
    private ?string $Alias;
    
    /**
     * Flag indicating whether the alias of the Command has been changed.
     *
     * @var bool
     */
    private bool $AliasChanged = false;
    
    /**
     * The Parameters of the Command.
     *
     * @var \vDesk\Struct\Collections\Observable\Collection|null
     */
    private ?Collection $Parameters;
    
    /**
     * The added Parameters of the Command.
     *
     * @var array
     */
    private array $Added = [];
    
    /**
     * The deleted Parameters of the Command.
     *
     * @var array
     */
    private array $Deleted = [];
    
    /**
     * Initializes a new instance of the Command class.
     *
     * @param int|null                                                     $ID            Initializes the Command with the specified ID.
     * @param \vDesk\Modules\Module|null                                   $Module        Initializes the Command with the specified Module.
     * @param string                                                       $Name          Initializes the Command with the specified command.
     * @param bool|null                                                    $RequireTicket Initializes the Command with the specified flag indicating whether
     *                                                                                    the Command requires a ticket.
     * @param bool|null                                                    $Binary        Initializes the Command with the specified flag indicating whether
     *                                                                                    the Command result is binary.
     * @param string|null                                                  $Alias         Initializes the Command with the specified alias.
     * @param \vDesk\Struct\Collections\Observable\Collection|null $Parameters    Initializes the Command with the specified Collection of Parameters.
     */
    public function __construct(
        ?int $ID = null,
        ?Module $Module = null,
        string $Name = null,
        bool $RequireTicket = null,
        bool $Binary = null,
        ?string $Alias = null,
        ?Collection $Parameters = null
    ) {
        $this->ID            = $ID;
        $this->Module        = $Module;
        $this->Name          = $Name;
        $this->RequireTicket = $RequireTicket;
        $this->Binary        = $Binary;
        $this->Alias         = $Alias;
        $this->Parameters   = $Parameters;
        if($Parameters !== null) {
            $this->Parameters->OnAdd[]    = function(Collection $Sender, Parameter $Parameter): void {
                if($this->ID !== null) {
                    if($Sender->Any(fn(Parameter $Existing): bool => $Existing->Name === $Parameter->Name)) {
                        throw new \InvalidArgumentException("Parameter with name '$Parameter->Name' already exists!");
                    }
                    $this->Added[] = $Parameter;
                }
                $Parameter->Command = $this;
            };
            $this->Parameters->OnDelete[] = function($Sender, Parameter $Parameter): void {
                if($this->ID !== null && $Parameter->ID !== null) {
                    $this->Deleted[] = $Parameter;
                }
            };
        }
        $this->AddProperties([
            "ID"            => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(int $Value) => $this->ID ??= $Value,
            ],
            "Module"        => [
                \Get => fn(): ?Module => $this->Module,
                \Set => fn(Module $Value) => $this->Module ??= $Value,
            ],
            "Name"          => [
                \Get => MappedGetter::Create(
                    $this->Name,
                    Type::String,
                    true,
                    $this->ID,
                    Expression::Select("Name")
                              ->From("Modules.Commands")
                ),
                \Set => MappedSetter::Create(
                    $this->Name,
                    Type::String,
                    false,
                    $this->ID,
                    $this->NameChanged
                )
            ],
            "RequireTicket" => [
                \Get => MappedGetter::Create(
                    $this->RequireTicket,
                    Type::Bool,
                    true,
                    $this->ID,
                    Expression::Select("RequireTicket")
                              ->From("Modules.Commands")
                ),
                \Set => MappedSetter::Create(
                    $this->RequireTicket,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->RequireTicketChanged
                )
            ],
            "Binary"        => [
                \Get => MappedGetter::Create(
                    $this->Binary,
                    Type::Bool,
                    true,
                    $this->ID,
                    Expression::Select("Binary")
                              ->From("Modules.Commands")
                ),
                \Set => MappedSetter::Create(
                    $this->Binary,
                    Type::Bool,
                    false,
                    $this->ID,
                    $this->BinaryChanged
                )
            ],
            "Alias"         => [
                \Get => fn(): ?string => $this->Alias,
                \Set => MappedSetter::Create(
                    $this->Alias,
                    Type::String,
                    true,
                    $this->ID,
                    $this->AliasChanged
                )
            ],
            "Parameters"    => [
                \Get => function(): Collection {
                    if($this->Parameters === null) {
                        $this->Parameters = new Collection();
                        if($this->ID !== null) {
                            foreach(
                                Expression::Select("Name", "Type", "Optional", "Nullable")
                                          ->From("Modules.Parameters")
                                          ->Where(["Command" => $this->ID])
                                as
                                $Parameter
                            ) {
                                $this->Parameters->Add(
                                    new Parameter(
                                        null,
                                        null,
                                        $Parameter["Name"],
                                        $Parameter["Type"],
                                        (bool)$Parameter["Optional"],
                                        (bool)$Parameter["Nullable"]
                                    )
                                );
                            }
                        }
                        $this->Parameters->OnAdd[]    = function(Collection $Sender, Parameter $Parameter): void {
                            if($this->ID !== null) {
                                if($Sender->Any(fn(Parameter $Existing): bool => $Existing->Name === $Parameter->Name)) {
                                    throw new \InvalidArgumentException("Parameter with name '$Parameter->Name' already exists!");
                                }
                                $this->Added[] = $Parameter;
                            }
                            $Parameter->Command = $this;
                        };
                        $this->Parameters->OnDelete[] = function($Sender, Parameter $Parameter): void {
                            if($this->ID !== null && $Parameter->ID !== null) {
                                $this->Deleted[] = $Parameter;
                            }
                        };
                    }
                    return $this->Parameters;
                }
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
    public function Fill(): IManagedModel {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $Command             = Expression::Select("*")
                                         ->From("Modules.Commands")
                                         ->Where(["ID" => $this->ID])
                                         ->Execute()
                                         ->ToMap();
        $this->Name          = $Command["Name"];
        $this->RequireTicket = (bool)$Command["RequireTicket"];
        $this->Binary        = (bool)$Command["Binary"];
        $this->Alias         = $Command["Alias"];
        foreach(
            Expression::Select("*")
                      ->From("Modules.Parameters")
                      ->Where(["Command" => $this])
            as
            $Parameter
        ) {
            $this->Parameters->Add(
                new Parameter(
                    (int)$Parameter["ID"],
                    $this,
                    $Parameter["Name"],
                    $Parameter["Type"],
                    (bool)$Parameter["Optional"],
                    (bool)$Parameter["Nullable"]
                )
            );
        }
        return $this;
    }
    
    /**
     * @inheritDoc
     */
    public function Save(): void {
        if($this->ID === null) {
            $this->ID = Expression::Insert()
                                  ->Into("Modules.Commands")
                                  ->Values([
                                      "ID"            => $this->ID,
                                      "Module"        => $this->Module,
                                      "Name"          => $this->Name,
                                      "RequireTicket" => $this->RequireTicket,
                                      "Binary"        => $this->Binary,
                                      "Alias"         => $this->Alias
                                  ])
                                  ->ID();
            foreach($this->Parameters ?? [] as $Parameter) {
                $Parameter->Command = $this;
                $Parameter->Save();
            }
        } else {
            Expression::Update("Modules.Commands")
                      ->SetIf([
                          "Name"          => [$this->NameChanged => $this->Name],
                          "RequireTicket" => [$this->RequireTicketChanged => $this->RequireTicket],
                          "Binary"        => [$this->BinaryChanged => $this->Binary],
                          "Alias"         => [$this->AliasChanged => $this->Alias],
                      ])
                      ->Where(["ID" => $this->ID])
                      ->Execute();
            foreach($this->Added as $Parameter) {
                $Parameter->Command = $this;
                $Parameter->Save();
            }
            foreach($this->Deleted as $Parameter) {
                $Parameter->Delete();
            }
        }
    }
    
    /**
     * @inheritDoc
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("Modules.Commands")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
            Expression::Delete()
                      ->From("Modules.Parameters")
                      ->Where(["Command" => $this->ID])
                      ->Execute();
        }
    }
    
    /**
     * @inheritDoc
     */
    public static function FromDataView($DataView): IDataView {
        return new static(
            $DataView["ID"] ?? null,
            null,
            $DataView["Name"] ?? null,
            $DataView["RequireTicket"] ?? null,
            $DataView["Binary"] ?? null,
            $DataView["Alias"] ?? null,
            $DataView["Parameters"] ?? [],
        );
    }
    
    /**
     * @inheritDoc
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "Name"          => $this->Name,
                "RequireTicket" => $this->RequireTicket,
                "Binary"        => $this->Binary,
                "Parameters"    => ($this->Properties["Parameters"]->Getter)()->Reduce(
                    static function(array $Entries, Parameter $Entry): array {
                        $Entries[] = $Entry->ToDataView();
                        return $Entries;
                    },
                    []
                )
            ];
    }
}