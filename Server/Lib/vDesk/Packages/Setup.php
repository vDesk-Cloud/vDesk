<?php
declare(strict_types=1);

namespace vDesk\Packages;

use vDesk\Modules\Module\Command;
use vDesk\Modules\Module\Command\Parameter;
use vDesk\Struct\Collections\Observable\Collection;
use vDesk\Struct\Type;

/**
 * Setup Package manifest class.
 *
 * @package vDesk\Setup
 * @author  Kerry <DevelopmentHero@gmail.com>
 */
final class Setup extends Package {

    /**
     * The name of the Package.
     */
    public const Name = "Setup";

    /**
     * The version of the Package.
     */
    public const Version = "1.0.1";

    /**
     * The vendor of the Package.
     */
    public const Vendor = "Kerry <DevelopmentHero@gmail.com>";

    /**
     * The description of the Package.
     */
    public const Description = "Package providing functionality for creating and installing setups.";

    /**
     * The dependencies of the Package.
     */
    public const Dependencies = ["Packages" => "1.0.2"];

    /**
     * The files and directories of the Package.
     */
    public const Files = [
        self::Server => [
            self::Modules => [
                "Setup.php"
            ]
        ]
    ];

    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {

        //Install Module.
        /** @var \Modules\Setup $Setup */
        $Setup = \vDesk\Modules::Setup();
        $Setup->Commands->Add(
            new Command(
                null,
                $Setup,
                "Create",
                true,
                false,
                null,
                new Collection([
                    new Parameter(null, null, "Path", Type::String, true, true),
                    new Parameter(null, null, "Exclude", Type::Array, true, true),
                    new Parameter(null, null, "Compression", Type::Int, true, true)
                ])
            )
        );
        $Setup->Save();

        //Extract files.
        self::Deploy($Phar, $Path);

    }

    /**
     * @inheritDoc
     */
    public static function Uninstall(string $Path): void {

        //Uninstall Module.
        /** @var \Modules\Setup $Setup */
        $Setup = \vDesk\Modules::Setup();
        $Setup->Delete();

        //Delete files.
        self::Undeploy();

    }
}