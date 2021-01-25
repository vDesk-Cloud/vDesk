<?php
declare(strict_types=1);

namespace vDesk\Machines;

use vDesk\Data\IDNullException;
use vDesk\Data\IModel;
use vDesk\Data\Model;
use vDesk\DataProvider\Expression;
use vDesk\DataProvider\MappedGetter;
use vDesk\DataProvider\MappedSetter;
use vDesk\Security\User;
use vDesk\Struct\Properties;
use vDesk\Struct\Type;

/**
 * Represents an abstract base class for processes.
 *
 * @property int|null                  $ID                    Gets or sets the process ID of the Machine.
 * @property \vDesk\Security\User|null $Owner                 Gets or sets the owner of the Machine.
 * @property string|null               $Guid                  Gets or sets the Guid of the Machine.
 * @property \DateTime|null            $TimeStamp             Gets or sets the start time of the Machine.
 * @property string                    $Status                Gets or sets the current execution status of the Machine.
 * @package vDesk\Messenger
 * @author  Kerry Holz <DevelopmentHero@gmail.com>
 */
abstract class Machine implements IModel {
    
    use Properties;
    
    /**
     * Status representing a virtual Machine.
     */
    public const Virtual = "0";
    
    /**
     * Status representing a running Machine.
     */
    public const Running = "1";
    
    /**
     * Status representing a suspended Machine.
     */
    public const Suspended = "2";
    
    /**
     * Status representing a stopped Machine.
     */
    public const Stopped = "3";
    
    /**
     * Flag indicating whether the ID of the Machine has been changed.
     *
     * @var bool
     */
    private bool $IDChanged = false;
    
    /**
     * Flag indicating whether the timestamp of the Machine has been changed.
     *
     * @var bool
     */
    private bool $TimeStampChanged = false;
    
    /**
     * Flag indicating whether the execution status of the Machine has been changed.
     *
     * @var bool
     */
    private bool $StatusChanged = false;
    
    /**
     * The pointer of the shared memory page of the Machine.
     *
     * @var resource|null
     */
    private $Pointer;
    
    /**
     * Initializes a new instance of the Machine class.
     *
     * @param int|null                  $ID        Initializes the Machine with the specified process ID.
     * @param \vDesk\Security\User|null $Owner     Initializes the Machine with the specified owner.
     * @param string|null               $Guid      Initializes the Machine with the specified Guid.
     * @param null|int                  $TimeStamp Initializes the Machine with the specified start time.
     * @param string                    $Status    Initializes the Machine with the specified status.
     */
    public function __construct(
        protected ?int $ID = null,
        protected ?User $Owner = null,
        protected ?string $Guid = null,
        protected ?int $TimeStamp = null,
        protected string $Status = self::Virtual
    ) {
        $this->AddProperties([
            "ID"        => [
                \Get => MappedGetter::Create(
                    $this->ID,
                    Type::Int,
                    true,
                    $this->Guid,
                    Expression::Select("ID")
                              ->From("Machines.Running"),
                    "Guid"
                ),
                \Set => MappedSetter::Create(
                    $this->ID,
                    Type::Int,
                    false,
                    $this->Guid,
                    $this->IDChanged
                )
            ],
            "Owner"     => [
                \Get => MappedGetter::Create(
                    $this->Owner,
                    User::class,
                    true,
                    $this->Guid,
                    Expression::Select("Owner")
                              ->From("Machines.Running")
                ),
                \Set => fn(User $Value) => $this->Owner ??= $Value
            ],
            "Guid"      => [
                \Get => fn(): ?string => $this->Guid,
                \Set => fn(string $Value) => $this->Guid ??= $Value
            ],
            "TimeStamp" => [
                \Get => MappedGetter::Create(
                    $this->TimeStamp,
                    Type::Int,
                    true,
                    $this->TimeStamp,
                    Expression::Select("TimeStamp")
                              ->From("Machines.Running")
                ),
                \Set => MappedSetter::Create(
                    $this->TimeStamp,
                    Type::Int,
                    false,
                    $this->Guid,
                    $this->TimeStampChanged
                )
            ],
            "Status"    => [
                \Get => fn(): ?string => $this->Status,
                \Set => MappedSetter::Create(
                    $this->Status,
                    Type::String,
                    false,
                    $this->Guid,
                    $this->StatusChanged
                )
            ]
        ]);
    }
    
