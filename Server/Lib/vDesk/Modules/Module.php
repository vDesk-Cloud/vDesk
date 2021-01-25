<?php
declare(strict_types=1);

namespace vDesk\Modules;

use vDesk\DataProvider\Expression;
use vDesk\Data\IModel;
use \vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Properties;
use vDesk\Struct\Text;
use vDesk\Modules\Module\Command;

/**
 * Abstract baseclass for modules, will provide in future versions interoperability between stack and process based version.
 *
 * @property int|null        $ID                    Gets or sets the ID of the Module.
 * @property string|null     $Name                  Gets the name of the Module.
 * @property Collection|null $Commands              Gets or sets the Commands of the Module.
 *
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Module implements IModel {
    
    use Properties;
    
    /**
     * Flag indicating whether the Module is running remote.
     */
    public const Remote = false;
    
    /**
     * The lazy evaluated name of the Module.
     *
     * @var string
     */
    private string $Name;
    
    /**
     * The newly added Commands of the Module.
     *
     * @var \vDesk\Modules\Module\Command[]
     */
    private array $Added = [];
    
    /**
     * The deleted Commands of the Module.
     *
     * @var \vDesk\Modules\Module\Command[]
     */
    private array $Deleted = [];
    
    /**
     * Initializes a new instance of the Module class.
     *
     * @param int|null                                             $ID       Initializes the Module with the specified ID.
     * @param \vDesk\Struct\Collections\Observable\Collection|null $Commands Initializes the Module with the specified Collection of Commands.
     */
    public function __construct(protected ?int $ID = null, protected ?Collection $Commands = null) {
        $this->Name     = (string)Text::Substring(static::class, Text::LastIndexOf(static::class, "\\") + 1);
        if($Commands !== null) {
            $this->Commands->OnAdd[]    = function($Sender, Command $Command): void {
                if($this->ID !== null) {
                    if($Sender->Any(fn(Command $Existing): bool => $Existing->Name === $Command->Name)) {
                        throw new \InvalidArgumentException("Command with name '$Command->Name' already exists!");
                    }
                    $this->Added[] = $Command;
                }
                $Command->Module = $this;
            };
            $this->Commands->OnDelete[] = function($Sender, Command $Command): void {
                if($this->ID !== null && $Command->ID !== null) {
                    $this->Deleted[] = $Command;
                }
            };
        }
        
        $this->AddProperties([
            "ID"       => [
                \Get => fn(): ?int => $this->ID,
                \Set => fn(?int $Value) => $this->ID ??= $Value
            ],
            "Name"     => [
                \Get => fn(): string => $this->Name
            ],
            "Commands" => [
                \Get => function(): Collection {
                    if($this->Commands === null) {
                        $this->Commands = new Collection();
                        if($this->ID !== null) {
                            foreach(
                                Expression::Select("*")
                                          ->From("Modules.Commands")
                                          ->Where(["Module" => $this->ID])
                                as
                                $Command
                            ) {
                                $this->Commands->Add(
                                    new Command(
                                        (int)$Command["ID"],
                                        $this,
                                        $Command["Name"],
                                        (bool)$Command["RequireTicket"],
                                        (bool)$Command["Binary"],
                                        $Command["Alias"]
                                    )
                                );
                            }
                        }
                        $this->Commands->OnAdd[]    = function($Sender, Command $Command): void {
                            if($this->ID !== null) {
                                if($Sender->Any(fn(Command $Existing): bool => $Existing->Name === $Command->Name)) {
                                    throw new \InvalidArgumentException("Command with name '$Command->Name' already exists!");
                                }
                                $this->Added[] = $Command;
                            }
                            $Command->Module = $this;
                        };
                        $this->Commands->OnDelete[] = function($Sender, Command $Command): void {
                            if($this->ID !== null && $Command->ID !== null) {
                                $this->Deleted[] = $Command;
                            }
                        };
                    }
                    return $this->Commands;
                }
            ]
        ]);
    }
    
    /**
     * @inheritDoc
     */
    final public function ID() {
        return $this->ID;
    }
    
    /**
     * Fills the Module with it's values from the database.
     *
     * @return \vDesk\Modules\Module The current instance for further chaining.
     */
    final public function Fill(): Module {
        $Module         = Expression::Select("ID", "Remote")
                                    ->From("Modules.Modules")
                                    ->Where(["Name" => $this->Name])
                                    ->Execute()
                                    ->ToMap();
        $this->ID       = (int)$Module["ID"];
        $this->Commands ??= new Collection();
        foreach(
            Expression::Select("*")
                      ->From("Modules.Modules")
                      ->Where(["ID" => $this->ID])
            as
            $Command
        ) {
            $this->Commands->Add(
                new Command(
                    (int)$Command["ID"],
                    $this,
                    $Command["Name"],
                    (bool)$Command["RequireTicket"],
                    (bool)$Command["Binary"],
                    $Command["Alias"]
                )
            );
        }
        
        return $this;
    }
    
    /**
     * Saves the Module.
     */
    final public function Save(): void {
        if($this->ID === null) {
            $this->ID = Expression::Insert()
                                  ->Into("Modules.Modules")
                                  ->Values([
                                      "ID"     => null,
                                      "Name"   => $this->Name,
                                      "Remote" => false
                                  ])
                                  ->ID();
            /** @var \vDesk\Modules\Module\Command $Command */
            foreach($this->Commands ?? [] as $Command) {
                $Command->Module = $this;
                $Command->Save();
            }
        } else {
            foreach($this->Added as $Command) {
                $Command->Module = $this;
                $Command->Save();
            }
            foreach($this->Deleted as $Command) {
                $Command->Module = $this;
                $Command->Delete();
            }
        }
    }
    
    /**
     * Deletes the Module from the database.
     */
    public function Delete(): void {
        if($this->ID !== null) {
            Expression::Delete()
                      ->From("Modules.Modules")
                      ->Where(["ID" => $this->ID])
                      ->Execute();
            foreach(($this->Properties["Commands"]->Getter)() as $Command) {
                $Command->Delete();
            }
        }
    }
    
    /**
     * Creates a Module from a specified data view.
     *
     * @param mixed $DataView The data to use to create a Module.
     *
     * @return \vDesk\Modules\Module A Module created from the specified data view.
     */
    final public static function FromDataView(mixed $DataView): Module {
        // TODO: Implement FromDataView() method.
    }
    
    /**
     * Creates a data view of the Module.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference to the Module.
     *
     * @return mixed The data view representing the current state of the Module.
     */
    final public function ToDataView(bool $Reference = false) {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"       => $this->ID,
                "Name"     => $this->Name,
                //"Remote"   => static::Remote,
                "Commands" => ($this->Properties["Commands"]->Getter)()->Reduce(
                    static function(array $Commands, Command $Command): array {
                        $Commands[] = $Command->ToDataView();
                        return $Commands;
                    },
                    []
                )
            ];
    }
    
    /**
     * Gets the status information of the Module.
     *
     * @return null|array An array containing the status information of the Module; otherwise, null.
     */
    public static function Status(): ?array{
        return null;
    }
    
}
