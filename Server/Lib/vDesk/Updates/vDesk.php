<?php
declare(strict_types=1);

namespace vDesk\Updates;

use vDesk\Packages\Package;

class vDesk extends Update {
    /**
     * The class name of the Package of the Update.
     */
    public const Package = \vDesk\Packages\vDesk::class;
    
    /**
     * The required Package version of the Update.
     */
    public const RequiredVersion = "1.0.0";
    
    /**
     * The description of the Update.
     */
    public const Description = <<<Description
- Fixed missing context for error logging.
- Fixed Socket::Select().
- Fixed Collections.
- Removed unused files.
- Implemented auto initialization of DataProvider- and Expression-facades.
Description;
    
    /**
     * The files and directories of the Update.
     */
    public const Files = [
        self::Deploy   => [
            Package::Server => [
                Package::Lib => [
                    "vDesk.php",
                    "vDesk/DataProvider.php",
                    "vDesk/DataProvider/Expression.php",
                    "vDesk/Environment/API.php",
                    "vDesk/IO/Socket.php",
                    "vDesk/Struct/Collections/Stack.php",
                    "vDesk/Struct/Collections/Observable/Dictionary.php"
                ]
            ]
        ],
        self::Undeploy => [
            Package::Server => [
                Package::Lib => [
                    "vDesk.php",
                    "vDesk/DataProvider.php",
                    "vDesk/DataProvider/Expression.php",
                    "vDesk/Environment/API.php",
                    "vDesk/IO/Socket.php",
                    "vDesk/Struct/Value.php",
                    "vDesk/Struct/IStructuralEquatable.php",
                    "vDesk/Struct/IStructuralComparable.php",
                    "vDesk/Struct/Collections/Stack.php",
                    "vDesk/Struct/Collections/Observable/Dictionary.php"
                ]
            ]
        ]
    ];
    
    /**
     * @inheritDoc
     */
    public static function Install(\Phar $Phar, string $Path): void {
        self::Undeploy();
        self::Deploy($Phar, $Path);
    }
}