    /**
     * @inheritDoc
     */
    public function ID(): ?string {
        return $this->Guid;
    }
    
    /**
     * Fills the Machine with its values from the database.
     *
     * @return \vDesk\Machines\Machine The current instance for further chaining.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Machine is virtual.
     */
    public function Fill(): Machine {
        if($this->Guid === null) {
            throw new IDNullException();
        }
        $Machine         = Expression::Select("*")
                                     ->From("Machines.Running")
                                     ->Where(["Guid" => $this->Guid])
                                     ->Execute()
                                     ->ToMap();
        $this->ID        = (int)$Machine["ID"];
        $this->Owner     = new User((int)$Machine["Owner"]);
        $this->TimeStamp = (int)$Machine["TimeStamp"];
        $this->Status    = $Machine["Status"];
        return $this;
    }
    
    /**
     * Saves the values of the Machine to the database.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Machine is virtual.
     */
    public function Save(): void {
        if($this->Guid === null) {
            throw new IDNullException("Cannot save Machine without valid guid!");
        }
        if($this->ID === null) {
            Expression::Insert()
                      ->Into("Machines.Running")
                      ->Values([
                          "ID"        => $this->ID,
                          "Owner"     => $this->Owner,
                          "Guid"      => $this->Guid,
                          "TimeStamp" => $this->TimeStamp,
                          "Status"    => $this->Status,
                          "Name"      => \str_replace("vDesk\\Machines\\", "", static::class)
                      ])
                      ->Execute();
        } else {
            Expression::Update("Machines.Running")
                      ->Set([
                          "ID"        => $this->ID,
                          "TimeStamp" => $this->TimeStamp,
                          "Status"    => $this->Status
                      ])
                      ->Where(["Guid" => $this->Guid])
                      ->Execute();
        }
    }
    
    /**
     * Deletes the Machine from the database.
     */
    public function Delete(): void {
        if($this->Guid !== null) {
            Expression::Delete()
                      ->From("Machines.Running")
                      ->Where(["Guid" => $this->Guid])
                      ->Execute();
        }
    }
    
    /**
     * Starts the Machine
     */
    public function Start(): void {
    }
    
    /**
     * Runs the Machine.
     */
    abstract public function Run(): void;
    
    /**
     * Suspends the Machine.
     */
    public function Suspend(): void {
    }
    
    /**
     * Resumes the Machine.
     */
    public function Resume(): void {
    }
    
    /**
     * Stops the execution of the Machine.
     *
     * @param int $Code The exitcode of the Machine.
     *
     * @throws \vDesk\Data\IDNullException Thrown if the Machine is virtual.
     */
    public function Stop(int $Code = 0): void {
        if($this->ID === null) {
            throw new IDNullException();
        }
        $this->Delete();
        \shmop_delete(\shmop_open($this->ID, "c", 0644, 1));
        exit($Code);
    }
    
    /**
     * Creates a Machine from a specified data view.
     *
     * @param array $DataView The data to use to create a Machine.
     *
     * @return \vDesk\Machines\Machine A Machine created from the specified data view.
     */
    public static function FromDataView(mixed $DataView): Machine {
        $Class = "\\vDesk\\Machines\\{$DataView["Name"]}";
        /** @var \vDesk\Machines\Machine $Machine */
        return new $Class(
            $DataView["ID"] ?? null,
            new User($DataView["Owner"] ?? null),
            $DataView["Guid"] ?? null,
            $DataView["TimeStamp"] ?? null,
            $DataView["Status"] ?? self::Virtual
        );
    }
    
    /**
     * Creates a data view of the Machine.
     *
     * @param bool $Reference Flag indicating whether the data view should represent only a reference of the Machine.
     *
     * @return array The data view representing the current state of the Machine.
     */
    public function ToDataView(bool $Reference = false): array {
        return $Reference
            ? ["ID" => $this->ID]
            : [
                "ID"        => $this->ID,
                "Owner"     => ($this->Owner ?? new Model())->ToDataView(true),
                "Guid"      => $this->Guid,
                "TimeStamp" => $this->TimeStamp,
                "Status"    => $this->Status,
                "Name"      => \str_replace("vDesk\\Machines\\", "", static::class)
            ];
    }
}