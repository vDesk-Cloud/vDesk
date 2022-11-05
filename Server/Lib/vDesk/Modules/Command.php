<?php
declare(strict_types=1);

namespace vDesk\Modules;

use vDesk\IO\Input;
use vDesk\DataProvider\Expression;
use vDesk\Modules;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Type;
use vDesk\Struct\Extension;
use vDesk\Utils\Validate;

/**
 * Represents a Command of a Module.
 *
 * @package vDesk\Modules
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
class Command {

    /**
     * The response code for successful executed Commands.
     */
    public const Successful = 0;

    /**
     * The name of the Module of the Command.
     *
     * @var string|null
     */
    public static ?string $Module = null;

    /**
     * The name of the Command.
     *
     * @var string|null
     */
    public static ?string $Name = null;

    /**
     * The ticket of the Command.
     *
     * @var null|string
     */
    public static ?string $Ticket = null;

    /**
     * The parameters of the current called Command.
     *
     * @var iterable
     */
    public static iterable $Parameters = [];

    /**
     * Factory method that creates a new instance of the Command class holding the value passed through a specified IProvider.
     *
     * @return \vDesk\Modules\Command A Command holding the values from the specified IProvider.
     */
    public static function Parse(): self {

        static::$Module = Input::ParseCommand("Module");
        static::$Name   = Input::ParseCommand("Command");
        static::$Ticket = Input::ParseCommand("Ticket");

        $Row = Expression::Select(
            "Modules.ID",
            "Commands.ID",
            "Commands.RequireTicket",
            "Commands.Alias",
        )
                         ->From("Modules.Commands")
                         ->InnerJoin("Modules.Modules")
                         ->On(["Commands.Module" => "Modules.ID"])
                         ->Where([
                             "Modules.Name"  => static::$Module,
                             "Commands.Name" => static::$Name
                         ])
                         ->Execute()
                         ->ToMap();

        $Parameters = new Collection();
        foreach(
            Expression::Select("Name", "Type", "Optional", "Nullable")
                      ->From("Modules.Parameters")
                      ->Where(["Command" => (int)$Row["ID"]])
            as
            $Parameter
        ) {
            $Parameters->Add(
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

        //Validate parameters.
        foreach(
            $Parameters as $Parameter
        ) {
            $Value = $Parameter->Type === "file"
                ? Input::FetchFile($Parameter->Name)
                : Input::ParseParameter($Parameter->Name);

            //Check if the Parameter has been passed.
            if($Value === null && !$Parameter->Optional) {
                throw new \ArgumentCountError("Missing value for required Parameter '{$Parameter->Name}' of Command '" . static::$Module . "::" . static::$Name . "'!");
            }

            //Transform value.
            if($Value !== null) {
                switch($Parameter->Type) {
                    case Extension\Type::Date:
                    case Extension\Type::Time:
                    case Extension\Type::DateTime:
                        $Value = new \DateTime(\json_decode($Value));
                        break;
                    case Extension\Type::File:
                        break;
                    case Type::Array:
                        $Value = \json_decode($Value, true);
                        break;
                    case Type::Object:
                        $Value = \json_decode($Value);
                        break;
                    default:
                        $Value = \json_decode($Value);
                        if($Value === null) {
                            break;
                        }
                        //Check if the type is a referenced classname.
                        if(!Type::IsScalarType($Parameter->Type) && !Extension\Type::IsExtensionType($Parameter->Type) && \class_exists($Parameter->Type)) {
                            $Value = new $Parameter->Type($Value);
                        }
                        break;
                }
            }

            //Check if the parameter is nullable.
            if($Value === null && !$Parameter->Nullable) {
                throw new \InvalidArgumentException("Value of Parameter '{$Parameter->Name}' of Command '" . static::$Module . "::" . static::$Name . "' cannot be null!");
            }

            //Check if the parameter is of the correct type.
            if($Value !== null && !Validate::As($Value, $Parameter->Type)) {
                throw new \InvalidArgumentException("Type for parameter '{$Parameter->Name}' of command '" . static::$Module . "::" . static::$Name . "' must be typeof '{$Parameter->Type}' - " . \gettype($Parameter) . " given");
            }

            static::$Parameters[$Parameter->Name] = $Value;
        }

        return new static();

    }

    /**
     * Executes the Command.
     *
     * @return mixed The result of the executed Command.
     */
    public function Execute(): mixed {
        return Modules::Call(static::$Module, static::$Name);
    }
}